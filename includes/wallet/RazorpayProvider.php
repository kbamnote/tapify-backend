<?php
/**
 * TAPIFY Wallet — Razorpay adapter.
 * Functional but key-gated: until RAZORPAY_KEY_ID/SECRET are set, isConfigured()
 * is false and the top-up endpoints report "payments not enabled yet".
 * https://razorpay.com/docs/api/orders/
 */
class RazorpayProvider implements PaymentProviderInterface
{
    private $keyId;
    private $keySecret;
    private $webhookSecret;

    public function __construct()
    {
        $this->keyId         = defined('RAZORPAY_KEY_ID') ? RAZORPAY_KEY_ID : '';
        $this->keySecret     = defined('RAZORPAY_KEY_SECRET') ? RAZORPAY_KEY_SECRET : '';
        $this->webhookSecret = defined('RAZORPAY_WEBHOOK_SECRET') ? RAZORPAY_WEBHOOK_SECRET : '';
    }

    public function name() { return 'razorpay'; }

    public function isConfigured()
    {
        return $this->keyId !== '' && $this->keySecret !== '';
    }

    public function createOrder($amountInr, $receipt, array $notes = [])
    {
        if (!$this->isConfigured()) {
            throw new WalletException('Online payments are not enabled yet.', 503, 'razorpay keys missing');
        }
        $paise = (int) round(((float) $amountInr) * 100);
        if ($paise < 100) {
            throw new WalletException('Minimum top-up is ₹1.', 422, 'amount too small');
        }

        $body = ['amount' => $paise, 'currency' => 'INR', 'receipt' => (string) $receipt, 'payment_capture' => 1];
        if ($notes) $body['notes'] = $notes;

        $ch = curl_init('https://api.razorpay.com/v1/orders');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($body),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_USERPWD        => $this->keyId . ':' . $this->keySecret,
            CURLOPT_TIMEOUT        => 30,
        ]);
        $raw = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($raw === false) {
            throw new WalletException('Could not reach the payment gateway. Please try again.', 504, "curl: {$err}");
        }
        $res = json_decode($raw, true);
        if ($code >= 400 || empty($res['id'])) {
            $msg = $res['error']['description'] ?? 'order creation failed';
            throw new WalletException('Could not start the payment. Please try again.', 502, "razorpay: {$msg}");
        }

        return [
            'order_id'     => $res['id'],
            'amount_paise' => $paise,
            'currency'     => 'INR',
            'key_id'       => $this->keyId,   // publishable id, safe for the app
        ];
    }

    public function verifyPayment($orderId, $paymentId, $signature)
    {
        if (!$this->isConfigured()) return false;
        $expected = hash_hmac('sha256', $orderId . '|' . $paymentId, $this->keySecret);
        return hash_equals($expected, (string) $signature);
    }

    public function verifyWebhookSignature($rawBody, $signature)
    {
        if ($this->webhookSecret === '') return false;
        $expected = hash_hmac('sha256', $rawBody, $this->webhookSecret);
        return hash_equals($expected, (string) $signature);
    }
}
