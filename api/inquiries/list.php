<?php
/**
 * TAPIFY - Inquiries List
 * GET /backend/api/inquiries/list.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    $vcardId = (int)($_GET['vcard_id'] ?? 0);
    $filter = $_GET['filter'] ?? 'all'; // all, unread, today
    $search = trim($_GET['search'] ?? '');

    // Get all inquiries for user's vCards
    $sql = "SELECT i.*, v.vcard_name, v.url_alias AS vcard_url_alias,
                   CONCAT(v.first_name, ' ', v.last_name) AS vcard_owner_name
            FROM vcard_inquiries i
            JOIN vcards v ON v.id = i.vcard_id
            WHERE v.user_id = ?";
    $params = [$userId];

    if ($vcardId > 0) {
        $sql .= " AND i.vcard_id = ?";
        $params[] = $vcardId;
    }

    if ($filter === 'unread') {
        $sql .= " AND i.is_read = 0";
    } elseif ($filter === 'today') {
        $sql .= " AND DATE(i.created_at) = CURDATE()";
    }

    if (!empty($search)) {
        $sql .= " AND (i.name LIKE ? OR i.email LIKE ? OR i.phone LIKE ? OR i.message LIKE ?)";
        $searchParam = '%' . $search . '%';
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
    }

    $sql .= " ORDER BY i.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $inquiries = $stmt->fetchAll();

    foreach ($inquiries as &$inq) {
        $inq['is_read'] = (bool)$inq['is_read'];
        $inq['created_at_formatted'] = date('d M Y, h:i A', strtotime($inq['created_at']));
        $inq['time_ago'] = timeAgo($inq['created_at']);
    }

    // Get stats
    $stmt = $pdo->prepare("
        SELECT
            COUNT(*) AS total,
            SUM(CASE WHEN i.is_read = 0 THEN 1 ELSE 0 END) AS unread,
            SUM(CASE WHEN DATE(i.created_at) = CURDATE() THEN 1 ELSE 0 END) AS today,
            SUM(CASE WHEN DATE(i.created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) AS this_week
        FROM vcard_inquiries i
        JOIN vcards v ON v.id = i.vcard_id
        WHERE v.user_id = ?
    ");
    $stmt->execute([$userId]);
    $stats = $stmt->fetch();

    sendSuccess('Inquiries loaded', [
        'inquiries' => $inquiries,
        'total' => count($inquiries),
        'stats' => [
            'total' => (int)$stats['total'],
            'unread' => (int)$stats['unread'],
            'today' => (int)$stats['today'],
            'this_week' => (int)$stats['this_week']
        ]
    ]);

} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}

function timeAgo($datetime) {
    $diff = time() - strtotime($datetime);
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . ' min ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hr ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    return date('d M Y', strtotime($datetime));
}
