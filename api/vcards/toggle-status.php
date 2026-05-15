<?php
/**
 * TAPIFY - Toggle vCard Status API
 * POST /backend/api/vcards/toggle-status.php
 * Body: { id, status (0 or 1) }
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Only POST method allowed', 405);
}

$input = getInput();
$vcardId = (int)($input['id'] ?? 0);
$status = (int)($input['status'] ?? 0);

if (!$vcardId) {
    sendError('vCard ID is required');
}

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    $stmt = $pdo->prepare("UPDATE vcards SET status = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$status ? 1 : 0, $vcardId, $userId]);

    if ($stmt->rowCount() === 0) {
        sendError('vCard not found', 404);
    }

    sendSuccess('Status updated', [
        'status' => (bool)$status
    ]);

} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
