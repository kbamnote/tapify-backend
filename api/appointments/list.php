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
        ORDER BY a.appointment_date DESC, a.appointment_time DESC
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
        $a['source'] = 'vcard';

        $formatted[] = $a;
    }

    // --- website-builder appointments --------------------------------------
    // Booked from a published builder site. They live in their own table
    // (site_appointments) because they key to a site rather than a vCard, so
    // they are fetched separately and merged into the same list. Each row is
    // tagged source=site so the dashboard knows which endpoint to update.
    $siteTotal = 0; $siteToday = 0; $siteUpcoming = 0; $sitePending = 0;
    if ($vcardId <= 0) {   // a vCard filter can never match a builder booking
        try {
            $sc = []; $sp = [];
            if ($role !== 'admin')  { $sc[] = 's.user_id = ?';           $sp[] = $userId; }
            if ($status !== 'all')  { $sc[] = 'sa.status = ?';           $sp[] = $status; }
            if ($filter === 'today')        { $sc[] = 'sa.appointment_date = ?';  $sp[] = $today; }
            elseif ($filter === 'upcoming') { $sc[] = 'sa.appointment_date >= ?'; $sp[] = $today; }
            elseif ($filter === 'past')     { $sc[] = 'sa.appointment_date < ?';  $sp[] = $today; }
            $sw = $sc ? ('WHERE ' . implode(' AND ', $sc)) : '';

            $stmt = $pdo->prepare("
                SELECT sa.*, s.name AS site_name, s.slug AS site_slug
                FROM site_appointments sa
                JOIN sites s ON s.id = sa.site_id
                $sw
                ORDER BY sa.appointment_date DESC, sa.appointment_time DESC
            ");
            $stmt->execute($sp);
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $a) {
                $dObj = new DateTime($a['appointment_date']);
                $a['date_formatted'] = $dObj->format('M d, Y');
                $a['day_name'] = $dObj->format('l');
                $a['time_formatted'] = !empty($a['appointment_time'])
                    ? (new DateTime($a['appointment_time']))->format('g:i A') : 'N/A';
                $a['duration_minutes'] = 30;
                $a['is_read'] = (bool)$a['is_read'];
                $a['source'] = 'site';
                $a['vcard_id'] = 0;
                $a['vcard_name'] = $a['site_name'] ?: $a['site_slug'];
                $formatted[] = $a;
            }

            // Same-scope counts, added to the vCard stats below.
            $bw = ($role !== 'admin') ? 'WHERE s.user_id = ?' : '';
            $bp = ($role !== 'admin') ? [$userId] : [];
            $countSite = function ($extra, $params) use ($pdo, $bw) {
                if ($bw) {
                    $w = $bw . ($extra ? ' ' . $extra : '');
                } else {
                    $w = $extra ? 'WHERE ' . preg_replace('/^AND\s+/i', '', $extra) : '';
                }
                $st = $pdo->prepare("SELECT COUNT(*) FROM site_appointments sa JOIN sites s ON s.id = sa.site_id $w");
                $st->execute($params);
                return (int)$st->fetchColumn();
            };
            $siteTotal    = $countSite('', $bp);
            $siteToday    = $countSite('AND sa.appointment_date = ?', array_merge($bp, [$today]));
            $siteUpcoming = $countSite('AND sa.appointment_date >= ?', array_merge($bp, [$today]));
            $sitePending  = $countSite("AND sa.status = 'pending'", $bp);
        } catch (Exception $e) {
            // site_appointments not migrated yet — vCard appointments still work.
        }
    }

    // Newest first across both sources.
    usort($formatted, function ($x, $y) {
        $a = ($x['appointment_date'] ?? '') . ' ' . ($x['appointment_time'] ?? '');
        $b = ($y['appointment_date'] ?? '') . ' ' . ($y['appointment_time'] ?? '');
        return strcmp($b, $a);
    });

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
            // vCard + website-builder bookings combined.
            'total' => $total + $siteTotal,
            'today' => $todayCount + $siteToday,
            'upcoming' => $upcomingCount + $siteUpcoming,
            'pending' => $pendingCount + $sitePending
        ]
    ]);

} catch (Exception $e) {
    sendError('Failed to load appointments: ' . $e->getMessage(), 500);
}
