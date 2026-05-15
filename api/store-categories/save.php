<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$input = getInput();
$storeId = (int)($input['store_id'] ?? 0);
$catId = (int)($input['id'] ?? 0);
$name = sanitize($input['name'] ?? '');
$description = $input['description'] ?? '';

if (!$storeId) sendError('Store ID required');
if (empty($name)) sendError('Category name is required');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    // Verify store ownership
    $stmt = $pdo->prepare("SELECT id FROM whatsapp_stores WHERE id = ? AND user_id = ? LIMIT 1");
    $stmt->execute([$storeId, $userId]);
    if (!$stmt->fetch()) sendError('Access denied', 403);

    if ($catId > 0) {
        $stmt = $pdo->prepare("UPDATE whatsapp_store_categories SET name = ?, description = ? WHERE id = ? AND store_id = ?");
        $stmt->execute([$name, $description, $catId, $storeId]);
        sendSuccess('Category updated', ['id' => $catId]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO whatsapp_store_categories (store_id, name, description) VALUES (?, ?, ?)");
        $stmt->execute([$storeId, $name, $description]);
        sendSuccess('Category added', ['id' => $pdo->lastInsertId()]);
    }
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
