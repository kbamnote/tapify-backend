<?php
/**
 * TAPIFY - Products Delete API
 * POST /backend/api/products/delete.php
 * Body: { id, vcard_id }
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Only POST method allowed', 405);
}

$input = getInput();
$productId = (int)($input['id'] ?? 0);
$vcardId = (int)($input['vcard_id'] ?? 0);

if (!$productId || !$vcardId) sendError('Product ID and vCard ID required');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    // Verify vCard ownership
    $stmt = $pdo->prepare("SELECT id FROM vcards WHERE id = ? AND user_id = ? LIMIT 1");
    $stmt->execute([$vcardId, $userId]);
    if (!$stmt->fetch()) sendError('Access denied', 403);

    // Delete
    $stmt = $pdo->prepare("DELETE FROM vcard_products WHERE id = ? AND vcard_id = ?");
    $stmt->execute([$productId, $vcardId]);

    if ($stmt->rowCount() === 0) sendError('Product not found', 404);

    sendSuccess('Product deleted');

} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
