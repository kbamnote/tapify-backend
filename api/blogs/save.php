<?php
/**
 * TAPIFY - Blogs Save
 * POST /backend/api/blogs/save.php
 * Body: { id (optional), vcard_id, title, content, image, published_date }
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$input = getInput();
$vcardId = (int)($input['vcard_id'] ?? 0);
$blogId = (int)($input['id'] ?? 0);
$title = sanitize($input['title'] ?? '');
$content = $input['content'] ?? '';
$image = sanitize($input['image'] ?? '');
$publishedDate = $input['published_date'] ?? null;
if (empty($publishedDate)) $publishedDate = null;

if (!$vcardId) sendError('vCard ID required');
if (empty($title)) sendError('Blog title is required');
if (strlen($title) > 255) sendError('Title too long');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    $stmt = $pdo->prepare("SELECT id FROM vcards WHERE id = ? AND user_id = ? LIMIT 1");
    $stmt->execute([$vcardId, $userId]);
    if (!$stmt->fetch()) sendError('Access denied', 403);

    if ($blogId > 0) {
        $stmt = $pdo->prepare("UPDATE vcard_blogs SET title = ?, content = ?, image = ?, published_date = ? WHERE id = ? AND vcard_id = ?");
        $stmt->execute([$title, $content, $image, $publishedDate, $blogId, $vcardId]);
        sendSuccess('Blog updated', ['id' => $blogId]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO vcard_blogs (vcard_id, title, content, image, published_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$vcardId, $title, $content, $image, $publishedDate]);
        sendSuccess('Blog added', ['id' => $pdo->lastInsertId()]);
    }
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
