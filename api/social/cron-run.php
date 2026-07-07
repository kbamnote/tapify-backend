<?php
/**
 * GET/POST /api/social/cron-run.php?secret=XXX
 * Publishes all scheduled posts whose time has arrived. Intended for a Railway
 * cron job (no user session), so it is guarded by the SOCIAL_CRON_SECRET.
 *
 * Example cron: every 5 min → curl "https://app.tapify.co.in/api/social/cron-run.php?secret=YOUR_SECRET"
 */
require_once __DIR__ . '/../../config/database.php';
ini_set('display_errors', '0');
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/social/autoload.php';

header('Content-Type: application/json');

$secret = defined('SOCIAL_CRON_SECRET') ? SOCIAL_CRON_SECRET : '';
$given  = $_GET['secret'] ?? ($_SERVER['HTTP_X_CRON_SECRET'] ?? '');

if ($secret === '') {
    sendError('Cron endpoint is disabled (set SOCIAL_CRON_SECRET).', 403);
}
if (!hash_equals($secret, (string) $given)) {
    sendError('Forbidden', 403);
}

try {
    $service = new SocialService(getDB());
    $count = $service->runDuePosts();
    SocialLogger::info('cron.ran', ['published' => $count]);
    sendSuccess('Ran scheduled posts', ['processed' => $count]);
} catch (Exception $e) {
    SocialLogger::error('cron.error', ['error' => $e->getMessage()]);
    sendError('Cron run failed.', 500);
}
