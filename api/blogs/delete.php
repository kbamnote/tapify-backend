<?php
/**
 * TAPIFY - Blogs Delete
 * POST /backend/api/blogs/delete.php
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();
blockStaffDelete(); // card-editor accounts may not delete

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$input = getInput();
$blogId = (int)($input['id'] ?? 0);
$vcardId = (int)($input['vcard_id'] ?? 0);

if (!$blogId || !$vcardId) sendError('IDs required');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    if (!userCanEditVcard($pdo, $vcardId)) sendError('Access denied', 403);

    $stmt = $pdo->prepare("DELETE FROM vcard_blogs WHERE id = ? AND vcard_id = ?");
    $stmt->execute([$blogId, $vcardId]);

    if ($stmt->rowCount() === 0) sendError('Not found', 404);
    sendSuccess('Blog deleted');
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
