<?php
/**
 * TAPIFY - Record app usage events.
 * POST /api/activity/track.php
 *
 * Called by the Tapify app with events it queued locally — screens opened, a
 * few actions that never reach the server on their own (sharing the card,
 * downloading a design), and app launches:
 *
 *   { "events": [
 *       { "id": "k3j2…", "type": "screen",   "screen": "my-designs",           "at": 1726470000000 },
 *       { "id": "p9x1…", "type": "action",   "feature": "designs", "action": "download", "at": … },
 *       { "id": "a7c4…", "type": "app_open", "at": … }
 *   ] }
 *
 * Header: X-Tapify-Client: app;android;1.1.17
 *
 * Returns counts only. A batch is always acknowledged once parsed, so the app
 * can clear its queue; events it can't use are counted as skipped, not errors.
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/activity/ActivityTracker.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Only POST allowed', 405);
}
requireAuth();

$input = getInput();
$events = $input['events'] ?? null;
if (!is_array($events) || array_values($events) !== $events) {
    sendError('events must be an array', 400);
}

try {
    $result = ActivityTracker::ingestAppBatch(
        (int)getCurrentUserId(),
        (string)($_SESSION['user_role'] ?? ''),
        $events
    );
    sendSuccess('Recorded', $result);
} catch (Throwable $e) {
    error_log('activity/track: ' . $e->getMessage());
    // 503 so the app keeps the batch and retries later, rather than losing it.
    sendError('Activity could not be recorded right now', 503);
}
