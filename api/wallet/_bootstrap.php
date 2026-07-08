<?php
/**
 * TAPIFY Wallet — endpoint bootstrap + JSON handler.
 */
require_once __DIR__ . '/../../config/database.php';
ini_set('display_errors', '0');
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/wallet/autoload.php';

/** Run an authenticated JSON endpoint. $fn receives ($userId, PDO $db). */
function wallet_run(callable $fn)
{
    header('Content-Type: application/json');
    requireAuth();
    $userId = getCurrentUserId();
    try {
        $fn($userId, getDB());
    } catch (WalletException $e) {
        sendError($e->getSafeMessage(), $e->getHttpStatus());
    } catch (Exception $e) {
        error_log('[WALLET][ERROR] ' . $e->getMessage());
        sendError('Something went wrong. Please try again.', 500);
    }
}
