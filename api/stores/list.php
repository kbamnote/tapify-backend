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
    $admin = isAdmin();

    $search = trim($_GET['search'] ?? '');
    $status = $_GET['status'] ?? 'all';
    $sort = $_GET['sort'] ?? 'created_desc';

    if ($admin) {
        $sql = "SELECT s.id, s.url_alias, s.store_name, s.owner_name, s.whatsapp_number, s.logo_image,
                       s.view_count, s.order_count, s.status, s.template_id, s.user_id,
                       u.name AS owner_username, u.email AS owner_email,
                       DATE_FORMAT(s.created_at, '%d/%m/%Y') AS created_at_formatted,
                       s.created_at
                FROM whatsapp_stores s
                LEFT JOIN users u ON u.id = s.user_id
                WHERE 1=1";
        $params = [];
    } else {
        $sql = "SELECT id, url_alias, store_name, owner_name, whatsapp_number, logo_image,
                       view_count, order_count, status, template_id, user_id,
                       DATE_FORMAT(created_at, '%d/%m/%Y') AS created_at_formatted,
                       created_at
                FROM whatsapp_stores
                WHERE user_id = ?";
        $params = [$userId];
    }

    if (!empty($search)) {
        if ($admin) {
            $sql .= " AND (s.store_name LIKE ? OR s.url_alias LIKE ? OR u.name LIKE ? OR u.email LIKE ?)";
            $searchTerm = '%' . $search . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        } else {
            $sql .= " AND (store_name LIKE ? OR url_alias LIKE ?)";
            $searchTerm = '%' . $search . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
    }

    if ($status === 'active') {
        $sql .= $admin ? " AND s.status = 1" : " AND status = 1";
    } elseif ($status === 'inactive') {
        $sql .= $admin ? " AND s.status = 0" : " AND status = 0";
    }

    $orderCol = $admin ? 's.' : '';
    switch ($sort) {
        case 'name_asc':    $sql .= " ORDER BY {$orderCol}store_name ASC"; break;
        case 'name_desc':   $sql .= " ORDER BY {$orderCol}store_name DESC"; break;
        case 'orders_desc': $sql .= " ORDER BY {$orderCol}order_count DESC"; break;
        case 'views_desc':  $sql .= " ORDER BY {$orderCol}view_count DESC"; break;
        default:            $sql .= " ORDER BY {$orderCol}created_at DESC";
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
        if ($admin) {
            $store['owner_label'] = trim($store['owner_username'] ?? '') ?: ($store['owner_email'] ?? 'Unknown');
        }
    }

    sendSuccess('Stores loaded', [
        'stores' => $stores,
        'total' => count($stores),
        'is_admin' => $admin
    ]);

} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
