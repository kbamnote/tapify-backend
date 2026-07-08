<?php
/**
 * POST /api/wallet/topup-verify.php
 * Body: { order_id, payment_id, signature }   (from Razorpay checkout success)
 * Verifies the payment signature and credits wallet points exactly once.
 */
require_once __DIR__ . '/_bootstrap.php';

wallet_run(function ($userId, $db) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendError('Method not allowed', 405);
    }
    $input     = getInput();
    $orderId   = trim((string) ($input['order_id'] ?? ''));
    $paymentId = trim((string) ($input['payment_id'] ?? ''));
    $signature = trim((string) ($input['signature'] ?? ''));
    if ($orderId === '' || $paymentId === '' || $signature === '') {
        sendError('order_id, payment_id and signature are required.', 422);
    }
    $topup  = new TopupService($db);
    $result = $topup->verifyAndCredit($userId, $orderId, $paymentId, $signature);
    sendSuccess('Wallet credited', $result);
});
