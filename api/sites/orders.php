<?php
/**
 * Orders for the logged-in customer's builder sites — powers the dashboard's
 * "Orders" page.
 *
 *   GET  /api/sites/orders.php                 -> all orders across my sites
 *   GET  /api/sites/orders.php?site_id=12      -> one site's orders
 *   GET  /api/sites/orders.php?status=new      -> filter by status
 *   POST /api/sites/orders.php {id, status}    -> update an order's status
 *
 * Owner-only: every query is scoped to sites owned by the current user (staff
 * and admins may see all), so one customer can never read another's orders.
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../builder/lib/SiteRepo.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if (!isLoggedIn()) sendError('Not logged in', 401);

$userId = getCurrentUserId();
$staff  = isStaffOrAdmin();
$STATUSES = ['new', 'confirmed', 'completed', 'cancelled'];

try {
    $db = getDB();

    /* ----------------------------------------------------- update status */
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $in     = getInput();
        $id     = (int)($in['id'] ?? 0);
        $status = trim((string)($in['status'] ?? ''));
        if ($id <= 0) sendError('id is required');
        if (!in_array($status, $STATUSES, true)) sendError('Invalid status');

        // Confirm the order belongs to a site this user owns (and grab the
        // customer's details so we can WhatsApp them the status change).
        $st = $db->prepare(
            "SELECT o.id, o.customer_name, o.customer_phone, o.item_title
               FROM site_orders o JOIN sites s ON s.id = o.site_id
              WHERE o.id = ?" . ($staff ? '' : ' AND s.user_id = ?')
        );
        $st->execute($staff ? [$id] : [$id, $userId]);
        $order = $st->fetch(PDO::FETCH_ASSOC);
        if (!$order) sendError('Order not found', 404);

        $up = $db->prepare("UPDATE site_orders SET status = ? WHERE id = ?");
        $up->execute([$status, $id]);

        // === WhatsApp status update to the customer (silent failure) ===
        // Uses the approved 'order_status_update' template. Skipped for 'new'
        // (that's the initial state, already covered by the order confirmation).
        if ($status !== 'new' && !empty($order['customer_phone'])) {
            try {
                require_once __DIR__ . '/../../includes/whatsapp-helper.php';
                $labels = ['confirmed' => 'confirmed', 'completed' => 'completed', 'cancelled' => 'cancelled'];
                sendWhatsAppTemplate(
                    $order['customer_phone'],
                    'order_status_update',
                    [($order['customer_name'] ?: 'Customer'), '#' . $id, ($labels[$status] ?? $status)]
                );
            } catch (Exception $e) {
                error_log('site order status WhatsApp failed: ' . $e->getMessage());
            }
        }

        sendSuccess('Order updated');
    }

    /* ------------------------------------------------------------- list */
    $where  = [];
    $params = [];
    if (!$staff) { $where[] = 's.user_id = ?'; $params[] = $userId; }

    if (!empty($_GET['site_id'])) { $where[] = 'o.site_id = ?'; $params[] = (int)$_GET['site_id']; }
    if (!empty($_GET['status']) && in_array($_GET['status'], $STATUSES, true)) {
        $where[] = 'o.status = ?';
        $params[] = $_GET['status'];
    }
    $sql = "SELECT o.*, s.slug AS site_slug, s.name AS site_name
              FROM site_orders o JOIN sites s ON s.id = o.site_id"
         . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
         . ' ORDER BY o.created_at DESC LIMIT 500';

    $st = $db->prepare($sql);
    $st->execute($params);
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);

    // Small summary so the page can show counts without a second request.
    $counts = ['all' => 0, 'new' => 0, 'confirmed' => 0, 'completed' => 0, 'cancelled' => 0];
    foreach ($rows as $r) {
        $counts['all']++;
        if (isset($counts[$r['status']])) $counts[$r['status']]++;
    }

    sendSuccess('OK', ['orders' => $rows, 'counts' => $counts]);
} catch (Exception $e) {
    error_log('orders: ' . $e->getMessage());
    sendError('Could not load orders.', 500);
}
