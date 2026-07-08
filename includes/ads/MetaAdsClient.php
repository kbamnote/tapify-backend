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

        // 1) Campaign (engagement objective).
        $campaign = $this->post("/{$acct}/campaigns", [
            'name'                  => $name,
            'objective'             => 'OUTCOME_ENGAGEMENT',
            'status'                => 'ACTIVE',
            'special_ad_categories' => '[]',
        ]);
        $campaignId = $campaign['id'];

        // 2) Ad set (lifetime budget in paise, targeting, run window).
        $lifetimePaise = (int) round(((float) $opts['budget_inr']) * 100);
        $start = time() + 120;                                   // start ~2 min out
        $end   = $start + max(1, (int) $opts['duration_days']) * 86400;
        $adset = $this->post("/{$acct}/adsets", [
            'name'              => $name . ' — set',
            'campaign_id'       => $campaignId,
            'lifetime_budget'   => $lifetimePaise,
            'billing_event'     => 'IMPRESSIONS',
            'optimization_goal' => 'POST_ENGAGEMENT',
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
        $params['access_token'] = $this->token;
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
