<?php
/**
 * TAPIFY Meta Ads — endpoint bootstrap + JSON handler.
 * Ads depend on the wallet module, so both autoloaders are pulled in.
 */
require_once __DIR__ . '/../../config/database.php';
ini_set('display_errors', '0');
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/wallet/autoload.php';
require_once __DIR__ . '/../../includes/ads/autoload.php';

/** Run an authenticated JSON endpoint. $fn receives ($userId, AdsService). */
function ads_run(callable $fn)
{
    header('Content-Type: application/json');
    requireAuth();
    $userId = getCurrentUserId();
    try {
        $fn($userId, new AdsService(getDB()));
    } catch (AdsException $e) {
        sendError($e->getSafeMessage(), $e->getHttpStatus());
    } catch (WalletException $e) {
        sendError($e->getSafeMessage(), $e->getHttpStatus());
    } catch (Exception $e) {
        error_log('[ADS][ERROR] endpoint ' . $e->getMessage());
        sendError('Something went wrong. Please try again.', 500);
    }
}
