<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    $storeId = (int)($_GET['store_id'] ?? 0);
    $status = $_GET['status'] ?? 'all';
    $search = trim($_GET['search'] ?? '');

    // Get all orders for user's stores
    $sql = "SELECT o.*, s.store_name, s.url_alias AS store_url_alias
            FROM whatsapp_store_orders o
            JOIN whatsapp_stores s ON s.id = o.store_id
            WHERE s.user_id = ?";
    $params = [$userId];

    if ($storeId > 0) {
        $sql .= " AND o.store_id = ?";
        $params[] = $storeId;
    }

    if ($status !== 'all' && in_array($status, ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'])) {
        $sql .= " AND o.status = ?";
        $params[] = $status;
    }

    if (!empty($search)) {
        $sql .= " AND (o.customer_name LIKE ? OR o.customer_phone LIKE ?)";
        $params[] = '%' . $search . '%';
        $params[] = '%' . $search . '%';
    }

    $sql .= " ORDER BY o.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $orders = $stmt->fetchAll();

    // Decode items JSON
    foreach ($orders as &$order) {
        $order['items'] = json_decode($order['items'], true) ?: [];
        $order['subtotal'] = (float)$order['subtotal'];
        $order['delivery_charge'] = (float)$order['delivery_charge'];
        $order['total_amount'] = (float)$order['total_amount'];
        $order['is_read'] = (bool)$order['is_read'];
        $order['created_at_formatted'] = date('d M Y, h:i A', strtotime($order['created_at']));
    }

    // Get count of unread orders
    $stmt = $pdo->prepare("
        SELECT COUNT(*) AS unread
        FROM whatsapp_store_orders o
        JOIN whatsapp_stores s ON s.id = o.store_id
        WHERE s.user_id = ? AND o.is_read = 0
    ");
    $stmt->execute([$userId]);
    $unread = (int)$stmt->fetch()['unread'];

    sendSuccess('Orders loaded', [
        'orders' => $orders,
        'total' => count($orders),
        'unread_count' => $unread
    ]);
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
