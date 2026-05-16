<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

$storeId = (int)($_GET['store_id'] ?? 0);
if (!$storeId) sendError('Store ID required');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    // Verify ownership
    if (isAdmin()) {
        $stmt = $pdo->prepare("SELECT id FROM whatsapp_stores WHERE id = ? LIMIT 1");
        $stmt->execute([$storeId]);
    } else {
        $stmt = $pdo->prepare("SELECT id FROM whatsapp_stores WHERE id = ? AND user_id = ? LIMIT 1");
        $stmt->execute([$storeId, $userId]);
    }
    if (!$stmt->fetch()) sendError('Access denied', 403);

    // Get categories with product count
    $stmt = $pdo->prepare("
        SELECT c.*, COUNT(p.id) AS product_count
        FROM whatsapp_store_categories c
        LEFT JOIN whatsapp_store_products p ON p.category_id = c.id AND p.status = 1
        WHERE c.store_id = ?
        GROUP BY c.id
        ORDER BY c.display_order, c.id
    ");
    $stmt->execute([$storeId]);
    $categories = $stmt->fetchAll();

    foreach ($categories as &$c) {
        $c['product_count'] = (int)$c['product_count'];
        $c['status'] = (bool)$c['status'];
        if ($c['image']) $c['image_url'] = imgUrl($c['image']);
    }

    sendSuccess('Categories loaded', ['categories' => $categories]);
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
