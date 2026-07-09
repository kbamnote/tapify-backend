<?php
/**
 * TAPIFY Meta Ads — orchestration.
 * boostPost(): validate → charge the wallet (budget + commission) → create the
 * ad via Tapify's ad account → record it. If Meta creation fails AFTER the
 * charge, the wallet is REFUNDED — Tapify never keeps money without a live ad.
 */
class AdsService
{
    /** @var PDO */ private $db;
    /** @var WalletService */ private $wallet;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->wallet = new WalletService($db);   // from includes/wallet
    }

    public function boostPost($userId, array $input)
    {
        // 1) Validate the target Page (must be a connected Facebook page).
        $connectionId = (int) ($input['connection_id'] ?? 0);
        $conn = $this->fetchConnection($userId, $connectionId);
        if (!$conn) {
            throw new AdsException('Connect a Facebook Page first, then pick it to boost.', 422, 'no fb connection');
        }
        $postId = trim((string) ($input['post_id'] ?? ''));
        if ($postId === '') {
            throw new AdsException('Pick a post to boost.', 422, 'no post id');
        }

        // 2) Validate budget + duration.
        // Meta enforces a per-DAY minimum on ad-set budgets. Because we use a
        // LIFETIME budget, Meta's floor is (min daily) x (run days), e.g. a 3-day
        // boost needs ~₹284. A flat minimum isn't enough — a short, low budget
        // passes it but Meta still rejects ("your ad set budget must be more than
        // ₹X"). Enforce the duration-aware floor here, BEFORE charging the wallet,
        // so we fail fast with a clear message instead of charge-then-refund.
        // ₹100/day is padded slightly above Meta's ~₹94.80/day INR floor so we
        // always clear it (Meta's exact floor varies by currency/billing/targeting).
        $durationDays = max(1, min(30, (int) ($input['duration_days'] ?? 3)));
        $budgetInr = (float) ($input['budget_inr'] ?? 0);
        $minDailyInr = defined('ADS_MIN_DAILY_INR') ? (float) ADS_MIN_DAILY_INR : 100;
        $absMinInr = defined('ADS_MIN_BUDGET_INR') ? (int) ADS_MIN_BUDGET_INR : 100;
        $minInr = max($absMinInr, (int) ceil($minDailyInr * $durationDays));
        if ($budgetInr < $minInr) {
            throw new AdsException(
                "For a {$durationDays}-day boost the minimum budget is ₹{$minInr} (about ₹" . (int) $minDailyInr . "/day). Increase the budget or shorten the duration.",
                422,
                'budget below Meta per-day floor'
            );
        }
        $budgetPoints = WalletService::pointsForInr($budgetInr);

        $pageId  = $conn['account_id'];
        $storyId = strpos($postId, '_') !== false ? $postId : ($pageId . '_' . $postId);
        $targeting = $this->buildTargeting(is_array($input['targeting'] ?? null) ? $input['targeting'] : []);

        // 3) Charge the wallet first (reserves funds atomically).
        $charge = $this->wallet->chargeForAd($userId, $budgetPoints, 'boost', 'Boost post ' . $storyId);

        // 4) Create the ad; on any failure, refund the full charge.
        try {
            $client = new MetaAdsClient();
            $res = $client->createBoost([
                'name'            => 'Tapify Boost ' . date('Y-m-d H:i'),
                'object_story_id' => $storyId,
                'budget_inr'      => $budgetInr,
                'duration_days'   => $durationDays,
                'targeting'       => $targeting,
            ]);

            $stmt = $this->db->prepare(
                "INSERT INTO ad_campaigns
                   (user_id, connection_id, page_id, object_story_id, campaign_id, adset_id, ad_id, name,
                    budget_inr, budget_points, commission_points, duration_days, targeting_json, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')"
            );
            $stmt->execute([
                $userId, $connectionId, $pageId, $storyId, $res['campaign_id'], $res['adset_id'], $res['ad_id'],
                'Boost ' . date('Y-m-d H:i'), $budgetInr, $charge['budget'], $charge['commission'],
                $durationDays, json_encode($targeting),
            ]);

            return [
                'campaign_id'       => $res['campaign_id'],
                'ad_id'             => $res['ad_id'],
                'status'            => 'active',
                'budget_points'     => $charge['budget'],
                'commission_points' => $charge['commission'],
                'total_charged'     => $charge['total'],
                'wallet_balance'    => $charge['balance_after'],
            ];
        } catch (Exception $e) {
            // Refund everything we charged — the ad did not launch.
            try {
                $this->wallet->refund($userId, $charge['total'], 'boost_failed', 'Refund: ad could not be created');
            } catch (Exception $re) {
                error_log('[ADS][ERROR] refund_failed user=' . $userId . ' pts=' . $charge['total'] . ' ' . $re->getMessage());
            }
            if ($e instanceof AdsException) throw $e;
            throw new AdsException('Could not create the ad. Your wallet was refunded.', 502, $e->getMessage());
        }
    }

    public function listCampaigns($userId, $limit = 30)
    {
        $limit = max(1, min(100, (int) $limit));
        $s = $this->db->prepare(
            "SELECT id, page_id, object_story_id, campaign_id, ad_id, name, budget_inr, budget_points,
                    commission_points, duration_days, status, created_at
             FROM ad_campaigns WHERE user_id = ? ORDER BY id DESC LIMIT " . $limit
        );
        $s->execute([$userId]);
        return $s->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insights($userId, $campaignId)
    {
        $this->requireOwnedCampaign($userId, $campaignId);
        return (new MetaAdsClient())->campaignInsights($campaignId);
    }

    public function setStatus($userId, $campaignId, $active)
    {
        $this->requireOwnedCampaign($userId, $campaignId);
        (new MetaAdsClient())->setCampaignStatus($campaignId, $active ? 'ACTIVE' : 'PAUSED');
        $this->db->prepare("UPDATE ad_campaigns SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE campaign_id = ? AND user_id = ?")
            ->execute([$active ? 'active' : 'paused', $campaignId, $userId]);
        return $active ? 'active' : 'paused';
    }

    /** Recent Page posts to choose from, via the connection's page token. */
    public function pagePosts($userId, $connectionId)
    {
        $conn = $this->fetchConnection($userId, $connectionId);
        if (!$conn) {
            throw new AdsException('Connect a Facebook Page first.', 422, 'no fb connection');
        }
        return (new MetaAdsClient())->listPagePosts($conn['account_id'], $conn['access_token'], 15);
    }

    // ── helpers ─────────────────────────────────────────────────────────────
    private function fetchConnection($userId, $connectionId)
    {
        $s = $this->db->prepare(
            "SELECT * FROM social_connections WHERE id = ? AND user_id = ? AND platform = 'facebook' AND is_active = 1 LIMIT 1"
        );
        $s->execute([(int) $connectionId, $userId]);
        return $s->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    private function requireOwnedCampaign($userId, $campaignId)
    {
        $s = $this->db->prepare("SELECT id FROM ad_campaigns WHERE campaign_id = ? AND user_id = ? LIMIT 1");
        $s->execute([$campaignId, $userId]);
        if (!$s->fetchColumn()) {
            throw new AdsException('Campaign not found.', 404, 'not owned');
        }
    }

    private function buildTargeting(array $t)
    {
        $countries = !empty($t['country_codes']) && is_array($t['country_codes'])
            ? array_values($t['country_codes']) : ['IN'];
        $targeting = [
            'geo_locations' => ['countries' => $countries],
            'age_min' => isset($t['age_min']) ? max(13, min(65, (int) $t['age_min'])) : 18,
            'age_max' => isset($t['age_max']) ? max(13, min(65, (int) $t['age_max'])) : 65,
        ];
        if (!empty($t['genders']) && is_array($t['genders'])) {
            $g = array_values(array_filter(array_map('intval', $t['genders']), fn($x) => in_array($x, [1, 2], true)));
            if ($g) $targeting['genders'] = $g;
        }
        return $targeting;
    }
}
