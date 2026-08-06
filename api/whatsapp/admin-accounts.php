<?php
/**
 * GET /api/whatsapp/admin-accounts.php   (ADMIN ONLY)
 *
 * Every connected WhatsApp number across all customers, for Tapify staff:
 * who is connected, whether it still works, and how much traffic it carries.
 *
 * The CRM endpoint behind this is authenticated by the service key ALONE — it
 * has no customer scoping, by design, because it looks across customers. That
 * makes requireAdmin() here the only thing standing between a logged-in
 * customer and every other customer's account list, so it must come first.
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'GET') sendError('Method not allowed', 405);

// Admin only — this is the guard for a cross-customer endpoint.
requireAdmin();

if (CRM_SERVICE_KEY === '' || CRM_SERVICE_URL === '') {
    sendError('The WhatsApp service is not configured.', 503);
}

$ch = curl_init(rtrim(CRM_SERVICE_URL, '/') . '/api/partner-admin/whatsapp/accounts');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 20,
    CURLOPT_CONNECTTIMEOUT => 5,
    CURLOPT_HTTPHEADER     => [
        'Accept: application/json',
        'X-Service-Key: ' . CRM_SERVICE_KEY,
    ],
]);
$raw    = curl_exec($ch);
$status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err    = curl_error($ch);
curl_close($ch);

if ($err !== '') {
    error_log('whatsapp admin-accounts: ' . $err);
    sendError('Could not reach the WhatsApp service.', 502);
}
if ($status >= 400) {
    $d = json_decode($raw, true) ?: [];
    sendError($d['error'] ?? 'The WhatsApp service returned an error.', $status);
}

$data = json_decode($raw, true) ?: ['accounts' => [], 'totals' => []];

// Attach the Tapify-side identity. The CRM only knows a users.id integer; staff
// need the name and email to actually help someone.
try {
    $ids = array_values(array_filter(array_map(
        static fn($a) => (int)($a['tapifyUserId'] ?? 0),
        $data['accounts'] ?? []
    )));
    if ($ids) {
        $in = implode(',', array_fill(0, count($ids), '?'));
        $st = getDB()->prepare("SELECT id, name, email FROM users WHERE id IN ($in)");
        $st->execute($ids);
        $users = [];
        foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $u) $users[(int)$u['id']] = $u;

        foreach ($data['accounts'] as &$a) {
            $u = $users[(int)($a['tapifyUserId'] ?? 0)] ?? null;
            $a['customerName']  = $u['name']  ?? null;
            $a['customerEmail'] = $u['email'] ?? null;
        }
        unset($a);
    }
} catch (Exception $e) {
    // Cosmetic enrichment only — never fail the page over it.
    error_log('whatsapp admin-accounts: user lookup failed: ' . $e->getMessage());
}

sendSuccess('OK', $data);
