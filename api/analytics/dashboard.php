<?php
/**
 * TAPIFY - Analytics Dashboard Data
 * GET /backend/api/analytics/dashboard.php
 *
 * Inquiries / appointments / orders / stores now come from the NEW website-builder
 * tables (site_inquiries, site_appointments, site_orders, site_views, sites) instead
 * of the retired vCard/WhatsApp tables. vCard stats (cards, views) are kept as-is.
 * Response shape is unchanged so the dashboard UI keeps working.
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

try {
    $pdo = getDB();
    $userId = getCurrentUserId();
    $admin = isAdmin();

    // Admin sees platform-wide; a user sees only their own.
    $vcardScopeSql = $admin ? '1=1' : 'user_id = ?';
    $vcardScopeParams = $admin ? [] : [$userId];
    // Site scope for the new tables (joined as `s`).
    $siteWhere  = $admin ? '1=1' : 's.user_id = ?';
    $siteParams = $admin ? []    : [$userId];

    $stats = [];

    // ---- vCards (unchanged) ----
    $stmt = $pdo->prepare("SELECT COUNT(*) as c FROM vcards WHERE $vcardScopeSql");
    $stmt->execute($vcardScopeParams);
    $stats['total_vcards'] = (int)$stmt->fetch()['c'];

    $stmt = $pdo->prepare("SELECT COUNT(*) as c FROM vcards WHERE $vcardScopeSql AND status = 1");
    $stmt->execute($vcardScopeParams);
    $stats['active_vcards'] = (int)$stmt->fetch()['c'];

    $stats['total_users'] = null;
    if ($admin) {
        $stmt = $pdo->query("SELECT COUNT(*) as c FROM users");
        $stats['total_users'] = (int)$stmt->fetch()['c'];
    }

    $stmt = $pdo->prepare("SELECT COALESCE(SUM(view_count), 0) as v FROM vcards WHERE $vcardScopeSql");
    $stmt->execute($vcardScopeParams);
    $stats['total_views'] = (int)$stmt->fetch()['v'];

    // ---- Websites (replaces "stores") + website views ----
    $stats['total_stores'] = 0;   // now = websites
    $stats['store_views']  = 0;   // now = website views
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as c FROM sites s WHERE $siteWhere");
        $stmt->execute($siteParams);
        $stats['total_stores'] = (int)$stmt->fetch()['c'];
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(v.views),0) as v FROM site_views v JOIN sites s ON s.id = v.site_id WHERE $siteWhere");
        $stmt->execute($siteParams);
        $stats['store_views'] = (int)$stmt->fetch()['v'];
    } catch (Exception $e) {}

    // ---- Inquiries (site_inquiries) ----
    $stats['total_inquiries'] = 0;
    $stats['unread_inquiries'] = 0;
    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as c, SUM(CASE WHEN i.is_read = 0 THEN 1 ELSE 0 END) as unread
            FROM site_inquiries i JOIN sites s ON s.id = i.site_id
            WHERE $siteWhere
        ");
        $stmt->execute($siteParams);
        $r = $stmt->fetch();
        $stats['total_inquiries'] = (int)$r['c'];
        $stats['unread_inquiries'] = (int)$r['unread'];
    } catch (Exception $e) {}

    // ---- Appointments (site_appointments) ----
    $stats['total_appointments'] = 0;
    $stats['today_appointments'] = 0;
    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as c,
                   SUM(CASE WHEN a.appointment_date = CURDATE() THEN 1 ELSE 0 END) as today
            FROM site_appointments a JOIN sites s ON s.id = a.site_id
            WHERE $siteWhere
        ");
        $stmt->execute($siteParams);
        $r = $stmt->fetch();
        $stats['total_appointments'] = (int)$r['c'];
        $stats['today_appointments'] = (int)$r['today'];
    } catch (Exception $e) {}

    // ---- Orders + revenue (site_orders). Prices are free text, so parse a number. ----
    $stats['total_orders'] = 0;
    $stats['total_revenue'] = 0;
    $stats['pending_orders'] = 0;
    // A DECIMAL(12,2) of the digits in `price` × quantity, summed over completed orders.
    $revenueExpr = "CAST(NULLIF(REGEXP_REPLACE(COALESCE(o.price,''), '[^0-9.]', ''), '') AS DECIMAL(12,2)) * GREATEST(COALESCE(o.quantity,1),1)";
    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as c,
                   COALESCE(SUM(CASE WHEN o.status = 'completed' THEN $revenueExpr ELSE 0 END),0) as revenue,
                   SUM(CASE WHEN o.status = 'new' THEN 1 ELSE 0 END) as pending
            FROM site_orders o JOIN sites s ON s.id = o.site_id
            WHERE $siteWhere
        ");
        $stmt->execute($siteParams);
        $r = $stmt->fetch();
        $stats['total_orders'] = (int)$r['c'];
        $stats['total_revenue'] = (float)$r['revenue'];
        $stats['pending_orders'] = (int)$r['pending'];
    } catch (Exception $e) {}

    // ========== CHART 1+2: Inquiries & Appointments last 30 days ==========
    $inqMap = [];
    $apptMap = [];
    try {
        $stmt = $pdo->prepare("
            SELECT DATE(i.created_at) as date, COUNT(*) as count
            FROM site_inquiries i JOIN sites s ON s.id = i.site_id
            WHERE $siteWhere AND i.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY DATE(i.created_at)
        ");
        $stmt->execute($siteParams);
        foreach ($stmt->fetchAll() as $d) $inqMap[$d['date']] = (int)$d['count'];
    } catch (Exception $e) {}
    try {
        $stmt = $pdo->prepare("
            SELECT DATE(a.created_at) as date, COUNT(*) as count
            FROM site_appointments a JOIN sites s ON s.id = a.site_id
            WHERE $siteWhere AND a.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY DATE(a.created_at)
        ");
        $stmt->execute($siteParams);
        foreach ($stmt->fetchAll() as $d) $apptMap[$d['date']] = (int)$d['count'];
    } catch (Exception $e) {}

    $last30Days = [];
    for ($i = 29; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $last30Days[] = [
            'date' => $date,
            'label' => date('d M', strtotime($date)),
            'inquiries' => $inqMap[$date] ?? 0,
            'appointments' => $apptMap[$date] ?? 0
        ];
    }

    // ========== CHART 3: Order status distribution (site_orders) ==========
    $orderStatuses = [];
    try {
        $stmt = $pdo->prepare("
            SELECT o.status, COUNT(*) as count
            FROM site_orders o JOIN sites s ON s.id = o.site_id
            WHERE $siteWhere
            GROUP BY o.status
        ");
        $stmt->execute($siteParams);
        $orderStatuses = $stmt->fetchAll();
        foreach ($orderStatuses as &$os) $os['count'] = (int)$os['count'];
        unset($os);
    } catch (Exception $e) {}

    // ========== CHART 4: Top vCards by Views (unchanged) ==========
    $stmt = $pdo->prepare("
        SELECT vcard_name, view_count, url_alias
        FROM vcards
        WHERE $vcardScopeSql AND status = 1
        ORDER BY view_count DESC
        LIMIT 5
    ");
    $stmt->execute($vcardScopeParams);
    $topVcards = $stmt->fetchAll();
    foreach ($topVcards as &$v) $v['view_count'] = (int)$v['view_count'];
    unset($v);

    // ========== CHART 5: Revenue last 7 days (site_orders, completed) ==========
    $revenueData = [];
    try {
        $stmt = $pdo->prepare("
            SELECT DATE(o.created_at) as date, COALESCE(SUM($revenueExpr),0) as revenue
            FROM site_orders o JOIN sites s ON s.id = o.site_id
            WHERE $siteWhere AND o.status = 'completed'
              AND o.created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            GROUP BY DATE(o.created_at)
        ");
        $stmt->execute($siteParams);
        $revMap = [];
        foreach ($stmt->fetchAll() as $r) $revMap[$r['date']] = (float)$r['revenue'];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $revenueData[] = ['date' => $date, 'label' => date('D', strtotime($date)), 'revenue' => $revMap[$date] ?? 0];
        }
    } catch (Exception $e) {}

    // ========== RECENT ACTIVITY FEED (new tables) ==========
    $activities = [];
    try {
        $stmt = $pdo->prepare("
            SELECT 'inquiry' as type, i.id, i.name as title, i.message as detail,
                   s.name as source, i.created_at
            FROM site_inquiries i JOIN sites s ON s.id = i.site_id
            WHERE $siteWhere ORDER BY i.created_at DESC LIMIT 5
        ");
        $stmt->execute($siteParams);
        foreach ($stmt->fetchAll() as $row) $activities[] = $row;
    } catch (Exception $e) {}
    try {
        $stmt = $pdo->prepare("
            SELECT 'appointment' as type, a.id, a.customer_name as title,
                   CONCAT(DATE_FORMAT(a.appointment_date, '%d %b'), ' at ', TIME_FORMAT(a.appointment_time, '%h:%i %p')) as detail,
                   s.name as source, a.created_at
            FROM site_appointments a JOIN sites s ON s.id = a.site_id
            WHERE $siteWhere ORDER BY a.created_at DESC LIMIT 5
        ");
        $stmt->execute($siteParams);
        foreach ($stmt->fetchAll() as $row) $activities[] = $row;
    } catch (Exception $e) {}
    try {
        $stmt = $pdo->prepare("
            SELECT 'order' as type, o.id, o.customer_name as title,
                   CONCAT(COALESCE(o.item_title,'Order'), ' - ', o.status) as detail,
                   s.name as source, o.created_at
            FROM site_orders o JOIN sites s ON s.id = o.site_id
            WHERE $siteWhere ORDER BY o.created_at DESC LIMIT 5
        ");
        $stmt->execute($siteParams);
        foreach ($stmt->fetchAll() as $row) $activities[] = $row;
    } catch (Exception $e) {}

    usort($activities, function ($a, $b) { return strtotime($b['created_at']) - strtotime($a['created_at']); });
    $activities = array_slice($activities, 0, 10);
    foreach ($activities as &$act) $act['time_ago'] = timeAgo($act['created_at']);
    unset($act);

    // ========== INSIGHTS ==========
    $insights = [];
    if (!empty($topVcards) && $topVcards[0]['view_count'] > 0) {
        $insights[] = ['icon' => 'fa-fire', 'color' => '#f59e0b', 'title' => 'Top Performer',
            'message' => $topVcards[0]['vcard_name'] . ' has ' . number_format($topVcards[0]['view_count']) . ' views!'];
    }
    if ($stats['store_views'] > 0 && $stats['total_inquiries'] > 0) {
        $rate = round(($stats['total_inquiries'] / max(1, $stats['store_views'])) * 100, 1);
        $insights[] = ['icon' => 'fa-chart-line', 'color' => '#10b981', 'title' => 'Conversion Rate',
            'message' => $rate . '% of website views result in inquiries'];
    }
    if ($stats['unread_inquiries'] > 0) {
        $insights[] = ['icon' => 'fa-bell', 'color' => '#ef4444', 'title' => 'Action Needed',
            'message' => $stats['unread_inquiries'] . ' unread ' . ($stats['unread_inquiries'] === 1 ? 'inquiry' : 'inquiries') . ' to respond to'];
    }
    if ($stats['today_appointments'] > 0) {
        $insights[] = ['icon' => 'fa-calendar-day', 'color' => '#8338ec', 'title' => "Today's Schedule",
            'message' => $stats['today_appointments'] . ' appointment' . ($stats['today_appointments'] > 1 ? 's' : '') . ' scheduled today'];
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
