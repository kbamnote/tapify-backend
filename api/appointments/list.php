<?php
/**
 * TAPIFY - Appointments List
 * GET /backend/api/appointments/list.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    $vcardId = (int)($_GET['vcard_id'] ?? 0);
    $status = $_GET['status'] ?? 'all';
    $filter = $_GET['filter'] ?? 'all'; // all, today, upcoming, past
    $search = trim($_GET['search'] ?? '');

    $sql = "SELECT a.*, v.vcard_name, v.url_alias AS vcard_url_alias
            FROM vcard_appointments a
            JOIN vcards v ON v.id = a.vcard_id
            WHERE v.user_id = ?";
    $params = [$userId];

    if ($vcardId > 0) {
        $sql .= " AND a.vcard_id = ?";
        $params[] = $vcardId;
    }

    if ($status !== 'all' && in_array($status, ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'])) {
        $sql .= " AND a.status = ?";
        $params[] = $status;
    }

    if ($filter === 'today') {
        $sql .= " AND a.appointment_date = CURDATE()";
    } elseif ($filter === 'upcoming') {
        $sql .= " AND a.appointment_date >= CURDATE() AND a.status NOT IN ('cancelled', 'completed', 'no_show')";
    } elseif ($filter === 'past') {
        $sql .= " AND a.appointment_date < CURDATE()";
    }

    if (!empty($search)) {
        $sql .= " AND (a.customer_name LIKE ? OR a.customer_phone LIKE ? OR a.customer_email LIKE ? OR a.service_name LIKE ?)";
        $sp = '%' . $search . '%';
        $params[] = $sp; $params[] = $sp; $params[] = $sp; $params[] = $sp;
    }

    $sql .= " ORDER BY a.appointment_date ASC, a.appointment_time ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $appointments = $stmt->fetchAll();

    foreach ($appointments as &$a) {
        $a['is_read'] = (bool)$a['is_read'];
        $a['duration_minutes'] = (int)$a['duration_minutes'];
        $a['date_formatted'] = date('d M Y', strtotime($a['appointment_date']));
        $a['time_formatted'] = date('h:i A', strtotime($a['appointment_time']));
        $a['day_name'] = date('l', strtotime($a['appointment_date']));
        $a['created_at_formatted'] = date('d M Y, h:i A', strtotime($a['created_at']));

        // Determine if upcoming/today/past
        $today = date('Y-m-d');
        if ($a['appointment_date'] === $today) $a['date_status'] = 'today';
        elseif ($a['appointment_date'] > $today) $a['date_status'] = 'upcoming';
        else $a['date_status'] = 'past';
    }

    // Stats
    $stmt = $pdo->prepare("
        SELECT
            COUNT(*) AS total,
            SUM(CASE WHEN a.appointment_date = CURDATE() THEN 1 ELSE 0 END) AS today,
            SUM(CASE WHEN a.appointment_date >= CURDATE() AND a.status NOT IN ('cancelled', 'completed', 'no_show') THEN 1 ELSE 0 END) AS upcoming,
            SUM(CASE WHEN a.status = 'pending' THEN 1 ELSE 0 END) AS pending,
            SUM(CASE WHEN a.is_read = 0 THEN 1 ELSE 0 END) AS unread
        FROM vcard_appointments a
        JOIN vcards v ON v.id = a.vcard_id
        WHERE v.user_id = ?
    ");
    $stmt->execute([$userId]);
    $stats = $stmt->fetch();

    sendSuccess('Appointments loaded', [
        'appointments' => $appointments,
        'total' => count($appointments),
        'stats' => [
            'total' => (int)$stats['total'],
            'today' => (int)$stats['today'],
            'upcoming' => (int)$stats['upcoming'],
            'pending' => (int)$stats['pending'],
            'unread' => (int)$stats['unread']
        ]
    ]);

} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
