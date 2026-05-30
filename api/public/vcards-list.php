<?php
/**
 * TAPIFY - Public vCards Directory
 * GET /api/public/vcards-list.php
 *
 * No authentication required — returns all active vCards for the public Businesses page.
 *
 * Query params:
 *   search  (string)  filter by name, occupation, url_alias
 *   sort    (string)  newest | name_asc | views_desc
 *   page    (int)     page number, default 1
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

// Allow cross-origin requests from any front-end origin
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

try {
    $pdo = getDB();

    $search = trim($_GET['search'] ?? '');
    $sort   = $_GET['sort']   ?? 'newest';
    $page   = max(1, (int)($_GET['page'] ?? 1));
    $limit  = 24;
    $offset = ($page - 1) * $limit;

    $where  = "WHERE v.status = 1";
    $params = [];

    if (!empty($search)) {
        $where   .= " AND (v.vcard_name LIKE ? OR v.occupation LIKE ? OR v.url_alias LIKE ?)";
        $term     = '%' . $search . '%';
        $params[] = $term;
        $params[] = $term;
        $params[] = $term;
    }

    switch ($sort) {
        case 'name_asc':   $order = "ORDER BY v.vcard_name ASC";  break;
        case 'views_desc': $order = "ORDER BY v.view_count DESC";  break;
        default:           $order = "ORDER BY v.created_at DESC";  break;
    }

    // Total for pagination
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM vcards v $where");
    $countStmt->execute($params);
    $total = (int)$countStmt->fetchColumn();

    $sql = "
        SELECT
            v.id,
            v.url_alias,
            v.vcard_name,
            v.occupation,
            v.profile_image,
            v.cover_image,
            v.template_id,
            v.view_count,
            DATE_FORMAT(v.created_at, '%d %b %Y') AS joined_date
        FROM vcards v
        $where
        $order
        LIMIT $limit OFFSET $offset
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $vcards = $stmt->fetchAll();

    foreach ($vcards as &$v) {
        $v['view_count']        = (int)$v['view_count'];
        $v['preview_url']       = SITE_URL . '/' . $v['url_alias'];
        $v['profile_image_url'] = $v['profile_image'] ? imgUrl($v['profile_image']) : null;
        $v['cover_image_url']   = $v['cover_image']   ? imgUrl($v['cover_image'])   : null;

        // Initials avatar fallback
        $words  = explode(' ', $v['vcard_name']);
        $initials = '';
        foreach ($words as $word) {
            if (!empty($word)) $initials .= strtoupper($word[0]);
            if (strlen($initials) >= 2) break;
        }
        $v['initials'] = $initials ?: 'TP';
    }
    unset($v);

    sendSuccess('Businesses loaded', [
        'vcards'   => $vcards,
        'total'    => $total,
        'page'     => $page,
        'limit'    => $limit,
        'has_more' => ($offset + count($vcards)) < $total,
    ]);

} catch (Exception $e) {
    sendError('Failed to load: ' . $e->getMessage(), 500);
}
