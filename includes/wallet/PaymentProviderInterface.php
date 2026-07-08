<?php
/**
 * TAPIFY Wallet — payment gateway contract.
 * Wallet/top-up logic depends only on this, so Razorpay (or any gateway) can be
 * swapped without touching the ledger.
 */
interface PaymentProviderInterface
{
    /** Short id, e.g. "razorpay". */
    public function name();

    /** True only when the gateway credentials are configured. */
    public function isConfigured();

    /**
     * Create a gateway order for a top-up.
     * @return array ['order_id','amount_paise','currency','key_id']
     * @throws WalletException
     */
    public function createOrder($amountInr, $receipt, array $notes = []);

    /** Verify a completed payment's signature (order_id + payment_id). */
    public function verifyPayment($orderId, $paymentId, $signature);

    /** Verify a webhook payload signature against the raw body. */
    public function verifyWebhookSignature($rawBody, $signature);
}
