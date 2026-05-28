<?php
/**
 * TAPIFY - Business Directory List
 * GET /api/businesses/list.php
 *
 * Query params:
 *   search   (string)  - search by name, description, city
 *   category (string)  - filter by category (empty = all)
 *   sort     (string)  - newest | name_asc | views_desc
 *   page     (int)     - page number, default 1
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

try {
    $pdo           = getDB();
    $currentUserId = getCurrentUserId();

    $search   = trim($_GET['search']   ?? '');
    $category = trim($_GET['category'] ?? '');
    $sort     = $_GET['sort']          ?? 'newest';
    $page     = max(1, (int)($_GET['page'] ?? 1));
    $limit    = 20;
    $offset   = ($page - 1) * $limit;

    // ---------- Base query ----------
    $where  = "WHERE b.listed = 1";
    $params = [];

    if (!empty($search)) {
        $where   .= " AND (b.business_name LIKE ? OR b.description LIKE ? OR b.city LIKE ?)";
        $term     = '%' . $search . '%';
        $params[] = $term;
        $params[] = $term;
        $params[] = $term;
    }

    if (!empty($category)) {
        $where   .= " AND b.category = ?";
        $params[] = $category;
    }

    // ---------- Order ----------
    switch ($sort) {
        case 'name_asc':   $order = "ORDER BY b.business_name ASC";  break;
        case 'views_desc': $order = "ORDER BY b.view_count DESC";     break;
        default:           $order = "ORDER BY b.created_at DESC";     break;
    }

    // ---------- Total count (for has_more) ----------
    $countSql  = "SELECT COUNT(*) FROM businesses b $where";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $total = (int)$countStmt->fetchColumn();

    // ---------- Paginated data ----------
    $sql = "
        SELECT
            b.id, b.user_id, b.business_name, b.gstin,
            b.category, b.description, b.city, b.website,
            b.phone, b.logo, b.view_count,
            DATE_FORMAT(b.created_at, '%d %b %Y') AS joined_date,
            u.name  AS owner_name,
            u.avatar AS owner_avatar
        FROM businesses b
        JOIN users u ON u.id = b.user_id
        $where
        $order
        LIMIT $limit OFFSET $offset
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $businesses = $stmt->fetchAll();

    // ---------- Format each row ----------
    foreach ($businesses as &$biz) {
        $biz['view_count']        = (int)$biz['view_count'];
        $biz['is_mine']           = ((int)$biz['user_id'] === (int)$currentUserId);
        $biz['logo_url']          = $biz['logo']         ? imgUrl($biz['logo'])         : null;
        $biz['owner_avatar_url']  = $biz['owner_avatar'] ? imgUrl($biz['owner_avatar']) : null;
        $biz['gstin_registered']  = !empty($biz['gstin']);

        // Short description for list cards (max 120 chars)
        if ($biz['description'] && mb_strlen($biz['description']) > 120) {
            $biz['description_short'] = mb_substr($biz['description'], 0, 120) . '...';
        } else {
            $biz['description_short'] = $biz['description'];
        }
    }
    unset($biz);

    sendSuccess('Directory loaded', [
        'businesses' => $businesses,
        'total'      => $total,
        'page'       => $page,
        'limit'      => $limit,
        'has_more'   => ($offset + count($businesses)) < $total,
    ]);

} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
