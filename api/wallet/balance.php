<?php
/**
 * GET /api/wallet/balance.php
 * { balance, points_per_inr, commission_percent, payments_enabled }
 */
require_once __DIR__ . '/_bootstrap.php';

wallet_run(function ($userId, $db) {
    $wallet = new WalletService($db);
    sendSuccess('Wallet', [
        'balance'            => $wallet->getBalance($userId),
        'points_per_inr'     => defined('WALLET_POINTS_PER_INR') ? (int) WALLET_POINTS_PER_INR : 1,
        'commission_percent' => defined('ADS_COMMISSION_PERCENT') ? (float) ADS_COMMISSION_PERCENT : 0,
        'payments_enabled'   => PaymentFactory::make()->isConfigured(),
    ]);
});
