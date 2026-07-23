<?php
/**
 * Appointments booked on the logged-in customer's builder sites — powers the
 * dashboard's "Site Appointments" page.
 *
 *   GET  /api/sites/appointments.php                  -> all across my sites
 *   GET  /api/sites/appointments.php?site_id=12       -> one site
 *   GET  /api/sites/appointments.php?status=pending   -> filter by status
 *   GET  /api/sites/appointments.php?filter=upcoming  -> today | upcoming | past
 *   POST /api/sites/appointments.php {id, status}     -> update status
 *
 * Deliberately separate from /api/appointments/list.php: that one merges vCard
 * bookings and is left untouched. This reads ONLY site_appointments.
 *
 * Owner-only: every query is scoped to sites owned by the current user (staff
 * and admins may see all), so one customer can never read another's bookings.
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if (!isLoggedIn()) sendError('Not logged in', 401);

$userId = getCurrentUserId();
$staff  = isStaffOrAdmin();
$STATUSES = ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'];

try {
    $db = getDB();

    // The table ships in migration_site_appointments.sql. If it hasn't been run
    // yet, answer with an empty list instead of a 500 — the page then shows its
    // normal empty state rather than an error the customer can't act on.
    if (!$db->query("SHOW TABLES LIKE 'site_appointments'")->fetchColumn()) {
        sendSuccess('OK', [
            'appointments' => [],
            'counts' => ['all'=>0,'pending'=>0,'confirmed'=>0,'completed'=>0,'cancelled'=>0,'no_show'=>0],
            'stats'  => ['total'=>0,'today'=>0,'upcoming'=>0,'pending'=>0],
            'migrated' => false,
        ]);
    }

    /* ----------------------------------------------------- update status */
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $in     = getInput();
        $id     = (int)($in['id'] ?? 0);
        $status = trim((string)($in['status'] ?? ''));
        if ($id <= 0) sendError('id is required');
        if (!in_array($status, $STATUSES, true)) sendError('Invalid status');

        // Confirm the booking belongs to a site this user owns.
        $st = $db->prepare(
            "SELECT a.id FROM site_appointments a JOIN sites s ON s.id = a.site_id
              WHERE a.id = ?" . ($staff ? '' : ' AND s.user_id = ?')
        );
        $st->execute($staff ? [$id] : [$id, $userId]);
        if (!$st->fetchColumn()) sendError('Appointment not found', 404);

        $up = $db->prepare("UPDATE site_appointments SET status = ?, is_read = 1 WHERE id = ?");
        $up->execute([$status, $id]);
        sendSuccess('Appointment updated');
    }

    /* ------------------------------------------------------------- list */
    $today  = date('Y-m-d');
    $where  = [];
    $params = [];
    if (!$staff) { $where[] = 's.user_id = ?'; $params[] = $userId; }

    if (!empty($_GET['site_id'])) { $where[] = 'a.site_id = ?'; $params[] = (int)$_GET['site_id']; }
    if (!empty($_GET['status']) && in_array($_GET['status'], $STATUSES, true)) {
        $where[] = 'a.status = ?';
        $params[] = $_GET['status'];
    }
    $filter = $_GET['filter'] ?? 'all';
    if ($filter === 'today')         { $where[] = 'a.appointment_date = ?';  $params[] = $today; }
    elseif ($filter === 'upcoming')  { $where[] = 'a.appointment_date >= ?'; $params[] = $today; }
    elseif ($filter === 'past')      { $where[] = 'a.appointment_date < ?';  $params[] = $today; }

    $sql = "SELECT a.*, s.slug AS site_slug, s.name AS site_name
              FROM site_appointments a JOIN sites s ON s.id = a.site_id"
         . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
         . ' ORDER BY a.appointment_date DESC, a.appointment_time DESC LIMIT 500';

    $st = $db->prepare($sql);
    $st->execute($params);
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);

    // Pre-format for display so the page doesn't re-derive dates in JS.
    foreach ($rows as &$r) {
        $r['is_read'] = (bool)$r['is_read'];
        try {
            $d = new DateTime($r['appointment_date']);
            $r['date_formatted'] = $d->format('M d, Y');
            $r['day_name']       = $d->format('l');
        } catch (Exception $e) {
            $r['date_formatted'] = $r['appointment_date'];
            $r['day_name']       = '';
        }
        try {
            $r['time_formatted'] = !empty($r['appointment_time'])
                ? (new DateTime($r['appointment_time']))->format('g:i A') : '';
        } catch (Exception $e) {
            $r['time_formatted'] = (string)$r['appointment_time'];
        }
    }
    unset($r);

    $counts = ['all'=>0,'pending'=>0,'confirmed'=>0,'completed'=>0,'cancelled'=>0,'no_show'=>0];
    $stats  = ['total'=>0,'today'=>0,'upcoming'=>0,'pending'=>0];
    foreach ($rows as $r) {
        $counts['all']++;
        if (isset($counts[$r['status']])) $counts[$r['status']]++;
        $stats['total']++;
        if ($r['appointment_date'] === $today)  $stats['today']++;
        if ($r['appointment_date'] >= $today)   $stats['upcoming']++;
        if ($r['status'] === 'pending')         $stats['pending']++;
    }

    sendSuccess('OK', [
        'appointments' => $rows,
        'counts'       => $counts,
        'stats'        => $stats,
        'migrated'     => true,
    ]);
} catch (Exception $e) {
    error_log('site appointments: ' . $e->getMessage());
    sendError('Could not load appointments.', 500);
}
