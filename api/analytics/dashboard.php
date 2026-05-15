<?php
/**
 * TAPIFY - Analytics Dashboard Data
 * GET /backend/api/analytics/dashboard.php
 * Returns all stats + chart data for dashboard
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    // ========== KPI STATS ==========
    $stats = [];

    // Total vCards
    $stmt = $pdo->prepare("SELECT COUNT(*) as c FROM vcards WHERE user_id = ?");
    $stmt->execute([$userId]);
    $stats['total_vcards'] = (int)$stmt->fetch()['c'];

    // Active vCards
    $stmt = $pdo->prepare("SELECT COUNT(*) as c FROM vcards WHERE user_id = ? AND status = 1");
    $stmt->execute([$userId]);
    $stats['active_vcards'] = (int)$stmt->fetch()['c'];

    // Total vCard views
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(view_count), 0) as v FROM vcards WHERE user_id = ?");
    $stmt->execute([$userId]);
    $stats['total_views'] = (int)$stmt->fetch()['v'];

    // Total Stores (if Phase 7 installed)
    $stats['total_stores'] = 0;
    $stats['store_views'] = 0;
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as c, COALESCE(SUM(view_count), 0) as v FROM whatsapp_stores WHERE user_id = ?");
        $stmt->execute([$userId]);
        $r = $stmt->fetch();
        $stats['total_stores'] = (int)$r['c'];
        $stats['store_views'] = (int)$r['v'];
    } catch (Exception $e) {}

    // Total Inquiries
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as c, SUM(CASE WHEN i.is_read = 0 THEN 1 ELSE 0 END) as unread
        FROM vcard_inquiries i JOIN vcards v ON v.id = i.vcard_id
        WHERE v.user_id = ?
    ");
    $stmt->execute([$userId]);
    $r = $stmt->fetch();
    $stats['total_inquiries'] = (int)$r['c'];
    $stats['unread_inquiries'] = (int)$r['unread'];

    // Total Appointments (if Phase 9A installed)
    $stats['total_appointments'] = 0;
    $stats['today_appointments'] = 0;
    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as c,
                   SUM(CASE WHEN a.appointment_date = CURDATE() THEN 1 ELSE 0 END) as today
            FROM vcard_appointments a JOIN vcards v ON v.id = a.vcard_id
            WHERE v.user_id = ?
        ");
        $stmt->execute([$userId]);
        $r = $stmt->fetch();
        $stats['total_appointments'] = (int)$r['c'];
        $stats['today_appointments'] = (int)$r['today'];
    } catch (Exception $e) {}

    // Total Orders + Revenue (if Phase 7 installed)
    $stats['total_orders'] = 0;
    $stats['total_revenue'] = 0;
    $stats['pending_orders'] = 0;
    try {
        $stmt = $pdo->prepare("
            SELECT
                COUNT(*) as c,
                SUM(CASE WHEN o.status = 'delivered' THEN o.total_amount ELSE 0 END) as revenue,
                SUM(CASE WHEN o.status = 'pending' THEN 1 ELSE 0 END) as pending
            FROM whatsapp_store_orders o
            JOIN whatsapp_stores s ON s.id = o.store_id
            WHERE s.user_id = ?
        ");
        $stmt->execute([$userId]);
        $r = $stmt->fetch();
        $stats['total_orders'] = (int)$r['c'];
        $stats['total_revenue'] = (float)$r['revenue'];
        $stats['pending_orders'] = (int)$r['pending'];
    } catch (Exception $e) {}

    // ========== CHART 1: Inquiries last 30 days ==========
    $stmt = $pdo->prepare("
        SELECT DATE(i.created_at) as date, COUNT(*) as count
        FROM vcard_inquiries i JOIN vcards v ON v.id = i.vcard_id
        WHERE v.user_id = ? AND i.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        GROUP BY DATE(i.created_at)
        ORDER BY date ASC
    ");
    $stmt->execute([$userId]);
    $inquiriesData = $stmt->fetchAll();

    // ========== CHART 2: Appointments last 30 days ==========
    $appointmentsData = [];
    try {
        $stmt = $pdo->prepare("
            SELECT DATE(a.created_at) as date, COUNT(*) as count
            FROM vcard_appointments a JOIN vcards v ON v.id = a.vcard_id
            WHERE v.user_id = ? AND a.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY DATE(a.created_at)
            ORDER BY date ASC
        ");
        $stmt->execute([$userId]);
        $appointmentsData = $stmt->fetchAll();
    } catch (Exception $e) {}

    // Build last 30 days array
    $last30Days = [];
    $inqMap = [];
    $apptMap = [];
    foreach ($inquiriesData as $d) $inqMap[$d['date']] = (int)$d['count'];
    foreach ($appointmentsData as $d) $apptMap[$d['date']] = (int)$d['count'];

    for ($i = 29; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $last30Days[] = [
            'date' => $date,
            'label' => date('d M', strtotime($date)),
            'inquiries' => $inqMap[$date] ?? 0,
            'appointments' => $apptMap[$date] ?? 0
        ];
    }

    // ========== CHART 3: Orders Status Distribution ==========
    $orderStatuses = [];
    try {
        $stmt = $pdo->prepare("
            SELECT o.status, COUNT(*) as count
            FROM whatsapp_store_orders o
            JOIN whatsapp_stores s ON s.id = o.store_id
            WHERE s.user_id = ?
            GROUP BY o.status
        ");
        $stmt->execute([$userId]);
        $orderStatuses = $stmt->fetchAll();
        foreach ($orderStatuses as &$os) $os['count'] = (int)$os['count'];
    } catch (Exception $e) {}

    // ========== CHART 4: Top vCards by Views ==========
    $stmt = $pdo->prepare("
        SELECT vcard_name, view_count, url_alias
        FROM vcards
        WHERE user_id = ? AND status = 1
        ORDER BY view_count DESC
        LIMIT 5
    ");
    $stmt->execute([$userId]);
    $topVcards = $stmt->fetchAll();
    foreach ($topVcards as &$v) $v['view_count'] = (int)$v['view_count'];

    // ========== CHART 5: Revenue last 7 days ==========
    $revenueData = [];
    try {
        $stmt = $pdo->prepare("
            SELECT DATE(o.created_at) as date, SUM(o.total_amount) as revenue
            FROM whatsapp_store_orders o
            JOIN whatsapp_stores s ON s.id = o.store_id
            WHERE s.user_id = ?
              AND o.status = 'delivered'
              AND o.created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            GROUP BY DATE(o.created_at)
            ORDER BY date ASC
        ");
        $stmt->execute([$userId]);
        $revData = $stmt->fetchAll();

        $revMap = [];
        foreach ($revData as $r) $revMap[$r['date']] = (float)$r['revenue'];

        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $revenueData[] = [
                'date' => $date,
                'label' => date('D', strtotime($date)),
                'revenue' => $revMap[$date] ?? 0
            ];
        }
    } catch (Exception $e) {}

    // ========== RECENT ACTIVITY FEED ==========
    $activities = [];

    // Recent inquiries
    try {
        $stmt = $pdo->prepare("
            SELECT 'inquiry' as type, i.id, i.name as title, i.message as detail,
                   v.vcard_name as source, i.created_at
            FROM vcard_inquiries i JOIN vcards v ON v.id = i.vcard_id
            WHERE v.user_id = ?
            ORDER BY i.created_at DESC
            LIMIT 5
        ");
        $stmt->execute([$userId]);
        foreach ($stmt->fetchAll() as $row) $activities[] = $row;
    } catch (Exception $e) {}

    // Recent appointments
    try {
        $stmt = $pdo->prepare("
            SELECT 'appointment' as type, a.id, a.customer_name as title,
                   CONCAT(DATE_FORMAT(a.appointment_date, '%d %b'), ' at ', TIME_FORMAT(a.appointment_time, '%h:%i %p')) as detail,
                   v.vcard_name as source, a.created_at
            FROM vcard_appointments a JOIN vcards v ON v.id = a.vcard_id
            WHERE v.user_id = ?
            ORDER BY a.created_at DESC
            LIMIT 5
        ");
        $stmt->execute([$userId]);
        foreach ($stmt->fetchAll() as $row) $activities[] = $row;
    } catch (Exception $e) {}

    // Recent orders
    try {
        $stmt = $pdo->prepare("
            SELECT 'order' as type, o.id, o.customer_name as title,
                   CONCAT('₹', FORMAT(o.total_amount, 2), ' - ', o.status) as detail,
                   s.store_name as source, o.created_at
            FROM whatsapp_store_orders o
            JOIN whatsapp_stores s ON s.id = o.store_id
            WHERE s.user_id = ?
            ORDER BY o.created_at DESC
            LIMIT 5
        ");
        $stmt->execute([$userId]);
        foreach ($stmt->fetchAll() as $row) $activities[] = $row;
    } catch (Exception $e) {}

    // Sort by created_at DESC, take top 10
    usort($activities, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    $activities = array_slice($activities, 0, 10);

    // Format activities
    foreach ($activities as &$act) {
        $act['time_ago'] = timeAgo($act['created_at']);
    }

    // ========== INSIGHTS ==========
    $insights = [];

    // Most viewed vCard
    if (!empty($topVcards) && $topVcards[0]['view_count'] > 0) {
        $insights[] = [
            'icon' => 'fa-fire',
            'color' => '#f59e0b',
            'title' => 'Top Performer',
            'message' => $topVcards[0]['vcard_name'] . ' has ' . number_format($topVcards[0]['view_count']) . ' views!'
        ];
    }

    // Conversion rate
    if ($stats['total_views'] > 0 && $stats['total_inquiries'] > 0) {
        $rate = round(($stats['total_inquiries'] / $stats['total_views']) * 100, 1);
        $insights[] = [
            'icon' => 'fa-chart-line',
            'color' => '#10b981',
            'title' => 'Conversion Rate',
            'message' => $rate . '% of vCard views result in inquiries'
        ];
    }

    // Pending action
    if ($stats['unread_inquiries'] > 0) {
        $insights[] = [
            'icon' => 'fa-bell',
            'color' => '#ef4444',
            'title' => 'Action Needed',
            'message' => $stats['unread_inquiries'] . ' unread ' . ($stats['unread_inquiries'] === 1 ? 'inquiry' : 'inquiries') . ' to respond to'
        ];
    }

    // Today's appointments
    if ($stats['today_appointments'] > 0) {
        $insights[] = [
            'icon' => 'fa-calendar-day',
            'color' => '#8338ec',
            'title' => "Today's Schedule",
            'message' => $stats['today_appointments'] . ' appointment' . ($stats['today_appointments'] > 1 ? 's' : '') . ' scheduled today'
        ];
    }

    sendSuccess('Analytics loaded', [
        'stats' => $stats,
        'charts' => [
            'last_30_days' => $last30Days,
            'order_statuses' => $orderStatuses,
            'top_vcards' => $topVcards,
            'revenue_7days' => $revenueData
        ],
        'activities' => $activities,
        'insights' => $insights
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
