<?php
/**
 * TAPIFY - WhatsApp Stores List
 * GET /backend/api/stores/list.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAuth();

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    $search = trim($_GET['search'] ?? '');
    $status = $_GET['status'] ?? 'all';
    $sort = $_GET['sort'] ?? 'created_desc';

    $sql = "SELECT id, url_alias, store_name, owner_name, whatsapp_number, logo_image,
                   view_count, order_count, status, template_id,
                   DATE_FORMAT(created_at, '%d/%m/%Y') AS created_at_formatted,
                   created_at
            FROM whatsapp_stores
            WHERE user_id = ?";
    $params = [$userId];

    if (!empty($search)) {
        $sql .= " AND (store_name LIKE ? OR url_alias LIKE ?)";
        $params[] = '%' . $search . '%';
        $params[] = '%' . $search . '%';
    }

    if ($status === 'active') $sql .= " AND status = 1";
    elseif ($status === 'inactive') $sql .= " AND status = 0";

    switch ($sort) {
        case 'name_asc':    $sql .= " ORDER BY store_name ASC"; break;
        case 'name_desc':   $sql .= " ORDER BY store_name DESC"; break;
        case 'orders_desc': $sql .= " ORDER BY order_count DESC"; break;
        case 'views_desc':  $sql .= " ORDER BY view_count DESC"; break;
        default:            $sql .= " ORDER BY created_at DESC";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $stores = $stmt->fetchAll();

    foreach ($stores as &$store) {
        $store['avatar'] = strtoupper(substr($store['store_name'], 0, 2));
        $store['preview_url'] = SITE_URL . '/' . $store['url_alias'];
        $store['status'] = (bool)$store['status'];
        $store['view_count'] = (int)$store['view_count'];
        $store['order_count'] = (int)$store['order_count'];
        if ($store['logo_image']) {
            $store['logo_url'] = imgUrl($store['logo_image']);
        }
    }

    sendSuccess('Stores loaded', [
        'stores' => $stores,
        'total' => count($stores)
    ]);

} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
