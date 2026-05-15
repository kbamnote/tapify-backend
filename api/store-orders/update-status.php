<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$input = getInput();
$orderId = (int)($input['id'] ?? 0);
$status = $input['status'] ?? '';

if (!$orderId) sendError('Order ID required');
$validStatuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];
if (!in_array($status, $validStatuses)) sendError('Invalid status');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    // Verify ownership through store
    $stmt = $pdo->prepare("
        SELECT o.id FROM whatsapp_store_orders o
        JOIN whatsapp_stores s ON s.id = o.store_id
        WHERE o.id = ? AND s.user_id = ? LIMIT 1
    ");
    $stmt->execute([$orderId, $userId]);
    if (!$stmt->fetch()) sendError('Access denied', 403);

    $stmt = $pdo->prepare("UPDATE whatsapp_store_orders SET status = ?, is_read = 1 WHERE id = ?");
    $stmt->execute([$status, $orderId]);

    sendSuccess('Order status updated', ['status' => $status]);
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
