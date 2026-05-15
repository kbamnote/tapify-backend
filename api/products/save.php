<?php
/**
 * TAPIFY - Products Save API
 * POST /backend/api/products/save.php
 * Body: { id (optional), vcard_id, name, description, currency, price, product_url, image, display_order }
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Only POST method allowed', 405);
}

$input = getInput();
$vcardId = (int)($input['vcard_id'] ?? 0);
$productId = (int)($input['id'] ?? 0);
$name = sanitize($input['name'] ?? '');
$description = $input['description'] ?? '';
$currency = sanitize($input['currency'] ?? 'INR');
$price = isset($input['price']) && $input['price'] !== '' ? (float)$input['price'] : null;
$productUrl = trim($input['product_url'] ?? '');
$image = sanitize($input['image'] ?? '');
$displayOrder = (int)($input['display_order'] ?? 0);

if (!$vcardId) sendError('vCard ID required');
if (empty($name)) sendError('Product name is required');
if (strlen($name) > 200) sendError('Product name too long (max 200)');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    // Verify vCard ownership
    $stmt = $pdo->prepare("SELECT id FROM vcards WHERE id = ? AND user_id = ? LIMIT 1");
    $stmt->execute([$vcardId, $userId]);
    if (!$stmt->fetch()) sendError('Access denied', 403);

    if ($productId > 0) {
        // Update existing
        $stmt = $pdo->prepare("UPDATE vcard_products SET name = ?, description = ?, currency = ?, price = ?, product_url = ?, image = ?, display_order = ? WHERE id = ? AND vcard_id = ?");
        $stmt->execute([$name, $description, $currency, $price, $productUrl, $image, $displayOrder, $productId, $vcardId]);

        sendSuccess('Product updated', ['id' => $productId]);
    } else {
        // Create new
        $stmt = $pdo->prepare("INSERT INTO vcard_products (vcard_id, name, description, currency, price, product_url, image, display_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$vcardId, $name, $description, $currency, $price, $productUrl, $image, $displayOrder]);
        $newId = $pdo->lastInsertId();

        sendSuccess('Product added', ['id' => $newId]);
    }

} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
