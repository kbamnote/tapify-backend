<?php
/**
 * TAPIFY - List Appointments API
 * GET /backend/api/appointments/list.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAuth();

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    // Auto-create table if it doesn't exist (migration safety)
    $pdo->exec("CREATE TABLE IF NOT EXISTS `vcard_appointments` (
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `vcard_id` INT(11) UNSIGNED NOT NULL,
        `customer_name` VARCHAR(150) NOT NULL,
        `customer_email` VARCHAR(150) DEFAULT NULL,
        `customer_phone` VARCHAR(20) NOT NULL,
        `service_name` VARCHAR(255) DEFAULT NULL,
        `appointment_date` DATE NOT NULL,
        `appointment_time` TIME NOT NULL,
        `duration_minutes` INT(11) DEFAULT 30,
        `customer_notes` TEXT DEFAULT NULL,
        `admin_notes` TEXT DEFAULT NULL,
        `status` ENUM('pending','confirmed','completed','cancelled','no_show') DEFAULT 'pending',
        `is_read` TINYINT(1) DEFAULT 0,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_vcard_id` (`vcard_id`),
        KEY `idx_appointment_date` (`appointment_date`),
        KEY `idx_status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    $role = $_SESSION['user_role'] ?? 'user';

    $filter = $_GET['filter'] ?? 'all';
    $status = $_GET['status'] ?? 'all';
    $vcardId = (int)($_GET['vcard_id'] ?? 0);

    $today = date('Y-m-d');

    // Build WHERE conditions
    $conditions = [];
    $params = [];

    // Role-based filtering
    if ($role !== 'admin') {
        $conditions[] = "v.user_id = ?";
        $params[] = $userId;
    }

    // Status filter
    if ($status !== 'all') {
        $conditions[] = "a.status = ?";
        $params[] = $status;
    }

    // vCard filter
    if ($vcardId > 0) {
        $conditions[] = "a.vcard_id = ?";
        $params[] = $vcardId;
    }

    // Date filter
    if ($filter === 'today') {
        $conditions[] = "a.appointment_date = ?";
        $params[] = $today;
    } elseif ($filter === 'upcoming') {
        $conditions[] = "a.appointment_date >= ?";
        $params[] = $today;
    } elseif ($filter === 'past') {
        $conditions[] = "a.appointment_date < ?";
        $params[] = $today;
    }

    $where = '';
    if (!empty($conditions)) {
        $where = 'WHERE ' . implode(' AND ', $conditions);
    }

    $stmt = $pdo->prepare("
        SELECT a.*, v.vcard_name 
        FROM vcard_appointments a
        LEFT JOIN vcards v ON v.id = a.vcard_id
        $where
        ORDER BY a.appointment_date ASC, a.appointment_time ASC
    ");
    $stmt->execute($params);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format appointments for frontend
    $formatted = [];
    foreach ($appointments as $a) {
        $dateObj = new DateTime($a['appointment_date']);
        $a['date_formatted'] = $dateObj->format('M d, Y');
        $a['day_name'] = $dateObj->format('l');

        // Format time
        if (!empty($a['appointment_time'])) {
            $timeObj = new DateTime($a['appointment_time']);
            $a['time_formatted'] = $timeObj->format('g:i A');
        } else {
            $a['time_formatted'] = 'N/A';
        }

        $a['duration_minutes'] = (int)($a['duration_minutes'] ?? 30);
        $a['is_read'] = (bool)$a['is_read'];
        $a['vcard_name'] = $a['vcard_name'] ?? 'Unknown';

        $formatted[] = $a;
    }

    // Calculate stats
    $statsParams = [];
    $statsWhere = '';
    if ($role !== 'admin') {
        $statsWhere = 'WHERE v.user_id = ?';
        $statsParams[] = $userId;
    }

    // Total
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM vcard_appointments a LEFT JOIN vcards v ON v.id = a.vcard_id $statsWhere");
    $stmt->execute($statsParams);
    $total = (int)$stmt->fetchColumn();

    // Today
    $todayWhere = $statsWhere ? "$statsWhere AND a.appointment_date = ?" : "WHERE a.appointment_date = ?";
    $todayParams = array_merge($statsParams, [$today]);
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM vcard_appointments a LEFT JOIN vcards v ON v.id = a.vcard_id $todayWhere");
    $stmt->execute($todayParams);
    $todayCount = (int)$stmt->fetchColumn();

    // Upcoming
    $upWhere = $statsWhere ? "$statsWhere AND a.appointment_date >= ?" : "WHERE a.appointment_date >= ?";
    $upParams = array_merge($statsParams, [$today]);
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM vcard_appointments a LEFT JOIN vcards v ON v.id = a.vcard_id $upWhere");
    $stmt->execute($upParams);
    $upcomingCount = (int)$stmt->fetchColumn();

    // Pending
    $pendWhere = $statsWhere ? "$statsWhere AND a.status = 'pending'" : "WHERE a.status = 'pending'";
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM vcard_appointments a LEFT JOIN vcards v ON v.id = a.vcard_id $pendWhere");
    $stmt->execute($statsParams);
    $pendingCount = (int)$stmt->fetchColumn();

    sendSuccess('Appointments retrieved', [
        'appointments' => $formatted,
        'stats' => [
            'total' => $total,
            'today' => $todayCount,
            'upcoming' => $upcomingCount,
            'pending' => $pendingCount
        ]
    ]);

} catch (Exception $e) {
    sendError('Failed to load appointments: ' . $e->getMessage(), 500);
}
