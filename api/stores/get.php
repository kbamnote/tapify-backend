<?php
/**
 * TAPIFY - Get Store Details
 * GET /backend/api/stores/get.php?id=X
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAuth();

$id = (int)($_GET['id'] ?? 0);
if (!$id) sendError('Store ID required');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    if (isAdmin()) {
        $stmt = $pdo->prepare("SELECT * FROM whatsapp_stores WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM whatsapp_stores WHERE id = ? AND user_id = ? LIMIT 1");
        $stmt->execute([$id, $userId]);
    }
    $store = $stmt->fetch();

    if (!$store) sendError('Store not found', 404);

    // Convert types
    $store['status'] = (bool)$store['status'];
    $store['view_count'] = (int)$store['view_count'];
    $store['order_count'] = (int)$store['order_count'];
    $store['min_order_amount'] = (float)$store['min_order_amount'];
    $store['delivery_charge'] = (float)$store['delivery_charge'];
    $store['cod_available'] = (bool)$store['cod_available'];
    $store['show_search'] = (bool)$store['show_search'];
    $store['show_categories'] = (bool)$store['show_categories'];
    $store['show_featured'] = (bool)$store['show_featured'];

    $store['logo_url']    = imgUrl($store['logo_image']);
    $store['cover_url']   = imgUrl($store['cover_image']);
    $store['favicon_url'] = imgUrl($store['favicon_image']);

    $store['preview_url'] = SITE_URL . '/' . $store['url_alias'];

    sendSuccess('Store loaded', ['store' => $store]);

} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
