<?php
/**
 * TAPIFY - CRM bridge: every inquiry a customer has received, in full.
 * GET /api/crm/client-inquiries.php?user_id=123&limit=100
 * Header: X-CRM-API-Key
 *
 * Merges the three places a lead can arrive — the digital card's inquiry form,
 * the website's inquiry form and the website's custom forms — into one list,
 * newest first. Full contact details and messages are included, by decision:
 * the Sales CRM limits them to the customer's assigned manager and admin.
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
$limit = min(500, max(1, (int)($_GET['limit'] ?? 100)));

try {
    $pdo = crm_pdo();
    $all = [];

    $fetch = function (string $sql) use ($pdo, $userId, $limit): array {
        $st = $pdo->prepare($sql . " ORDER BY created_at DESC LIMIT $limit");
        $st->execute([$userId]);
        return $st->fetchAll();
    };

    crm_section(function () use ($fetch, &$all) {
        foreach ($fetch(
            'SELECT i.id, i.name, i.email, i.phone, i.message, i.is_read, i.created_at, v.vcard_name AS source_name
               FROM vcard_inquiries i JOIN vcards v ON v.id = i.vcard_id WHERE v.user_id = ?'
        ) as $r) {
            $all[] = [
                'source' => 'card', 'sourceName' => $r['source_name'], 'id' => (int)$r['id'],
                'name' => $r['name'], 'email' => $r['email'], 'phone' => $r['phone'],
                'subject' => null, 'message' => $r['message'], 'fields' => null, 'page' => null,
                'read' => (int)$r['is_read'] === 1, 'at' => crm_iso($r['created_at']),
            ];
        }
    });

    crm_section(function () use ($fetch, &$all) {
        foreach ($fetch(
            'SELECT i.id, i.name, i.email, i.phone, i.subject, i.message, i.page_url, i.is_read, i.created_at, s.name AS source_name
               FROM site_inquiries i JOIN sites s ON s.id = i.site_id WHERE s.user_id = ?'
        ) as $r) {
            $all[] = [
                'source' => 'website', 'sourceName' => $r['source_name'], 'id' => (int)$r['id'],
                'name' => $r['name'], 'email' => $r['email'], 'phone' => $r['phone'],
                'subject' => $r['subject'], 'message' => $r['message'], 'fields' => null, 'page' => $r['page_url'],
                'read' => (int)$r['is_read'] === 1, 'at' => crm_iso($r['created_at']),
            ];
        }
    });

    crm_section(function () use ($fetch, &$all) {
        foreach ($fetch(
            'SELECT f.id, f.form_id, f.data, f.page_slug, f.is_read, f.created_at, s.name AS source_name
               FROM form_submissions f JOIN sites s ON s.id = f.site_id WHERE s.user_id = ?'
        ) as $r) {
            // Custom forms store whatever fields the site owner built, as JSON.
            $fields = json_decode((string)$r['data'], true);
            $fields = is_array($fields) ? $fields : ['raw' => (string)$r['data']];
            $pick = function (array $keys) use ($fields) {
                foreach ($fields as $k => $v) {
                    if (is_scalar($v) && in_array(strtolower((string)$k), $keys, true)) {
                        return (string)$v;
                    }
                }
                return null;
            };
            $all[] = [
                'source' => 'website_form', 'sourceName' => $r['source_name'], 'id' => (int)$r['id'],
                'name' => $pick(['name', 'full_name', 'fullname']),
                'email' => $pick(['email', 'email_address']),
                'phone' => $pick(['phone', 'mobile', 'phone_number', 'whatsapp']),
                'subject' => $r['form_id'], 'message' => $pick(['message', 'msg', 'comments', 'details']),
                'fields' => $fields, 'page' => $r['page_slug'],
                'read' => (int)$r['is_read'] === 1, 'at' => crm_iso($r['created_at']),
            ];
        }
    });

    usort($all, fn($a, $b) => strcmp((string)$b['at'], (string)$a['at']));

    sendSuccess('OK', ['inquiries' => array_slice($all, 0, $limit)]);
} catch (Throwable $e) {
    error_log('crm/client-inquiries: ' . $e->getMessage());
    sendError('Could not load inquiries', 500);
}
