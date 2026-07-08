<?php
/**
 * TAPIFY Wallet — payment provider factory.
 * Add a gateway = a case here; wallet code never names a concrete provider.
 */
class PaymentFactory
{
    public static function make($provider = null)
    {
        $provider = strtolower(trim($provider ?: (defined('PAYMENT_PROVIDER') ? PAYMENT_PROVIDER : 'razorpay')));
        switch ($provider) {
            case 'razorpay': return new RazorpayProvider();
            default:
                throw new WalletException('Payment gateway is misconfigured.', 500, "unknown provider '{$provider}'");
        }
    }
}
