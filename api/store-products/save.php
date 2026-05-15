<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$input = getInput();
$storeId = (int)($input['store_id'] ?? 0);
$productId = (int)($input['id'] ?? 0);
$categoryId = !empty($input['category_id']) ? (int)$input['category_id'] : null;
$name = sanitize($input['name'] ?? '');
$description = $input['description'] ?? '';
$price = (float)($input['price'] ?? 0);
$discountPrice = isset($input['discount_price']) && $input['discount_price'] !== '' ? (float)$input['discount_price'] : null;
$sku = sanitize($input['sku'] ?? '');
$isFeatured = !empty($input['is_featured']) ? 1 : 0;
$inStock = isset($input['in_stock']) ? (!empty($input['in_stock']) ? 1 : 0) : 1;

if (!$storeId) sendError('Store ID required');
if (empty($name)) sendError('Product name is required');
if ($price < 0) sendError('Invalid price');
if ($discountPrice !== null && $discountPrice >= $price) sendError('Discount price must be less than regular price');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    // Verify store ownership
    $stmt = $pdo->prepare("SELECT id FROM whatsapp_stores WHERE id = ? AND user_id = ? LIMIT 1");
    $stmt->execute([$storeId, $userId]);
    if (!$stmt->fetch()) sendError('Access denied', 403);

    // Verify category belongs to this store
    if ($categoryId) {
        $stmt = $pdo->prepare("SELECT id FROM whatsapp_store_categories WHERE id = ? AND store_id = ? LIMIT 1");
        $stmt->execute([$categoryId, $storeId]);
        if (!$stmt->fetch()) sendError('Invalid category');
    }

    if ($productId > 0) {
        $stmt = $pdo->prepare("UPDATE whatsapp_store_products
            SET category_id = ?, name = ?, description = ?, price = ?, discount_price = ?,
                sku = ?, is_featured = ?, in_stock = ?
            WHERE id = ? AND store_id = ?");
        $stmt->execute([$categoryId, $name, $description, $price, $discountPrice, $sku, $isFeatured, $inStock, $productId, $storeId]);
        sendSuccess('Product updated', ['id' => $productId]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO whatsapp_store_products
            (store_id, category_id, name, description, price, discount_price, sku, is_featured, in_stock)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$storeId, $categoryId, $name, $description, $price, $discountPrice, $sku, $isFeatured, $inStock]);
        sendSuccess('Product added', ['id' => $pdo->lastInsertId()]);
    }
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
