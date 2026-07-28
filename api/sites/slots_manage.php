<?php
/**
 * Weekly appointment availability for a builder site (dashboard side).
 *
 *   GET  /api/sites/slots_manage.php?site_id=12
 *        -> { schedule: [{day_of_week,start_time,end_time}, ...], settings: {...}, sites: [...] }
 *
 *   POST /api/sites/slots_manage.php
 *        { action:'save_week', site_id:12,
 *          schedule:[{day:1,start:'10:00',end:'13:00'}, ...],
 *          settings:{slot_minutes:30, lead_minutes:120, horizon_days:60} }
 *
 * Mirrors api/appointments/slots_manage.php (the vCard version) but scoped to a
 * site. Owner-only: a client may only touch a site assigned to them; staff and
 * admins may manage any.
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if (!isLoggedIn()) sendError('Not logged in', 401);

$userId = getCurrentUserId();
$staff  = isStaffOrAdmin();

/** True when the current user may manage this site's availability. */
function canManageSite(PDO $db, int $siteId, int $userId, bool $staff): bool
{
    if ($siteId <= 0) return false;
    if ($staff) {
        $st = $db->prepare("SELECT id FROM sites WHERE id = ? LIMIT 1");
        $st->execute([$siteId]);
    } else {
        $st = $db->prepare("SELECT id FROM sites WHERE id = ? AND user_id = ? LIMIT 1");
        $st->execute([$siteId, $userId]);
    }
    return (bool)$st->fetchColumn();
}

const SLOT_DEFAULTS = ['slot_minutes' => 30, 'lead_minutes' => 120, 'horizon_days' => 60, 'capacity' => 1];

try {
    $db = getDB();

    // Tables ship in migration_site_schedule.sql. Until it's run, answer with an
    // empty schedule rather than a 500 so the page shows its normal empty state.
    $ready = (bool)$db->query("SHOW TABLES LIKE 'site_schedule'")->fetchColumn();

    /* ------------------------------------------------------------- read */
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // The site picker in the modal — only sites this user may manage.
        if ($staff) {
            $ss = $db->query("SELECT id, name, slug FROM sites ORDER BY name");
        } else {
            $ss = $db->prepare("SELECT id, name, slug FROM sites WHERE user_id = ? ORDER BY name");
            $ss->execute([$userId]);
        }
        $sites = $ss->fetchAll(PDO::FETCH_ASSOC);

        $siteId = (int)($_GET['site_id'] ?? 0);
        if ($siteId <= 0 && $sites) $siteId = (int)$sites[0]['id'];

        if ($siteId > 0 && !canManageSite($db, $siteId, $userId, $staff)) {
            sendError('You do not have access to that website.', 403);
        }

        $schedule = [];
        $settings = SLOT_DEFAULTS;
        if ($ready && $siteId > 0) {
            $st = $db->prepare("SELECT day_of_week, start_time, end_time FROM site_schedule WHERE site_id = ? ORDER BY day_of_week, start_time");
            $st->execute([$siteId]);
            $schedule = $st->fetchAll(PDO::FETCH_ASSOC);

            $sq = $db->prepare("SELECT slot_minutes, lead_minutes, horizon_days, capacity FROM site_schedule_settings WHERE site_id = ? LIMIT 1");
            $sq->execute([$siteId]);
            if ($row = $sq->fetch(PDO::FETCH_ASSOC)) {
                $settings = array_map('intval', $row);
            }
        }

        sendSuccess('OK', [
            'site_id'  => $siteId,
            'sites'    => $sites,
            'schedule' => $schedule,
            'settings' => $settings,
            'migrated' => $ready,
        ]);
    }

    /* ------------------------------------------------------------ write */
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!$ready) sendError('Run migration_site_schedule.sql before saving availability.', 400);

        $in     = getInput();
        $action = trim((string)($in['action'] ?? ''));
        $siteId = (int)($in['site_id'] ?? 0);

        if ($action !== 'save_week') sendError('Unknown action');
        if (!canManageSite($db, $siteId, $userId, $staff)) sendError('You do not have access to that website.', 403);

        $rows = [];
        foreach ((array)($in['schedule'] ?? []) as $slot) {
            $day   = (int)($slot['day'] ?? -1);
            $start = trim((string)($slot['start'] ?? ''));
            $end   = trim((string)($slot['end'] ?? ''));
            if ($day < 0 || $day > 6 || $start === '' || $end === '') continue;

            $s = date('H:i:s', strtotime($start));
            $e = date('H:i:s', strtotime($end));
            // A range that ends at or before it starts would generate no slots and
            // silently look like "closed" — reject it so the owner can fix it.
            if ($e <= $s) sendError('An end time must be after its start time (' . $start . ' – ' . $end . ').');
            $rows[] = [$siteId, $day, $s, $e];
        }

        $st = $in['settings'] ?? [];
        $slotMin  = max(5,  min(240,  (int)($st['slot_minutes'] ?? SLOT_DEFAULTS['slot_minutes'])));
        $leadMin  = max(0,  min(10080,(int)($st['lead_minutes'] ?? SLOT_DEFAULTS['lead_minutes'])));
        $horizon  = max(1,  min(365,  (int)($st['horizon_days'] ?? SLOT_DEFAULTS['horizon_days'])));
        $capacity = max(1,  min(200,  (int)($st['capacity']     ?? SLOT_DEFAULTS['capacity'])));

        $db->beginTransaction();
        try {
            $db->prepare("DELETE FROM site_schedule WHERE site_id = ?")->execute([$siteId]);
            if ($rows) {
                $ins = $db->prepare("INSERT INTO site_schedule (site_id, day_of_week, start_time, end_time) VALUES (?,?,?,?)");
                foreach ($rows as $r) $ins->execute($r);
            }
            $db->prepare(
                "INSERT INTO site_schedule_settings (site_id, slot_minutes, lead_minutes, horizon_days, capacity)
                 VALUES (?,?,?,?,?)
                 ON DUPLICATE KEY UPDATE slot_minutes = VALUES(slot_minutes),
                                         lead_minutes = VALUES(lead_minutes),
                                         horizon_days = VALUES(horizon_days),
                                         capacity     = VALUES(capacity)"
            )->execute([$siteId, $slotMin, $leadMin, $horizon, $capacity]);
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        sendSuccess('Availability saved', ['ranges' => count($rows)]);
    }

    sendError('Method not allowed', 405);
} catch (Exception $e) {
    error_log('site slots_manage: ' . $e->getMessage());
    sendError('Could not load or save availability.', 500);
}
