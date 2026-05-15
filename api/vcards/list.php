<?php
/**
 * TAPIFY - vCards List API (FIXED)
 * GET /backend/api/vcards/list.php
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

    // Build query using positional ? parameters
    $sql = "SELECT id, url_alias, vcard_name, occupation, first_name, last_name,
                   profile_image, view_count, status, template_id,
                   DATE_FORMAT(created_at, '%d/%m/%Y') AS created_at_formatted,
                   created_at
            FROM vcards
            WHERE user_id = ?";

    $params = [$userId];

    if (!empty($search)) {
        $sql .= " AND (vcard_name LIKE ? OR url_alias LIKE ? OR occupation LIKE ?)";
        $searchTerm = '%' . $search . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }

    if ($status === 'active') {
        $sql .= " AND status = 1";
    } elseif ($status === 'inactive') {
        $sql .= " AND status = 0";
    }

    switch ($sort) {
        case 'created_asc':  $sql .= " ORDER BY created_at ASC"; break;
        case 'name_asc':     $sql .= " ORDER BY vcard_name ASC"; break;
        case 'name_desc':    $sql .= " ORDER BY vcard_name DESC"; break;
        case 'views_desc':   $sql .= " ORDER BY view_count DESC"; break;
        default:             $sql .= " ORDER BY created_at DESC";
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
    }

    sendSuccess('vCards loaded', [
        'vcards' => $vcards,
        'total' => count($vcards)
    ]);

} catch (Exception $e) {
    sendError('Failed to load vCards: ' . $e->getMessage(), 500);
}
