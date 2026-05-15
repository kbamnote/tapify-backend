<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$input = getInput();
$id = (int)($input['id'] ?? 0);
if (!$id) sendError('Order ID required');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    $stmt = $pdo->prepare("
        SELECT o.id FROM whatsapp_store_orders o
        JOIN whatsapp_stores s ON s.id = o.store_id
        WHERE o.id = ? AND s.user_id = ? LIMIT 1
    ");
    $stmt->execute([$id, $userId]);
    if (!$stmt->fetch()) sendError('Access denied', 403);

    $stmt = $pdo->prepare("DELETE FROM whatsapp_store_orders WHERE id = ?");
    $stmt->execute([$id]);

    sendSuccess('Order deleted');
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
