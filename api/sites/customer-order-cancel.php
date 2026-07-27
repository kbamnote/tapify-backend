<?php
/**
 * POST /api/sites/customer-order-cancel.php   (PUBLIC)   { site, token, order_id }
 *
 * A signed-in customer cancels their OWN order. The token identifies the
 * customer (site_customers); the order must belong to that customer's email on
 * this site and not already be completed. Never touches anyone else's orders.
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../builder/lib/SiteRepo.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$in      = getInput();
$slug    = strtolower(trim((string)($in['site'] ?? '')));
$token   = trim((string)($in['token'] ?? ''));
$orderId = (int)($in['order_id'] ?? 0);
if ($slug === '')   sendError('This form is not configured correctly.');
if ($token === '')  sendError('Not signed in', 401);
if ($orderId <= 0)  sendError('order_id is required');

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

    $st = $db->prepare("SELECT id, status FROM site_orders WHERE id = ? AND site_id = ? AND customer_email = ? LIMIT 1");
    $st->execute([$orderId, $siteId, $email]);
    $order = $st->fetch(PDO::FETCH_ASSOC);
    if (!$order) sendError('Order not found', 404);

    if ($order['status'] === 'completed') sendError('This order is already completed and cannot be cancelled.');
    if ($order['status'] === 'cancelled') { sendSuccess('Order already cancelled', ['id' => $orderId]); }

    $db->prepare("UPDATE site_orders SET status = 'cancelled' WHERE id = ?")->execute([$orderId]);
    sendSuccess('Order cancelled', ['id' => $orderId]);
} catch (Exception $e) {
    error_log('customer-order-cancel: ' . $e->getMessage());
    sendError('Could not cancel the order right now.', 500);
}
