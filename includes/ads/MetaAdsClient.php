<?php
/**
 * TAPIFY Meta Ads — Marketing API client.
 * Runs ads through TAPIFY's own ad account using the system-user token
 * (server-to-server). A "boost" = Campaign -> Ad Set -> Ad, where the Ad's
 * creative references the customer's existing Page post (object_story_id).
 * https://developers.facebook.com/docs/marketing-apis
 */
class MetaAdsClient
{
    private $token;
    private $account;   // act_XXXXXXXXX

    public function __construct()
    {
        $this->token   = defined('TAPIFY_META_SYSTEM_TOKEN') ? TAPIFY_META_SYSTEM_TOKEN : '';
        $this->account = defined('TAPIFY_AD_ACCOUNT_ID') ? TAPIFY_AD_ACCOUNT_ID : '';
    }

    public function isConfigured()
    {
        return $this->token !== '' && $this->account !== '';
    }

    private function graph()
    {
        return 'https://graph.facebook.com/' . (defined('FACEBOOK_GRAPH_VERSION') ? FACEBOOK_GRAPH_VERSION : 'v21.0');
    }

    /**
     * Create + launch a boost.
     * @param array $opts name, object_story_id, budget_inr, duration_days, targeting(array)
     * @return array ['campaign_id','adset_id','ad_id']
     */
    public function createBoost(array $opts)
    {
        if (!$this->isConfigured()) {
            throw new AdsException('Ads are not configured on the server yet.', 503, 'TAPIFY ad creds missing');
        }
        $acct = $this->account;
        $name = $opts['name'] ?? ('Boost ' . date('Y-m-d H:i'));

        // 1) Campaign. Objective = AWARENESS/REACH: "show this post to as many
        // people as possible." Unlike OUTCOME_ENGAGEMENT (which Meta now treats as
        // a destination/link ad and demands a call-to-action + website URL on the
        // creative), REACH boosts the existing post as-is with no link required —
        // exactly what a "boost my post" means.
        // Budget is set at the AD SET level (not campaign/CBO), so Meta requires us
        // to explicitly declare whether ad sets may share budget. A single-ad-set
        // boost never shares — send 'false'. (http_build_query turns a PHP bool
        // false into '', so pass the string.)
        $campaign = $this->post("/{$acct}/campaigns", [
            'name'                              => $name,
            'objective'                         => 'OUTCOME_AWARENESS',
            'status'                            => 'ACTIVE',
            'special_ad_categories'             => '[]',
            'is_adset_budget_sharing_enabled'   => 'false',
        ]);
        $campaignId = $campaign['id'];

        // Steps 2+3 build on the campaign. If either fails, delete the campaign so
        // we don't strand an empty "No ads" ad set in the account (Meta creates
        // campaign→adset→ad sequentially, so a late failure orphans earlier objects).
        try {
            // 2) Ad set (lifetime budget in paise, targeting, run window).
            $lifetimePaise = (int) round(((float) $opts['budget_inr']) * 100);
            $start = time() + 120;                                   // start ~2 min out
            $end   = $start + max(1, (int) $opts['duration_days']) * 86400;
            $adset = $this->post("/{$acct}/adsets", [
                'name'              => $name . ' — set',
                'campaign_id'       => $campaignId,
                'lifetime_budget'   => $lifetimePaise,
                'billing_event'     => 'IMPRESSIONS',
                'optimization_goal' => 'REACH',
                'bid_strategy'      => 'LOWEST_COST_WITHOUT_CAP',
                'targeting'         => json_encode($opts['targeting'] ?? ['geo_locations' => ['countries' => ['IN']]]),
                'start_time'        => date('c', $start),
                'end_time'          => date('c', $end),
                'status'            => 'ACTIVE',
            ]);
            $adsetId = $adset['id'];

            // 3) Ad — creative references the existing Page post.
            $ad = $this->post("/{$acct}/ads", [
                'name'     => $name . ' — ad',
                'adset_id' => $adsetId,
                'creative' => json_encode(['object_story_id' => $opts['object_story_id']]),
                'status'   => 'ACTIVE',
            ]);

            return ['campaign_id' => $campaignId, 'adset_id' => $adsetId, 'ad_id' => $ad['id']];
        } catch (Exception $e) {
            try { $this->delete("/{$campaignId}"); } catch (Exception $ignore) {}
            throw $e;
        }
    }

    /**
     * Search Meta's ad geo-locations (cities/regions/etc.) for the location picker.
     * Meta needs a location "key" — you can't target by a plain city name — so the
     * app searches here, shows the results, and sends the chosen key(s) back in
     * targeting.cities[].key. Returns Meta's rows (key, name, type, region,
     * country_name, ...).
     */
    public function searchGeo($query, $types = null)
    {
        if (!$this->isConfigured()) {
            throw new AdsException('Ads are not configured on the server yet.', 503, 'TAPIFY ad creds missing');
        }
        // Default to cities only: every result is then a valid `cities[].key` that
        // accepts a radius. (Regions/zips need different targeting fields with no
        // radius, so mixing them in would make Meta reject the boost.)
        $locTypes = (is_array($types) && $types) ? $types : ['city'];
        $url = $this->graph() . '/search?' . http_build_query([
            'type'           => 'adgeolocation',
            'location_types' => json_encode(array_values($locTypes)),
            'q'              => $query,
            'limit'          => 20,
            'access_token'   => $this->token,
        ]);
        $res = $this->getRaw($url);
        return $res['data'] ?? [];
    }

    /**
     * Best-effort: give Tapify's system user the ADVERTISE task on a customer's
     * Page, using the Page token captured at connect time. This is what lets the
     * system user boost that Page's posts from Tapify's ad account. Idempotent —
     * re-assigning an already-assigned user succeeds. Returns true on success;
     * failures are logged and swallowed (until Meta App Review approves
     * ads_management/business_management for the login flow, this can fail —
     * the boost then surfaces Meta's own permission error as before).
     */
    public function assignPageAdvertiser($pageId, $pageToken)
    {
        if (!defined('TAPIFY_SYSTEM_USER_ID') || TAPIFY_SYSTEM_USER_ID === '' || !$pageId || !$pageToken) {
            return false;
        }
        try {
            $res = $this->post("/{$pageId}/assigned_users", [
                'user'         => TAPIFY_SYSTEM_USER_ID,
                'tasks'        => json_encode(['ADVERTISE']),
                'access_token' => $pageToken,
            ]);
            return !empty($res['success']);
        } catch (Exception $e) {
            error_log('[ADS][WARN] page_advertiser_assign_failed page=' . $pageId . ' ' . $e->getMessage());
            return false;
        }
    }

    /** Search Meta interests/behaviours for detailed targeting. */
    public function searchInterests($query)
    {
        return $this->searchDict('adinterest', $query);
    }

    /** Search Meta languages (locales); each row's `key` is the numeric locale id. */
    public function searchLocales($query)
    {
        return $this->searchDict('adlocale', $query);
    }

    private function searchDict($type, $query)
    {
        if (!$this->isConfigured()) {
            throw new AdsException('Ads are not configured on the server yet.', 503, 'TAPIFY ad creds missing');
        }
        $url = $this->graph() . '/search?' . http_build_query([
            'type'         => $type,
            'q'            => $query,
            'limit'        => 25,
            'access_token' => $this->token,
        ]);
        $res = $this->getRaw($url);
        return $res['data'] ?? [];
    }

    /** Recent published posts for a Page (using the page token from the connection). */
    public function listPagePosts($pageId, $pageToken, $limit = 15)
    {
        $url = $this->graph() . "/{$pageId}/published_posts?" . http_build_query([
            'fields'       => 'id,message,full_picture,created_time',
            'limit'        => (int) $limit,
            'access_token' => $pageToken,
        ]);
        $res = $this->getRaw($url);
        return $res['data'] ?? [];
    }

    /** Spend/reach/impressions/clicks for a campaign. */
    public function campaignInsights($campaignId)
    {
        $url = $this->graph() . "/{$campaignId}/insights?" . http_build_query([
            'fields'       => 'spend,reach,impressions,clicks,cpc,ctr',
            'access_token' => $this->token,
        ]);
        $res = $this->getRaw($url);
        return $res['data'][0] ?? null;
    }

    public function setCampaignStatus($campaignId, $status)   // ACTIVE | PAUSED
    {
        return $this->post("/{$campaignId}", ['status' => $status]);
    }

    // ── HTTP ────────────────────────────────────────────────────────────────
    private function post($path, array $params)
    {
        // Default to the system token, but let callers pass a different one
        // (e.g. a Page token for Page-scoped calls like assigned_users).
        if (!isset($params['access_token'])) $params['access_token'] = $this->token;
        $ch = curl_init($this->graph() . $path);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($params),
            CURLOPT_TIMEOUT        => 45,
        ]);
        $raw = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);
        return $this->handle($raw, $code, $err, $path);
    }

    private function delete($path)
    {
        $ch = curl_init($this->graph() . $path . '?access_token=' . urlencode($this->token));
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => 'DELETE',
            CURLOPT_TIMEOUT        => 30,
        ]);
        $raw = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);
        return $this->handle($raw, $code, $err, $path);
    }

    private function getRaw($url)
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 45]);
        $raw = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);
        return $this->handle($raw, $code, $err, $url);
    }

    private function handle($raw, $code, $err, $ctx)
    {
        if ($raw === false) {
            throw new AdsException('Could not reach Meta Ads. Please try again.', 504, "curl: {$err}");
        }
        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            throw new AdsException('Unexpected response from Meta Ads.', 502, 'non-JSON: ' . substr((string) $raw, 0, 300));
        }
        if ($code >= 400 || isset($decoded['error'])) {
            $msg = $decoded['error']['error_user_msg'] ?? ($decoded['error']['message'] ?? 'ads api error');
            error_log('[ADS][ERROR] ' . json_encode(['ctx' => $ctx, 'status' => $code, 'msg' => $msg]));
            // error_user_msg is safe to show; otherwise stay generic.
            $safe = isset($decoded['error']['error_user_msg'])
                ? $msg : 'Meta rejected the ad. Please check the post and budget, then try again.';
            throw new AdsException($safe, $code >= 400 ? $code : 502, "ads: {$msg}");
        }
        return $decoded;
    }
}
