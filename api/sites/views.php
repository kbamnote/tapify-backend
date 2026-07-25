<?php
/**
 * GET /api/sites/views.php   (auth)
 *
 * Website view analytics for the logged-in user's builder sites:
 *   { total: <int>, daily: [ {day:'Mon', views:N}, ... 7 entries ending today ] }
 *
 * Clients see their own site(s); staff/admin see all sites (matches list.php).
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if (!isLoggedIn()) sendError('Not logged in', 401);

$userId = getCurrentUserId();
$staff  = isStaffOrAdmin();

// Empty 7-day series (used when there's no data yet or the table isn't migrated).
$emptyDaily = [];
for ($i = 6; $i >= 0; $i--) {
    $emptyDaily[] = ['day' => date('D', strtotime("-$i day")), 'views' => 0];
}

try {
    $db = getDB();

    if (!$db->query("SHOW TABLES LIKE 'site_views'")->fetchColumn()) {
        sendSuccess('OK', ['total' => 0, 'daily' => $emptyDaily, 'migrated' => false]);
    }

    // Which sites belong to this viewer.
    if ($staff) {
        $ids = $db->query("SELECT id FROM sites")->fetchAll(PDO::FETCH_COLUMN);
    } else {
        $st = $db->prepare("SELECT id FROM sites WHERE user_id = ?");
        $st->execute([$userId]);
        $ids = $st->fetchAll(PDO::FETCH_COLUMN);
    }
    if (!$ids) sendSuccess('OK', ['total' => 0, 'daily' => $emptyDaily]);

    $in = implode(',', array_map('intval', $ids));

    $total = (int)$db->query("SELECT COALESCE(SUM(views),0) FROM site_views WHERE site_id IN ($in)")->fetchColumn();

    // Per-day totals for the last 7 days, keyed by date.
    $rows = $db->query(
        "SELECT view_date, SUM(views) AS v FROM site_views
          WHERE site_id IN ($in) AND view_date >= (CURDATE() - INTERVAL 6 DAY)
          GROUP BY view_date"
    )->fetchAll(PDO::FETCH_KEY_PAIR);

    $daily = [];
    for ($i = 6; $i >= 0; $i--) {
        $d = date('Y-m-d', strtotime("-$i day"));
        $daily[] = ['day' => date('D', strtotime($d)), 'views' => (int)($rows[$d] ?? 0)];
    }

    sendSuccess('OK', ['total' => $total, 'daily' => $daily]);
} catch (Exception $e) {
    error_log('site views: ' . $e->getMessage());
    sendSuccess('OK', ['total' => 0, 'daily' => $emptyDaily]);
}
