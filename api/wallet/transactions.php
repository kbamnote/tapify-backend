<?php
/**
 * GET /api/wallet/transactions.php?limit=50
 * Wallet ledger, most recent first.
 */
require_once __DIR__ . '/_bootstrap.php';

wallet_run(function ($userId, $db) {
    $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 50;
    $wallet = new WalletService($db);
    sendSuccess('Transactions', ['transactions' => $wallet->history($userId, $limit)]);
});
