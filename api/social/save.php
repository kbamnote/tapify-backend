<?php
/**
 * TAPIFY - Social Links Bulk Save
 * POST /backend/api/social/save.php
 * Body: { vcard_id, links: [{ platform, url }, ...] }
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Only POST allowed', 405);
}

$input = getInput();
$vcardId = (int)($input['vcard_id'] ?? 0);
$links = $input['links'] ?? [];

if (!$vcardId) sendError('vCard ID required');
if (!is_array($links)) sendError('Links must be array');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    // Verify ownership
    $stmt = $pdo->prepare("SELECT id FROM vcards WHERE id = ? AND user_id = ? LIMIT 1");
    $stmt->execute([$vcardId, $userId]);
    if (!$stmt->fetch()) sendError('Access denied', 403);

    // Delete all existing - then re-insert (simpler for bulk)
    $pdo->prepare("DELETE FROM vcard_social_links WHERE vcard_id = ?")->execute([$vcardId]);

    $insertStmt = $pdo->prepare("INSERT INTO vcard_social_links (vcard_id, platform, url, display_order) VALUES (?, ?, ?, ?)");
    $count = 0;
    foreach ($links as $i => $link) {
        $platform = sanitize($link['platform'] ?? '');
        $url = trim($link['url'] ?? '');
        if (!empty($platform) && !empty($url)) {
            $insertStmt->execute([$vcardId, $platform, $url, $i]);
            $count++;
        }
    }

    sendSuccess("$count social link(s) saved");
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
