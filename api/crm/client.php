<?php
/**
 * TAPIFY - CRM bridge: one customer in full.
 * GET /api/crm/client.php?user_id=123
 * Header: X-CRM-API-Key
 *
 * Everything a Customer Manager needs on the profile page: the account, app
 * install, what they've set up (cards, websites, stores and their links), plan,
 * per-feature usage, and what their Tapify presence is producing for them
 * (inquiries, appointments, orders).
 */
require_once __DIR__ . '/_bridge.php';
crm_bridge_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError('Only GET allowed', 405);
}
$userId = (int)($_GET['user_id'] ?? 0);
if ($userId <= 0) {
    sendError('user_id is required', 400);
}

try {
    $pdo = crm_pdo();

    $st = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'user'");
    $st->execute([$userId]);
    $u = $st->fetch();
    if (!$u) {
        sendError('Customer not found', 404);
    }

    $rows = function (string $sql, array $params = []) use ($pdo): array {
        $st = $pdo->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    };

    $vcards = crm_section(fn() => array_map(fn($v) => [
        'id' => (int)$v['id'],
        'name' => $v['vcard_name'],
        'alias' => $v['url_alias'],
        'url' => public_card_url($v['url_alias']),
        'active' => (int)$v['status'] === 1,
        'views' => (int)$v['view_count'],
        'createdAt' => crm_iso($v['created_at']),
    ], $rows('SELECT id, vcard_name, url_alias, status, view_count, created_at FROM vcards WHERE user_id = ? ORDER BY id', [$userId])), []);

    // View counts come from a separate table, fetched on their own so the list
    // of websites still shows if that table is missing.
    $siteViews = crm_section(function () use ($rows, $userId) {
        $out = [];
        foreach ($rows(
            'SELECT v.site_id, SUM(v.views) AS views FROM site_views v JOIN sites s ON s.id = v.site_id
              WHERE s.user_id = ? AND v.view_date >= UTC_DATE() - INTERVAL 30 DAY GROUP BY v.site_id',
            [$userId]
        ) as $r) {
            $out[(int)$r['site_id']] = (int)$r['views'];
        }
        return $out;
    }, []);

    $sites = crm_section(fn() => array_map(fn($s) => [
        'id' => (int)$s['id'],
        'name' => $s['name'],
        'slug' => $s['slug'],
        'url' => public_card_url($s['slug']),
        'status' => $s['status'],
        'published' => $s['published_version_id'] !== null,
        'publishedAt' => crm_iso($s['published_at']),
        'views30d' => $siteViews[(int)$s['id']] ?? null,
        'createdAt' => crm_iso($s['created_at']),
    ], $rows(
        'SELECT id, name, slug, status, published_version_id, published_at, created_at FROM sites WHERE user_id = ? ORDER BY id',
        [$userId]
    )), []);

    $stores = crm_section(fn() => array_map(fn($s) => [
        'id' => (int)$s['id'],
        'name' => $s['store_name'],
        'alias' => $s['url_alias'],
        'url' => public_card_url($s['url_alias']),
        'active' => (int)$s['status'] === 1,
        'views' => (int)$s['view_count'],
        'orders' => (int)$s['order_count'],
        'createdAt' => crm_iso($s['created_at']),
    ], $rows('SELECT id, store_name, url_alias, status, view_count, order_count, created_at FROM whatsapp_stores WHERE user_id = ? ORDER BY id', [$userId])), []);

    $subscription = crm_section(function () use ($rows, $userId) {
        $r = $rows('SELECT plan_name, status, price, subscribed_date, expiry_date FROM subscriptions WHERE user_id = ? ORDER BY id DESC LIMIT 1', [$userId]);
        return $r ? [
            'plan' => $r[0]['plan_name'],
            'status' => $r[0]['status'],
            'price' => $r[0]['price'] !== null ? (float)$r[0]['price'] : null,
            'subscribedOn' => $r[0]['subscribed_date'],
            'expiresOn' => $r[0]['expiry_date'],
        ] : null;
    });

    $titanium = crm_section(function () use ($rows, $userId) {
        $r = $rows('SELECT is_active, expiry_date FROM titanium_members WHERE user_id = ? LIMIT 1', [$userId]);
        return $r ? ['active' => (int)$r[0]['is_active'] === 1, 'expiry' => $r[0]['expiry_date']] : null;
    });

    // What their Tapify presence is producing: total and last 30 days, by source.
    $results = [];
    $tally = function (string $key, string $sql) use ($pdo, $userId, &$results) {
        crm_section(function () use ($pdo, $userId, $sql, $key, &$results) {
            $st = $pdo->prepare($sql);
            $st->execute([$userId]);
            $r = $st->fetch();
            $results[$key] = [
                'total' => (int)($r['total'] ?? 0),
                'last30d' => (int)($r['last30d'] ?? 0),
                'lastAt' => crm_iso($r['last_at'] ?? null),
            ];
        });
    };
    $window = "UTC_TIMESTAMP() - INTERVAL 30 DAY";
    $tally('cardInquiries', "SELECT COUNT(*) total, SUM(i.created_at >= $window) last30d, MAX(i.created_at) last_at
                               FROM vcard_inquiries i JOIN vcards v ON v.id = i.vcard_id WHERE v.user_id = ?");
    $tally('websiteInquiries', "SELECT COUNT(*) total, SUM(i.created_at >= $window) last30d, MAX(i.created_at) last_at
                                  FROM site_inquiries i JOIN sites s ON s.id = i.site_id WHERE s.user_id = ?");
    $tally('websiteForms', "SELECT COUNT(*) total, SUM(f.created_at >= $window) last30d, MAX(f.created_at) last_at
                              FROM form_submissions f JOIN sites s ON s.id = f.site_id WHERE s.user_id = ?");
    $tally('cardAppointments', "SELECT COUNT(*) total, SUM(a.created_at >= $window) last30d, MAX(a.created_at) last_at
                                  FROM vcard_appointments a JOIN vcards v ON v.id = a.vcard_id WHERE v.user_id = ?");
    $tally('websiteAppointments', "SELECT COUNT(*) total, SUM(a.created_at >= $window) last30d, MAX(a.created_at) last_at
                                     FROM site_appointments a JOIN sites s ON s.id = a.site_id WHERE s.user_id = ?");
    $tally('storeOrders', "SELECT COUNT(*) total, SUM(o.created_at >= $window) last30d, MAX(o.created_at) last_at
                             FROM whatsapp_store_orders o JOIN whatsapp_stores s ON s.id = o.store_id WHERE s.user_id = ?");
    $tally('websiteOrders', "SELECT COUNT(*) total, SUM(o.created_at >= $window) last30d, MAX(o.created_at) last_at
                               FROM site_orders o JOIN sites s ON s.id = o.site_id WHERE s.user_id = ?");

    sendSuccess('OK', crm_client_base($u) + [
        'vcards' => $vcards,
        'sites' => $sites,
        'stores' => $stores,
        'subscription' => $subscription,
        'titanium' => $titanium,
        'results' => (object)$results,
        'usage' => (object)(crm_usage_for($pdo, [$userId])[$userId] ?? []),
    ]);
} catch (Throwable $e) {
    error_log('crm/client: ' . $e->getMessage());
    sendError('Could not load the customer', 500);
}
