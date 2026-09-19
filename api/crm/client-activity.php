<?php
/**
 * TAPIFY - CRM bridge: what a customer did, in plain English, day by day.
 * GET /api/crm/client-activity.php?user_id=123&days=7
 * Header: X-CRM-API-Key
 *
 * The companion to client-timeline.php, which returns raw events. This returns
 * the same activity already written up: one entry per day the customer used
 * Tapify, plus a summary of the whole window — which is what a Customer Manager
 * reads before picking up the phone. Wording lives in ActivityNarrator so the
 * web panel and the mobile app say exactly the same thing.
 *
 * days: 1 = today, 7 = this week, 30 = this month (capped at 92, which is as
 * far back as a report is ever asked for and keeps one query cheap).
 */
require_once __DIR__ . '/_bridge.php';
require_once __DIR__ . '/../../includes/activity/ActivityNarrator.php';
crm_bridge_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError('Only GET allowed', 405);
}
$userId = (int)($_GET['user_id'] ?? 0);
if ($userId <= 0) {
    sendError('user_id is required', 400);
}
$days = (int)($_GET['days'] ?? 7);
$days = max(1, min(92, $days));

try {
    $pdo = crm_pdo();

    // Days are counted in IST (see ActivityNarrator), so the window starts at
    // midnight IST of the first day, converted back to UTC for the query.
    $tz = new DateTimeZone(ActivityNarrator::TZ);
    $startLocal = (new DateTimeImmutable('now', $tz))->setTime(0, 0)->modify('-' . ($days - 1) . ' days');
    $startUtc = $startLocal->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s');

    $st = $pdo->prepare(
        'SELECT created_at, feature, action, kind, platform, detail
           FROM user_activity_events
          WHERE user_id = ? AND created_at >= ?
          ORDER BY created_at'
    );
    $st->execute([$userId, $startUtc]);
    $events = $st->fetchAll();

    $built = ActivityNarrator::build($events, gmdate('Y-m-d H:i:s'), $days);

    sendSuccess('OK', [
        'days'    => $built['days'],
        'summary' => $built['summary'],
        'window'  => ['days' => $days, 'from' => $startLocal->format('Y-m-d')],
    ]);
} catch (Throwable $e) {
    // Before the activity migration, or if Tapify is mid-deploy, an empty
    // report is better than a broken profile page.
    error_log('crm/client-activity: ' . $e->getMessage());
    sendSuccess('OK', [
        'days'    => [],
        'summary' => ['headline' => 'Activity is unavailable right now', 'lines' => [], 'activeDays' => 0, 'visits' => 0, 'changes' => 0, 'minutes' => 0, 'lastSeen' => null],
        'window'  => ['days' => $days, 'from' => null],
    ]);
}
