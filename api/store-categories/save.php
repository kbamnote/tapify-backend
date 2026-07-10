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
$displayOrder = isset($input['display_order']) ? (int)$input['display_order'] : 0;
// Optional: allow setting an image URL directly (uploads still go via upload-image.php type=category)
$image = isset($input['image']) ? sanitize($input['image']) : null;

if (!$storeId) sendError('Store ID required');
if (empty($name)) sendError('Category name is required');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    // Verify store ownership
    if (isAdmin()) {
        $stmt = $pdo->prepare("SELECT id FROM whatsapp_stores WHERE id = ? LIMIT 1");
        $stmt->execute([$storeId]);
    } else {
        $stmt = $pdo->prepare("SELECT id FROM whatsapp_stores WHERE id = ? AND user_id = ? LIMIT 1");
        $stmt->execute([$storeId, $userId]);
    }
    if (!$stmt->fetch()) sendError('Access denied', 403);

    if ($catId > 0) {
        // Only overwrite image when an explicit value was provided (keep uploaded image otherwise)
        if ($image !== null) {
            $stmt = $pdo->prepare("UPDATE whatsapp_store_categories SET name = ?, description = ?, display_order = ?, image = ? WHERE id = ? AND store_id = ?");
            $stmt->execute([$name, $description, $displayOrder, $image, $catId, $storeId]);
        } else {
            $stmt = $pdo->prepare("UPDATE whatsapp_store_categories SET name = ?, description = ?, display_order = ? WHERE id = ? AND store_id = ?");
            $stmt->execute([$name, $description, $displayOrder, $catId, $storeId]);
        }
        sendSuccess('Category updated', ['id' => $catId]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO whatsapp_store_categories (store_id, name, description, display_order, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$storeId, $name, $description, $displayOrder, $image]);
        sendSuccess('Category added', ['id' => $pdo->lastInsertId()]);
    }
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
