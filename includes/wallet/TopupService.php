<?php
/**
 * TAPIFY Wallet — top-up flow (bridges the payment gateway to wallet credit).
 * createTopup() makes a gateway order the app opens in checkout; verifyAndCredit()
 * validates the payment signature and credits points exactly once (idempotent).
 */
class TopupService
{
    /** @var PDO */ private $db;
    /** @var WalletService */ private $wallet;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->wallet = new WalletService($db);
    }

    /** Create a gateway order + a wallet_topups record. */
    public function createTopup($userId, $amountInr)
    {
        $amountInr = round((float) $amountInr, 2);
        if ($amountInr < 1) {
            throw new WalletException('Minimum top-up is ₹1.', 422, 'amount too small');
        }

        $provider = PaymentFactory::make();
        if (!$provider->isConfigured()) {
            throw new WalletException('Online payments are not enabled yet. Please try again later.', 503, 'gateway not configured');
        }

        $points  = WalletService::pointsForInr($amountInr);
        $receipt = 'tap_' . $userId . '_' . time();
        $order   = $provider->createOrder($amountInr, $receipt, ['user_id' => (string) $userId]);

        $stmt = $this->db->prepare(
            "INSERT INTO wallet_topups (user_id, provider, order_id, amount_inr, points, status)
             VALUES (?, ?, ?, ?, ?, 'created')"
        );
        $stmt->execute([$userId, $provider->name(), $order['order_id'], $amountInr, $points]);

        return [
            'order_id'     => $order['order_id'],
            'amount_inr'   => $amountInr,
            'amount_paise' => $order['amount_paise'],
            'currency'     => $order['currency'],
            'key_id'       => $order['key_id'],
            'points'       => $points,
        ];
    }

    /** Verify a completed payment and credit points once. */
    public function verifyAndCredit($userId, $orderId, $paymentId, $signature)
    {
        $topup = $this->fetchTopup($userId, $orderId);
        if (!$topup) {
            throw new WalletException('Top-up not found.', 404, 'no topup row');
        }
        if ($topup['status'] === 'paid') {
            return ['balance' => $this->wallet->getBalance($userId), 'points' => (int) $topup['points'], 'already' => true];
        }

        $provider = PaymentFactory::make();
        if (!$provider->verifyPayment($orderId, $paymentId, $signature)) {
            $this->db->prepare("UPDATE wallet_topups SET status='failed', payment_id=? WHERE order_id=? AND status='created'")
                ->execute([$paymentId, $orderId]);
            throw new WalletException('Payment could not be verified.', 400, 'signature mismatch');
        }

        // Credit points AND mark the top-up paid in ONE transaction, so the
        // wallet is never charged/credited without the status flip (and vice
        // versa). Locking the top-up row makes this exactly-once under retries.
        $this->db->beginTransaction();
        try {
            $lock = $this->db->prepare(
                "SELECT status, points, amount_inr FROM wallet_topups WHERE order_id = ? AND user_id = ? FOR UPDATE"
            );
            $lock->execute([$orderId, $userId]);
            $row = $lock->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                throw new WalletException('Top-up not found.', 404, 'no topup in tx');
            }
            if ($row['status'] === 'paid') {
                $balance = $this->wallet->getBalance($userId);
                $this->db->commit();
                return ['balance' => $balance, 'points' => (int) $row['points'], 'already' => true];
            }

            $points  = (int) $row['points'];
            $balance = $this->wallet->applyInTransaction($userId, [[
                'direction' => 'credit', 'points' => $points, 'category' => 'topup',
                'reference' => $paymentId, 'description' => 'Wallet top-up ₹' . $row['amount_inr'],
            ]]);
            $this->db->prepare(
                "UPDATE wallet_topups SET status='paid', payment_id=?, updated_at=CURRENT_TIMESTAMP WHERE order_id=? AND user_id=?"
            )->execute([$paymentId, $orderId, $userId]);

            $this->db->commit();
            return ['balance' => $balance, 'points' => $points, 'already' => false];
        } catch (WalletException $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            throw $e;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            throw new WalletException('Could not credit your wallet. If money was deducted, contact support.', 500, $e->getMessage());
        }
    }

    private function fetchTopup($userId, $orderId)
    {
        $s = $this->db->prepare("SELECT * FROM wallet_topups WHERE order_id = ? AND user_id = ? LIMIT 1");
        $s->execute([$orderId, $userId]);
        return $s->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}
