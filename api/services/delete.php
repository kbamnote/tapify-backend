<?php
/**
 * TAPIFY - Services Delete API
 * POST /backend/api/services/delete.php
 * Body: { id, vcard_id }
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Only POST method allowed', 405);
}

$input = getInput();
$serviceId = (int)($input['id'] ?? 0);
$vcardId = (int)($input['vcard_id'] ?? 0);

if (!$serviceId || !$vcardId) sendError('Service ID and vCard ID required');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    // Verify vCard ownership
    $stmt = $pdo->prepare("SELECT id FROM vcards WHERE id = ? AND user_id = ? LIMIT 1");
    $stmt->execute([$vcardId, $userId]);
    if (!$stmt->fetch()) sendError('Access denied', 403);

    // Delete service
    $stmt = $pdo->prepare("DELETE FROM vcard_services WHERE id = ? AND vcard_id = ?");
    $stmt->execute([$serviceId, $vcardId]);

    if ($stmt->rowCount() === 0) sendError('Service not found', 404);

    sendSuccess('Service deleted');

} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
