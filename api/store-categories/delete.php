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

    $stmt = $pdo->prepare("SELECT id FROM whatsapp_stores WHERE id = ? AND user_id = ? LIMIT 1");
    $stmt->execute([$storeId, $userId]);
    if (!$stmt->fetch()) sendError('Access denied', 403);

    // Products keep but category becomes NULL (handled by DB)
    $stmt = $pdo->prepare("DELETE FROM whatsapp_store_categories WHERE id = ? AND store_id = ?");
    $stmt->execute([$id, $storeId]);

    if ($stmt->rowCount() === 0) sendError('Not found', 404);
    sendSuccess('Category deleted');
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
