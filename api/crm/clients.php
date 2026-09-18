<?php
/**
 * TAPIFY - CRM bridge: every customer, with their activity summary.
 * GET /api/crm/clients.php?after_id=0&limit=200
 * Header: X-CRM-API-Key
 *
 * Paged over ALL customers (users.role = 'user'), whatever way they signed up.
 * The Sales CRM walks every page on a timer to refresh its Customer Manager
 * dashboard, so each page costs a fixed handful of queries regardless of size.
 *
 * Keyset paging (pass back `nextAfterId`), not OFFSET: if a customer is deleted
 * while the CRM is part-way through, OFFSET would shift every later page and
 * silently skip someone.
 */
require_once __DIR__ . '/_bridge.php';
crm_bridge_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError('Only GET allowed', 405);
}

$afterId = max(0, (int)($_GET['after_id'] ?? 0));
$limit = min(500, max(1, (int)($_GET['limit'] ?? 200)));

try {
    $pdo = crm_pdo();

    $total = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();

    // SELECT * so the tracking columns are simply absent (not an error) if the
    // activity migration hasn't been run; only whitelisted fields are returned.
    $st = $pdo->prepare("SELECT * FROM users WHERE role = 'user' AND id > ? ORDER BY id LIMIT $limit");
    $st->execute([$afterId]);
    $users = $st->fetchAll();
    $ids = array_map(fn($u) => (int)$u['id'], $users);

    $usage = crm_usage_for($pdo, $ids);
    $engagement = crm_engagement_for($pdo, $ids);
    $assets = [];
    $inquiries = [];

    if ($ids) {
        $in = crm_placeholders($ids);

        $count = function (string $sql, string $key) use ($pdo, $ids, &$assets) {
            crm_section(function () use ($pdo, $ids, $sql, $key, &$assets) {
                $st = $pdo->prepare($sql);
                $st->execute($ids);
                foreach ($st as $r) {
                    $assets[(int)$r['user_id']][$key] = (int)$r['n'];
                }
            });
        };
        $count("SELECT user_id, COUNT(*) n FROM vcards WHERE user_id IN ($in) GROUP BY user_id", 'vcards');
        $count("SELECT user_id, COUNT(*) n FROM sites WHERE user_id IN ($in) GROUP BY user_id", 'sites');
        $count("SELECT user_id, COUNT(*) n FROM sites WHERE user_id IN ($in) AND published_version_id IS NOT NULL GROUP BY user_id", 'publishedSites');
        $count("SELECT user_id, COUNT(*) n FROM whatsapp_stores WHERE user_id IN ($in) GROUP BY user_id", 'stores');

        // Inquiries from the card, the website's inquiry form and its custom forms.
        $sources = [
            "SELECT v.user_id, COUNT(*) total, SUM(i.is_read = 0) unread, MAX(i.created_at) last_at
               FROM vcard_inquiries i JOIN vcards v ON v.id = i.vcard_id
              WHERE v.user_id IN ($in) GROUP BY v.user_id",
            "SELECT s.user_id, COUNT(*) total, SUM(i.is_read = 0) unread, MAX(i.created_at) last_at
               FROM site_inquiries i JOIN sites s ON s.id = i.site_id
              WHERE s.user_id IN ($in) GROUP BY s.user_id",
            "SELECT s.user_id, COUNT(*) total, SUM(f.is_read = 0) unread, MAX(f.created_at) last_at
               FROM form_submissions f JOIN sites s ON s.id = f.site_id
              WHERE s.user_id IN ($in) GROUP BY s.user_id",
        ];
        foreach ($sources as $sql) {
            crm_section(function () use ($pdo, $ids, $sql, &$inquiries) {
                $st = $pdo->prepare($sql);
                $st->execute($ids);
                foreach ($st as $r) {
                    $u = (int)$r['user_id'];
                    $cur = $inquiries[$u] ?? ['total' => 0, 'unread' => 0, 'lastAt' => null];
                    $cur['total'] += (int)$r['total'];
                    $cur['unread'] += (int)$r['unread'];
                    if ($r['last_at'] && (!$cur['lastAt'] || $r['last_at'] > $cur['lastAt'])) {
                        $cur['lastAt'] = $r['last_at'];
                    }
                    $inquiries[$u] = $cur;
                }
            });
        }
    }

    $clients = array_map(function ($u) use ($usage, $assets, $inquiries, $engagement) {
        $id = (int)$u['id'];
        $inq = $inquiries[$id] ?? ['total' => 0, 'unread' => 0, 'lastAt' => null];
        return crm_client_base($u) + [
            'assets' => [
                'vcards'         => $assets[$id]['vcards'] ?? 0,
                'sites'          => $assets[$id]['sites'] ?? 0,
                'publishedSites' => $assets[$id]['publishedSites'] ?? 0,
                'stores'         => $assets[$id]['stores'] ?? 0,
            ],
            'inquiries' => ['total' => $inq['total'], 'unread' => $inq['unread'], 'lastAt' => crm_iso($inq['lastAt'])],
            'usage' => (object)($usage[$id] ?? []),
            // What their own audience did: card opens, NFC taps, website visits,
            // review-card scans, and the taps that followed.
            'engagement' => $engagement[$id] ?? [
                'total' => crm_engagement_zero(), 'd7' => crm_engagement_zero(), 'd30' => crm_engagement_zero(),
                'people30' => 0, 'bySource' => (object)[], 'byEvent' => (object)[], 'byAsset' => (object)[],
                'lastAt' => null,
            ],
        ];
    }, $users);

    sendSuccess('OK', [
        'limit' => $limit,
        'total' => $total,
        // A short page means the end; otherwise continue from the last id.
        'nextAfterId' => count($users) === $limit ? end($ids) : null,
        'generatedAt' => gmdate('Y-m-d\TH:i:s\Z'),
        'clients' => $clients,
    ]);
} catch (Throwable $e) {
    error_log('crm/clients: ' . $e->getMessage());
    sendError('Could not load clients', 500);
}
