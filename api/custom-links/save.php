<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$input = getInput();
$vcardId = (int)($input['vcard_id'] ?? 0);
$linkId = (int)($input['id'] ?? 0);
$label = sanitize($input['label'] ?? '');
$url = trim($input['url'] ?? '');
$icon = sanitize($input['icon'] ?? 'fa-link');

if (!$vcardId) sendError('vCard ID required');
if (empty($label)) sendError('Label is required');
if (empty($url)) sendError('URL is required');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    $stmt = $pdo->prepare("SELECT id FROM vcards WHERE id = ? AND user_id = ? LIMIT 1");
    $stmt->execute([$vcardId, $userId]);
    if (!$stmt->fetch()) sendError('Access denied', 403);

    if ($linkId > 0) {
        $stmt = $pdo->prepare("UPDATE vcard_custom_links SET label = ?, url = ?, icon = ? WHERE id = ? AND vcard_id = ?");
        $stmt->execute([$label, $url, $icon, $linkId, $vcardId]);
        sendSuccess('Link updated', ['id' => $linkId]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO vcard_custom_links (vcard_id, label, url, icon) VALUES (?, ?, ?, ?)");
        $stmt->execute([$vcardId, $label, $url, $icon]);
        sendSuccess('Link added', ['id' => $pdo->lastInsertId()]);
    }
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
