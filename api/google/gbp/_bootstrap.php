<?php
/**
 * TAPIFY Google Business Profile — endpoint bootstrap + JSON handler.
 * config/database.php provides sessions + CORS + OPTIONS. The callback endpoint
 * does NOT use gbp_run() (it is a browser redirect, authed via OAuth state).
 */
require_once __DIR__ . '/../../../config/database.php';
ini_set('display_errors', '0'); // keep PHP errors out of JSON responses
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../../includes/google/autoload.php';

/**
 * Run an authenticated JSON endpoint. $fn receives ($userId, GoogleBusinessService).
 */
function gbp_run(callable $fn)
{
    header('Content-Type: application/json');
    requireAuth();
    $userId = getCurrentUserId();
    try {
        $service = new GoogleBusinessService(getDB());
        $fn($userId, $service);
    } catch (GoogleException $e) {
        sendError($e->getSafeMessage(), $e->getHttpStatus());
    } catch (Exception $e) {
        GoogleLogger::error('endpoint.unexpected', ['error' => $e->getMessage()]);
        sendError('Something went wrong with Google Business Profile. Please try again.', 500);
    }
}
