<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

$storeId = (int)($_GET['store_id'] ?? 0);
if (!$storeId) sendError('Store ID required');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    if (isAdmin()) {
        $stmt = $pdo->prepare("SELECT id FROM whatsapp_stores WHERE id = ? LIMIT 1");
        $stmt->execute([$storeId]);
    } else {
        $stmt = $pdo->prepare("SELECT id FROM whatsapp_stores WHERE id = ? AND user_id = ? LIMIT 1");
        $stmt->execute([$storeId, $userId]);
    }
    if (!$stmt->fetch()) sendError('Access denied', 403);

    $stmt = $pdo->prepare("
        SELECT p.*, c.name AS category_name
        FROM whatsapp_store_products p
        LEFT JOIN whatsapp_store_categories c ON c.id = p.category_id
        WHERE p.store_id = ?
        ORDER BY p.is_featured DESC, p.display_order, p.id DESC
    ");
    $stmt->execute([$storeId]);
    $products = $stmt->fetchAll();

    foreach ($products as &$p) {
        $p['price'] = (float)$p['price'];
        $p['discount_price'] = $p['discount_price'] !== null ? (float)$p['discount_price'] : null;
        $p['is_featured'] = (bool)$p['is_featured'];
        $p['in_stock'] = (bool)$p['in_stock'];
        $p['status'] = (bool)$p['status'];
        // If image is already a full URL (Cloudinary), use as-is; otherwise build from SITE_URL
        if ($p['image']) {
            $p['image_url'] = (strpos($p['image'], 'http') === 0)
                ? $p['image']
                : SITE_URL . '/' . $p['image'];
        }
    }

    sendSuccess('Products loaded', [
        'products' => $products,
        'total' => count($products)
    ]);
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
