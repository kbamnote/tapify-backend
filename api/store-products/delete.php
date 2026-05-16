<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$input = getInput();
$id = (int)($input['id'] ?? 0);
$storeId = (int)($input['store_id'] ?? 0);

if (!$id || !$storeId) sendError('IDs required');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    if (isAdmin()) {
        $stmt = $pdo->prepare("SELECT id FROM whatsapp_stores WHERE id = ? LIMIT 1");
        $stmt->execute([$storeId]);
    } else {
        $stmt = $pdo->prepare("SELECT id FROM whatsapp_stores WHERE id = ? AND user_id = ? LIMIT 1");
        $stmt->execute([$storeId, $userId]);
    }
    if (!$stmt->fetch()) sendError('Access denied', 403);

    // Get image to delete
    $stmt = $pdo->prepare("SELECT image FROM whatsapp_store_products WHERE id = ? AND store_id = ?");
    $stmt->execute([$id, $storeId]);
    $row = $stmt->fetch();

    if (!$row) sendError('Product not found', 404);

    // Delete from DB
    $stmt = $pdo->prepare("DELETE FROM whatsapp_store_products WHERE id = ? AND store_id = ?");
    $stmt->execute([$id, $storeId]);

    // Delete image file
    if (!empty($row['image'])) {
        $fullPath = __DIR__ . '/../../../' . $row['image'];
        if (file_exists($fullPath) && is_file($fullPath)) @unlink($fullPath);
    }

    sendSuccess('Product deleted');
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
