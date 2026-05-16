<?php
/**
 * TAPIFY - vCards List API
 * GET /backend/api/vcards/list.php
 * Admin: all vCards. User: own vCards only.
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
        $sql = "SELECT v.id, v.url_alias, v.vcard_name, v.occupation, v.first_name, v.last_name,
                       v.profile_image, v.view_count, v.status, v.template_id, v.user_id,
                       u.name AS owner_name, u.email AS owner_email,
                       DATE_FORMAT(v.created_at, '%d/%m/%Y') AS created_at_formatted,
                       v.created_at
                FROM vcards v
                LEFT JOIN users u ON u.id = v.user_id
                WHERE 1=1";
        $params = [];
    } else {
        $sql = "SELECT id, url_alias, vcard_name, occupation, first_name, last_name,
                       profile_image, view_count, status, template_id, user_id,
                       DATE_FORMAT(created_at, '%d/%m/%Y') AS created_at_formatted,
                       created_at
                FROM vcards
                WHERE user_id = ?";
        $params = [$userId];
    }

    if (!empty($search)) {
        if ($admin) {
            $sql .= " AND (v.vcard_name LIKE ? OR v.url_alias LIKE ? OR v.occupation LIKE ? OR u.name LIKE ? OR u.email LIKE ?)";
        } else {
            $sql .= " AND (vcard_name LIKE ? OR url_alias LIKE ? OR occupation LIKE ?)";
        }
        $searchTerm = '%' . $search . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        if ($admin) {
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
    }

    if ($status === 'active') {
        $sql .= $admin ? " AND v.status = 1" : " AND status = 1";
    } elseif ($status === 'inactive') {
        $sql .= $admin ? " AND v.status = 0" : " AND status = 0";
    }

    $orderCol = $admin ? 'v.' : '';
    switch ($sort) {
        case 'created_asc':  $sql .= " ORDER BY {$orderCol}created_at ASC"; break;
        case 'name_asc':     $sql .= " ORDER BY {$orderCol}vcard_name ASC"; break;
        case 'name_desc':    $sql .= " ORDER BY {$orderCol}vcard_name DESC"; break;
        case 'views_desc':   $sql .= " ORDER BY {$orderCol}view_count DESC"; break;
        default:             $sql .= " ORDER BY {$orderCol}created_at DESC";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $vcards = $stmt->fetchAll();

    foreach ($vcards as &$vcard) {
        $name = $vcard['vcard_name'];
        $words = explode(' ', $name);
        $avatar = '';
        foreach ($words as $word) {
            if (!empty($word)) $avatar .= strtoupper($word[0]);
            if (strlen($avatar) >= 2) break;
        }
        $vcard['avatar'] = $avatar ?: 'V';
        $vcard['preview_url'] = SITE_URL . '/' . $vcard['url_alias'];
        $vcard['status'] = (bool)$vcard['status'];
        $vcard['view_count'] = (int)$vcard['view_count'];
        $vcard['title'] = $vcard['occupation'] ?: 'No occupation';
        if ($admin) {
            $vcard['owner_label'] = trim($vcard['owner_name'] ?? '') ?: ($vcard['owner_email'] ?? 'Unknown');
        }
    }

    sendSuccess('vCards loaded', [
        'vcards' => $vcards,
        'total' => count($vcards),
        'is_admin' => $admin
    ]);

} catch (Exception $e) {
    sendError('Failed to load vCards: ' . $e->getMessage(), 500);
}
