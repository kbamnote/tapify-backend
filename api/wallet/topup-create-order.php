<?php
/**
 * POST /api/wallet/topup-create-order.php   Body: { amount_inr }
 * Creates a Razorpay order the app opens in checkout. Returns order details.
 * (Razorpay keys required; until set, responds 503 "payments not enabled".)
 */
require_once __DIR__ . '/_bootstrap.php';

wallet_run(function ($userId, $db) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendError('Method not allowed', 405);
    }
    $input  = getInput();
    $amount = (float) ($input['amount_inr'] ?? 0);
    if ($amount < 1) {
        sendError('Enter an amount of at least ₹1.', 422);
    }
    $topup = new TopupService($db);
    sendSuccess('Order created', $topup->createTopup($userId, $amount));
});
