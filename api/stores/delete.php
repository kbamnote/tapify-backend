<?php
/**
 * TAPIFY - Delete Store
 * POST /backend/api/stores/delete.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$input = getInput();
$id = (int)($input['id'] ?? 0);
if (!$id) sendError('Store ID required');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    $stmt = $pdo->prepare("DELETE FROM whatsapp_stores WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $userId]);

    if ($stmt->rowCount() === 0) sendError('Store not found', 404);

    sendSuccess('Store deleted successfully');
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
