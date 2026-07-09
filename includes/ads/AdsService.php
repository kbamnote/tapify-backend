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

        // Self-healing Page permission: make sure Tapify's system user holds the
        // ADVERTISE task on this Page (idempotent, uses the Page token saved at
        // connect). Best-effort — if it fails, the boost proceeds and Meta's own
        // error explains what's missing.
        (new MetaAdsClient())->assignPageAdvertiser($pageId, $conn['access_token'] ?? '');

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

    /** Geo-location autocomplete for the boost screen's location picker. */
    public function searchGeo($query)
    {
        $query = trim((string) $query);
        if (strlen($query) < 2) return [];
        return (new MetaAdsClient())->searchGeo($query);
    }

    /** Interest/behaviour autocomplete for detailed targeting. */
    public function searchInterests($query)
    {
        $query = trim((string) $query);
        if (strlen($query) < 2) return [];
        return (new MetaAdsClient())->searchInterests($query);
    }

    /** Language autocomplete (Meta locales). */
    public function searchLocales($query)
    {
        $query = trim((string) $query);
        if (strlen($query) < 2) return [];
        return (new MetaAdsClient())->searchLocales($query);
    }

    /**
     * Build a Meta targeting spec from the app's input. Everything except a
     * geo default is optional, so old callers (country + age + gender) keep
     * working while new ones can pin a city/region/radius, add languages, or
     * layer on interests.
     *
     * Geo precedence: if any fine-grained location (city/region/custom pin/zip)
     * is given we target THAT and drop the whole-country default — otherwise a
     * ₹ "target Mumbai" boost would silently blanket all of India.
     */
    private function buildTargeting(array $t)
    {
        $geo = [];

        // Cities — each needs a Meta key (from searchGeo) + a radius (km, 1..80).
        if (!empty($t['cities']) && is_array($t['cities'])) {
            $cities = [];
            foreach ($t['cities'] as $c) {
                if (empty($c['key'])) continue;
                $cities[] = [
                    'key'           => (string) $c['key'],
                    'radius'        => max(1, min(80, (int) ($c['radius'] ?? 25))),
                    'distance_unit' => 'kilometer',
                ];
            }
            if ($cities) $geo['cities'] = $cities;
        }
        // Regions/states — key only.
        if (!empty($t['regions']) && is_array($t['regions'])) {
            $regions = [];
            foreach ($t['regions'] as $r) {
                $key = is_array($r) ? ($r['key'] ?? null) : $r;
                if ($key) $regions[] = ['key' => (string) $key];
            }
            if ($regions) $geo['regions'] = $regions;
        }
        // Custom pin(s) — latitude/longitude + radius (drop-a-pin targeting).
        if (!empty($t['custom_locations']) && is_array($t['custom_locations'])) {
            $custom = [];
            foreach ($t['custom_locations'] as $c) {
                if (!isset($c['latitude'], $c['longitude'])) continue;
                $custom[] = [
                    'latitude'      => (float) $c['latitude'],
                    'longitude'     => (float) $c['longitude'],
                    'radius'        => max(1, min(80, (int) ($c['radius'] ?? 25))),
                    'distance_unit' => 'kilometer',
                ];
            }
            if ($custom) $geo['custom_locations'] = $custom;
        }
        // Post/PIN codes.
        if (!empty($t['zips']) && is_array($t['zips'])) {
            $zips = [];
            foreach ($t['zips'] as $z) {
                $key = is_array($z) ? ($z['key'] ?? null) : $z;
                if ($key) $zips[] = ['key' => (string) $key];
            }
            if ($zips) $geo['zips'] = $zips;
        }

        if (!$geo) {
            // No fine location → whole country (default India).
            $countries = !empty($t['country_codes']) && is_array($t['country_codes'])
                ? array_values($t['country_codes']) : ['IN'];
            $geo['countries'] = $countries;
        } elseif (!empty($t['country_codes']) && is_array($t['country_codes'])) {
            // Caller explicitly wants a country layered on top of the fine geo.
            $geo['countries'] = array_values($t['country_codes']);
        }

        $targeting = [
            'geo_locations' => $geo,
            'age_min' => isset($t['age_min']) ? max(13, min(65, (int) $t['age_min'])) : 18,
            'age_max' => isset($t['age_max']) ? max(13, min(65, (int) $t['age_max'])) : 65,
        ];

        if (!empty($t['genders']) && is_array($t['genders'])) {
            $g = array_values(array_filter(array_map('intval', $t['genders']), fn($x) => in_array($x, [1, 2], true)));
            if ($g) $targeting['genders'] = $g;
        }

        // Languages — Meta numeric locale ids.
        if (!empty($t['locales']) && is_array($t['locales'])) {
            $loc = array_values(array_filter(array_map('intval', $t['locales'])));
            if ($loc) $targeting['locales'] = $loc;
        }

        // Detailed targeting — interests/behaviours by id (from a targeting search).
        if (!empty($t['interests']) && is_array($t['interests'])) {
            $ints = [];
            foreach ($t['interests'] as $i) {
                $id = is_array($i) ? ($i['id'] ?? null) : $i;
                if ($id) $ints[] = ['id' => (string) $id, 'name' => is_array($i) ? (string) ($i['name'] ?? '') : ''];
            }
            if ($ints) $targeting['flexible_spec'] = [['interests' => $ints]];
        }

        // Advantage+ audience (formerly "detailed targeting expansion"). Meta now
        // REQUIRES this flag on every ad set: 1 lets Meta widen the audience beyond
        // what we specified, 0 targets exactly what the user chose. Default to 0 to
        // respect the picked age/gender/interests; the location radius is always
        // honoured either way. Caller may pass advantage_audience:1 for broader reach.
        $advAudience = isset($t['advantage_audience']) ? ((int) $t['advantage_audience'] ? 1 : 0) : 0;
        $targeting['targeting_automation'] = ['advantage_audience' => $advAudience];

        return $targeting;
    }
}
