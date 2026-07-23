<?php
/**
 * Website enquiries for the logged-in customer's builder sites — powers the
 * dashboard's "Website Inquiries" page.
 *
 *   GET  /api/sites/inquiries.php                    -> all enquiries across my sites
 *   GET  /api/sites/inquiries.php?site_id=12         -> one site
 *   GET  /api/sites/inquiries.php?unread=1           -> unread only
 *   POST /api/sites/inquiries.php {id, action:read}  -> mark read / unread
 *   POST /api/sites/inquiries.php {id, action:delete}-> delete
 *
 * Owner-only: every statement is scoped to sites owned by the current user
 * (staff/admin may see all), so one customer can never read another's enquiries.
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if (!isLoggedIn()) sendError('Not logged in', 401);

$userId = getCurrentUserId();
$staff  = isStaffOrAdmin();

try {
    $db = getDB();

    /* ------------------------------------------------- mark read / delete */
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $in     = getInput();
        $id     = (int)($in['id'] ?? 0);
        $action = trim((string)($in['action'] ?? 'read'));
        if ($id <= 0) sendError('id is required');

        // Confirm the enquiry belongs to a site this user owns.
        $st = $db->prepare(
            "SELECT i.id FROM site_inquiries i JOIN sites s ON s.id = i.site_id
              WHERE i.id = ?" . ($staff ? '' : ' AND s.user_id = ?')
        );
        $st->execute($staff ? [$id] : [$id, $userId]);
        if (!$st->fetchColumn()) sendError('Enquiry not found', 404);

        if ($action === 'delete') {
            $db->prepare("DELETE FROM site_inquiries WHERE id = ?")->execute([$id]);
            sendSuccess('Enquiry deleted');
        }

        $isRead = isset($in['is_read']) ? (int)!!$in['is_read'] : 1;
        $db->prepare("UPDATE site_inquiries SET is_read = ? WHERE id = ?")->execute([$isRead, $id]);
        sendSuccess('Enquiry updated');
    }

    /* ------------------------------------------------------------- list */
    $where  = [];
    $params = [];
    if (!$staff) { $where[] = 's.user_id = ?'; $params[] = $userId; }
    if (!empty($_GET['site_id'])) { $where[] = 'i.site_id = ?'; $params[] = (int)$_GET['site_id']; }
    if (!empty($_GET['unread']))  { $where[] = 'i.is_read = 0'; }

    $sql = "SELECT i.*, s.slug AS site_slug, s.name AS site_name
              FROM site_inquiries i JOIN sites s ON s.id = i.site_id"
         . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
         . ' ORDER BY i.created_at DESC LIMIT 500';

    $st = $db->prepare($sql);
    $st->execute($params);
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);

    $today  = date('Y-m-d');
    $counts = ['all' => 0, 'unread' => 0, 'today' => 0];
    foreach ($rows as &$r) {
        $r['is_read'] = (bool)$r['is_read'];
        $counts['all']++;
        if (!$r['is_read']) $counts['unread']++;
        if (substr((string)$r['created_at'], 0, 10) === $today) $counts['today']++;
    }
    unset($r);

    sendSuccess('OK', ['inquiries' => $rows, 'counts' => $counts]);
} catch (Exception $e) {
    error_log('site inquiries: ' . $e->getMessage());
    sendError('Could not load enquiries.', 500);
}
