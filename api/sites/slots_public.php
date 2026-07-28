<?php
/**
 * GET /api/sites/slots_public.php?site=<slug>&date=YYYY-MM-DD   (PUBLIC)
 *
 * Bookable slots for one day on a published builder site:
 *
 *   the day's open ranges  ->  sliced into slots  ->  minus already-booked
 *                          ->  minus anything inside the notice window
 *
 * -> { slots: [{value:"14:00", label:"02:00 PM"}, ...], configured: bool }
 *
 * `configured` false means the owner has not set any availability yet. The page
 * then falls back to its old open list rather than showing a booking form with
 * nothing selectable, which would look broken on every site created before this.
 *
 * Mirrors api/appointments/slots_public.php (the vCard version), keyed to a site
 * slug instead of a vcard_id.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../builder/lib/SiteRepo.php';

$slug = strtolower(trim((string)($_GET['site'] ?? '')));
$date = trim((string)($_GET['date'] ?? ''));

if ($slug === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    sendError('A site and a date (YYYY-MM-DD) are required.');
}
// Reject a date that isn't real (2026-02-31 parses but isn't a day).
[$y, $m, $d] = array_map('intval', explode('-', $date));
if (!checkdate($m, $d, $y)) sendError('That date is not valid.');

try {
    $site = SiteRepo::findBySlug($slug);
    if (!$site || ($site['status'] ?? '') === 'disabled') sendError('This website is not available.', 404);
    $siteId = (int)$site['id'];

    $db = getDB();

    if (!$db->query("SHOW TABLES LIKE 'site_schedule'")->fetchColumn()) {
        sendSuccess('OK', ['slots' => [], 'configured' => false]);
    }

    // --- booking rules -----------------------------------------------------
    $slotMin = 30; $leadMin = 120; $horizon = 60; $capacity = 1;
    // capacity landed after the first release of this table, so select it
    // separately — an older deployment without the column must still work.
    $sq = $db->prepare("SELECT slot_minutes, lead_minutes, horizon_days FROM site_schedule_settings WHERE site_id = ? LIMIT 1");
    $sq->execute([$siteId]);
    if ($cfg = $sq->fetch(PDO::FETCH_ASSOC)) {
        $slotMin = max(5, (int)$cfg['slot_minutes']);
        $leadMin = max(0, (int)$cfg['lead_minutes']);
        $horizon = max(1, (int)$cfg['horizon_days']);
    }
    try {
        $cq = $db->prepare("SELECT capacity FROM site_schedule_settings WHERE site_id = ? LIMIT 1");
        $cq->execute([$siteId]);
        $capacity = max(1, (int)$cq->fetchColumn());
    } catch (Exception $eCap) {
        $capacity = 1;   // column not migrated yet
    }

    // --- the day's open ranges --------------------------------------------
    $dow = (int)date('w', strtotime($date));
    $st  = $db->prepare("SELECT start_time, end_time FROM site_schedule WHERE site_id = ? AND day_of_week = ? ORDER BY start_time");
    $st->execute([$siteId, $dow]);
    $ranges = $st->fetchAll(PDO::FETCH_ASSOC);

    // Has the owner set up ANY availability at all? Distinguishes "closed on a
    // Sunday" (configured, no slots) from "never set this up" (not configured).
    $any = $db->prepare("SELECT COUNT(*) FROM site_schedule WHERE site_id = ?");
    $any->execute([$siteId]);
    $configured = (int)$any->fetchColumn() > 0;

    if (!$configured) sendSuccess('OK', ['slots' => [], 'configured' => false]);

    // Outside the booking window -> configured, but nothing offered.
    $today    = new DateTime('today');
    $wanted   = new DateTime($date);
    $lastDay  = (new DateTime('today'))->modify('+' . $horizon . ' days');
    if ($wanted < $today || $wanted > $lastDay) {
        sendSuccess('OK', ['slots' => [], 'configured' => true]);
    }

    // --- slice ranges into slots ------------------------------------------
    $generated = [];
    $step = $slotMin * 60;
    foreach ($ranges as $r) {
        $cur = strtotime($date . ' ' . $r['start_time']);
        $end = strtotime($date . ' ' . $r['end_time']);
        // Only whole slots that finish inside the range.
        while ($cur + $step <= $end) {
            $generated[date('H:i', $cur)] = true;
            $cur += $step;
        }
    }
    $generated = array_keys($generated);
    sort($generated);

    // --- how many are already booked in each slot --------------------------
    // Counted, not just flagged: a slot stays open until it reaches capacity,
    // so a salon with three chairs can take three bookings at 10:00.
    //
    // Must match appointment-submit.php's guard exactly. It counts anything not
    // 'cancelled', so a no-show still occupies its place — if this were looser
    // we would offer a time the submit endpoint then refuses, and the visitor
    // would hit "that time has just been taken".
    $taken = [];
    if ($db->query("SHOW TABLES LIKE 'site_appointments'")->fetchColumn()) {
        $bq = $db->prepare(
            "SELECT appointment_time, COUNT(*) AS n FROM site_appointments
              WHERE site_id = ? AND appointment_date = ? AND status != 'cancelled'
              GROUP BY appointment_time"
        );
        $bq->execute([$siteId, $date]);
        foreach ($bq->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $taken[date('H:i', strtotime($row['appointment_time']))] = (int)$row['n'];
        }
    }

    // --- drop anything too soon, or already full ---------------------------
    $earliest = time() + ($leadMin * 60);

    $slots = [];
    foreach ($generated as $hm) {
        $used = $taken[$hm] ?? 0;
        if ($used >= $capacity) continue;                          // full
        if (strtotime($date . ' ' . $hm) < $earliest) continue;    // too soon
        $slots[] = [
            'value' => $hm,
            'label' => date('h:i A', strtotime($hm)),
            'left'  => $capacity - $used,   // lets the page show "2 left"
        ];
    }

    sendSuccess('OK', ['slots' => $slots, 'configured' => true, 'capacity' => $capacity]);
} catch (Exception $e) {
    error_log('site slots_public: ' . $e->getMessage());
    sendError('Could not load available times.', 500);
}
