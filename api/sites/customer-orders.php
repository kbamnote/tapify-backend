<?php
/**
 * POST /api/sites/customer-orders.php   (PUBLIC)   { site, token }
 *
 * A signed-in customer's own order history on a published builder site. The
 * token identifies the customer (from site_customers); orders are matched by
 * that customer's email. Never exposes anyone else's orders.
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../builder/lib/SiteRepo.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$in    = getInput();
$slug  = strtolower(trim((string)($in['site'] ?? '')));
$token = trim((string)($in['token'] ?? ''));
if ($slug === '') sendError('This form is not configured correctly.');
if ($token === '') sendError('Not signed in', 401);

try {
    $site = SiteRepo::findBySlug($slug);
    if (!$site) sendError('This website is not available.', 404);
    $siteId = (int)$site['id'];
    $db = getDB();

    if (!$db->query("SHOW TABLES LIKE 'site_customers'")->fetchColumn()) sendError('Not signed in', 401);

    $cs = $db->prepare("SELECT email FROM site_customers WHERE site_id = ? AND token = ? LIMIT 1");
    $cs->execute([$siteId, $token]);
    $email = $cs->fetchColumn();
    if (!$email) sendError('Session expired. Please sign in again.', 401);

    if (!$db->query("SHOW TABLES LIKE 'site_orders'")->fetchColumn()) {
        sendSuccess('OK', ['orders' => []]);
    }

    $st = $db->prepare(
        "SELECT id, item_title, price, mrp, option_label, option_value, quantity, status, note, created_at
           FROM site_orders
          WHERE site_id = ? AND customer_email = ?
          ORDER BY created_at DESC LIMIT 200"
    );
    $st->execute([$siteId, $email]);
    sendSuccess('OK', ['orders' => $st->fetchAll(PDO::FETCH_ASSOC)]);
} catch (Exception $e) {
    error_log('customer-orders: ' . $e->getMessage());
    sendError('Could not load your orders.', 500);
}
