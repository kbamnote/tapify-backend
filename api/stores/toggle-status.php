<?php
/**
 * TAPIFY - Toggle Store Status
 * POST /backend/api/stores/toggle-status.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$input = getInput();
$id = (int)($input['id'] ?? 0);
$status = isset($input['status']) ? (int)(bool)$input['status'] : null;

if (!$id) sendError('Store ID required');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    if ($status === null) {
        // Toggle current status
        $stmt = $pdo->prepare("UPDATE whatsapp_stores SET status = NOT status WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
    } else {
        $stmt = $pdo->prepare("UPDATE whatsapp_stores SET status = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$status, $id, $userId]);
    }

    if ($stmt->rowCount() === 0) sendError('Store not found', 404);

    sendSuccess('Status updated');
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
