<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$input = getInput();
$vcardId = (int)($input['vcard_id'] ?? 0);
$categoryId = (int)($input['id'] ?? 0);
$name = sanitize($input['name'] ?? '');

if (!$vcardId) sendError('vCard ID required');
if (empty($name)) sendError('Category name is required');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    if (!userCanEditVcard($pdo, $vcardId)) sendError('Access denied', 403);

    if ($categoryId > 0) {
        $stmt = $pdo->prepare("UPDATE vcard_service_categories SET name = ? WHERE id = ? AND vcard_id = ?");
        $stmt->execute([$name, $categoryId, $vcardId]);
        sendSuccess('Category updated', ['id' => $categoryId]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO vcard_service_categories (vcard_id, name) VALUES (?, ?)");
        $stmt->execute([$vcardId, $name]);
        sendSuccess('Category added', ['id' => $pdo->lastInsertId()]);
    }
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
