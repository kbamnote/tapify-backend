<?php
/**
 * TAPIFY - Website Leads List
 * GET /api/admin/leads/list.php
 * Admin only.
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/functions.php';

header('Content-Type: application/json');

requireAuth();
if (!isAdmin()) sendError('Admin access required', 403);

try {
    $pdo = getDB();

    // Auto-create table if it doesn't exist yet
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS website_leads (
            id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name       VARCHAR(150) NOT NULL,
            phone      VARCHAR(20)  NOT NULL,
            email      VARCHAR(150) DEFAULT NULL,
            city       VARCHAR(100) DEFAULT NULL,
            source     VARCHAR(80)  DEFAULT 'website',
            is_read    TINYINT(1)   DEFAULT 0,
            ip         VARCHAR(45)  DEFAULT NULL,
            created_at DATETIME     DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    // Add is_read column if missing (for older installs)
    try {
        $pdo->exec("ALTER TABLE website_leads ADD COLUMN is_read TINYINT(1) DEFAULT 0");
    } catch (Exception $e) { /* already exists */ }

    $search = trim($_GET['search'] ?? '');
    $source = trim($_GET['source'] ?? '');
    $read   = $_GET['read']   ?? 'all'; // all | unread | read
    $sort   = $_GET['sort']   ?? 'newest';
    $page   = max(1, (int)($_GET['page'] ?? 1));
    $limit  = 25;
    $offset = ($page - 1) * $limit;

    $where  = "WHERE 1=1";
    $params = [];

    if (!empty($search)) {
        $where   .= " AND (name LIKE ? OR phone LIKE ? OR email LIKE ? OR city LIKE ?)";
        $term     = '%' . $search . '%';
        $params[] = $term; $params[] = $term;
        $params[] = $term; $params[] = $term;
    }
    if (!empty($source)) {
        $where .= " AND source = ?"; $params[] = $source;
    }
    if ($read === 'unread') { $where .= " AND is_read = 0"; }
    if ($read === 'read')   { $where .= " AND is_read = 1"; }

    switch ($sort) {
        case 'oldest': $order = "ORDER BY created_at ASC";  break;
        case 'name':   $order = "ORDER BY name ASC";        break;
        default:       $order = "ORDER BY created_at DESC"; break;
    }

    // Counts
    $totalStmt = $pdo->prepare("SELECT COUNT(*) FROM website_leads $where");
    $totalStmt->execute($params);
    $total = (int)$totalStmt->fetchColumn();

    $unreadStmt = $pdo->query("SELECT COUNT(*) FROM website_leads WHERE is_read = 0");
    $unread = (int)$unreadStmt->fetchColumn();

    // Data
    $stmt = $pdo->prepare("SELECT * FROM website_leads $where $order LIMIT $limit OFFSET $offset");
    $stmt->execute($params);
    $leads = $stmt->fetchAll();

    foreach ($leads as &$l) {
        $l['is_read']    = (bool)$l['is_read'];
        $l['created_at_formatted'] = date('d M Y, h:i A', strtotime($l['created_at']));
    }

    // Distinct sources for filter
    $sources = $pdo->query("SELECT DISTINCT source FROM website_leads ORDER BY source")->fetchAll(\PDO::FETCH_COLUMN);

    sendSuccess('Leads loaded', [
        'leads'    => $leads,
        'total'    => $total,
        'unread'   => $unread,
        'page'     => $page,
        'limit'    => $limit,
        'has_more' => ($offset + count($leads)) < $total,
        'sources'  => $sources,
    ]);

} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
