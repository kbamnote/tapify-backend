<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$input = getInput();
$id = (int)($input['id'] ?? 0);
$vcardId = (int)($input['vcard_id'] ?? 0);

if (!$id || !$vcardId) sendError('IDs required');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    $stmt = $pdo->prepare("SELECT id FROM vcards WHERE id = ? AND user_id = ? LIMIT 1");
    $stmt->execute([$vcardId, $userId]);
    if (!$stmt->fetch()) sendError('Access denied', 403);

    $stmt = $pdo->prepare("DELETE FROM vcard_custom_links WHERE id = ? AND vcard_id = ?");
    $stmt->execute([$id, $vcardId]);

    if ($stmt->rowCount() === 0) sendError('Not found', 404);
    sendSuccess('Link deleted');
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
