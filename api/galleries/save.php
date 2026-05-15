<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$input = getInput();
$vcardId = (int)($input['vcard_id'] ?? 0);
$galleryId = (int)($input['id'] ?? 0);
$name = sanitize($input['name'] ?? '');

if (!$vcardId) sendError('vCard ID required');
if (empty($name)) sendError('Gallery name is required');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    $stmt = $pdo->prepare("SELECT id FROM vcards WHERE id = ? AND user_id = ? LIMIT 1");
    $stmt->execute([$vcardId, $userId]);
    if (!$stmt->fetch()) sendError('Access denied', 403);

    if ($galleryId > 0) {
        $stmt = $pdo->prepare("UPDATE vcard_galleries SET name = ? WHERE id = ? AND vcard_id = ?");
        $stmt->execute([$name, $galleryId, $vcardId]);
        sendSuccess('Gallery updated', ['id' => $galleryId]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO vcard_galleries (vcard_id, name) VALUES (?, ?)");
        $stmt->execute([$vcardId, $name]);
        sendSuccess('Gallery added', ['id' => $pdo->lastInsertId()]);
    }
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
