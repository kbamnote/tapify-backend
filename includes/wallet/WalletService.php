<?php
/**
 * TAPIFY Wallet — points ledger.
 *
 * balance_points on `wallets` is the source of truth; every change also appends
 * an immutable row to `wallet_transactions`. All balance changes go through
 * applyEntries(), which runs in a DB transaction and locks the wallet row
 * (SELECT ... FOR UPDATE) so concurrent debits can't oversell the balance.
 *
 * Points are integers (₹1 = WALLET_POINTS_PER_INR points).
 */
class WalletService
{
    /** @var PDO */
    private $db;

    public function __construct(PDO $db) { $this->db = $db; }

    // ── Reads ──────────────────────────────────────────────────────────────
    public function getBalance($userId)
    {
        $s = $this->db->prepare("SELECT balance_points FROM wallets WHERE user_id = ? LIMIT 1");
        $s->execute([$userId]);
        $v = $s->fetchColumn();
        return $v === false ? 0 : (int) $v;
    }

    public function history($userId, $limit = 50)
    {
        $limit = max(1, min(200, (int) $limit));
        $s = $this->db->prepare(
            "SELECT id, direction, points, balance_after, category, reference, description, created_at
             FROM wallet_transactions WHERE user_id = ? ORDER BY id DESC LIMIT " . $limit
        );
        $s->execute([$userId]);
        return $s->fetchAll(PDO::FETCH_ASSOC);
    }

    // ── Writes ─────────────────────────────────────────────────────────────
    public function credit($userId, $points, $category, $reference = null, $description = null, $meta = null)
    {
        return $this->applyEntries($userId, [[
            'direction' => 'credit', 'points' => (int) $points, 'category' => $category,
            'reference' => $reference, 'description' => $description, 'meta' => $meta,
        ]]);
    }

    public function debit($userId, $points, $category, $reference = null, $description = null, $meta = null)
    {
        return $this->applyEntries($userId, [[
            'direction' => 'debit', 'points' => (int) $points, 'category' => $category,
            'reference' => $reference, 'description' => $description, 'meta' => $meta,
        ]]);
    }

    public function refund($userId, $points, $reference = null, $description = null, $meta = null)
    {
        return $this->credit($userId, $points, 'refund', $reference, $description, $meta);
    }

    /**
     * Charge a wallet for an ad: debit the ad budget + Tapify commission in one
     * atomic operation (two ledger rows). Throws if the balance can't cover both.
     *
     * @return array ['budget','commission','total','balance_after']
     */
    public function chargeForAd($userId, $budgetPoints, $reference = null, $description = null)
    {
        $budget = (int) $budgetPoints;
        if ($budget <= 0) {
            throw new WalletException('Enter a valid ad budget.', 422, 'non-positive budget');
        }
        $commission = self::commissionPoints($budget);
        $balance = $this->applyEntries($userId, [
            ['direction' => 'debit', 'points' => $budget, 'category' => 'ad_spend',
             'reference' => $reference, 'description' => $description ?: 'Ad budget'],
            ['direction' => 'debit', 'points' => $commission, 'category' => 'commission',
             'reference' => $reference, 'description' => 'Tapify service fee'],
        ]);
        return ['budget' => $budget, 'commission' => $commission, 'total' => $budget + $commission, 'balance_after' => $balance];
    }

    /** Commission (points) for a given ad budget, per ADS_COMMISSION_PERCENT. */
    public static function commissionPoints($budgetPoints)
    {
        $pct = defined('ADS_COMMISSION_PERCENT') ? (float) ADS_COMMISSION_PERCENT : 0;
        return (int) ceil(((int) $budgetPoints) * $pct / 100);
    }

    /** Points a rupee amount buys. */
    public static function pointsForInr($amountInr)
    {
        $rate = defined('WALLET_POINTS_PER_INR') ? (int) WALLET_POINTS_PER_INR : 1;
        return (int) round(((float) $amountInr) * $rate);
    }

    /**
     * Apply one or more ledger entries in their own transaction.
     */
    private function applyEntries($userId, array $entries)
    {
        $this->db->beginTransaction();
        try {
            $balance = $this->applyInTransaction($userId, $entries);
            $this->db->commit();
            return $balance;
        } catch (WalletException $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            throw $e;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            throw new WalletException('Wallet update failed. Please try again.', 500, $e->getMessage());
        }
    }

    /**
     * Apply ledger entries within an EXISTING transaction (the caller manages
     * begin/commit/rollback). Locks the wallet row (FOR UPDATE) and validates the
     * running balance never goes negative. Lets other operations — e.g. marking a
     * top-up paid — commit atomically with the balance change. Returns the final
     * balance. Must be called inside a transaction.
     */
    public function applyInTransaction($userId, array $entries)
    {
        $this->db->prepare("INSERT IGNORE INTO wallets (user_id, balance_points) VALUES (?, 0)")->execute([$userId]);

        $lock = $this->db->prepare("SELECT balance_points FROM wallets WHERE user_id = ? FOR UPDATE");
        $lock->execute([$userId]);
        $balance = (int) $lock->fetchColumn();

        $ins = $this->db->prepare(
            "INSERT INTO wallet_transactions
               (user_id, direction, points, balance_after, category, reference, description, meta_json)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );

        foreach ($entries as $e) {
            $pts = (int) $e['points'];
            if ($pts <= 0) {
                throw new WalletException('Invalid amount.', 422, 'non-positive points');
            }
            if ($e['direction'] === 'debit') {
                if ($balance < $pts) {
                    throw new WalletException('Insufficient wallet balance. Please top up.', 402, 'insufficient balance');
                }
                $balance -= $pts;
            } else {
                $balance += $pts;
            }
            $ins->execute([
                $userId, $e['direction'], $pts, $balance, $e['category'],
                $e['reference'] ?? null, $e['description'] ?? null,
                isset($e['meta']) && $e['meta'] !== null ? json_encode($e['meta']) : null,
            ]);
        }

        $this->db->prepare("UPDATE wallets SET balance_points = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ?")
            ->execute([$balance, $userId]);

        return $balance;
    }
}
