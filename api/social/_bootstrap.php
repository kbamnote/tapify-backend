<?php
/**
 * TAPIFY Social Publishing — endpoint bootstrap + JSON handler.
 * config/database.php provides sessions + CORS + OPTIONS. callback.php does NOT
 * use social_run() (browser redirect, state-authed).
 */
require_once __DIR__ . '/../../config/database.php';
ini_set('display_errors', '0');
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/social/autoload.php';

/** Run an authenticated JSON endpoint. $fn receives ($userId, SocialService). */
function social_run(callable $fn)
{
    header('Content-Type: application/json');
    requireAuth();
    $userId = getCurrentUserId();
    try {
        $service = new SocialService(getDB());
        $fn($userId, $service);
    } catch (SocialException $e) {
        sendError($e->getSafeMessage(), $e->getHttpStatus());
    } catch (Exception $e) {
        SocialLogger::error('endpoint.unexpected', ['error' => $e->getMessage()]);
        sendError('Something went wrong. Please try again.', 500);
    }
}
