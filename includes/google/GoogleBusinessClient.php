<?php
/**
 * TAPIFY Google Business Profile — API client.
 * Wraps the Account Management + Business Information APIs and transparently
 * refreshes the access token when it's near expiry.
 */
class GoogleBusinessClient
{
    const ACCOUNTS_BASE = 'https://mybusinessaccountmanagement.googleapis.com/v1';
    const INFO_BASE     = 'https://mybusinessbusinessinformation.googleapis.com/v1';
    // Reviews were never migrated off the legacy v4 surface — there is no v1
    // equivalent. It is a SEPARATE API in Google Cloud Console ("Google My
    // Business API") and must be enabled there, even though the OAuth scope
    // (business.manage) already covers it. Review calls 404 until it is on.
    const REVIEWS_BASE  = 'https://mybusiness.googleapis.com/v4';

    /** @var PDO */
    private $db;
    /** @var GoogleBusinessRepo */
    private $repo;
    /** @var array connection row */
    private $conn;

    public function __construct(PDO $db, GoogleBusinessRepo $repo, array $connection)
    {
        $this->db   = $db;
        $this->repo = $repo;
        $this->conn = $connection;
    }

    /** Valid access token, refreshing via the refresh_token when expired. */
    private function accessToken()
    {
        $expiry = $this->conn['token_expiry'] ? strtotime($this->conn['token_expiry']) : 0;
        // refresh if missing or within 60s of expiry
        if (empty($this->conn['access_token']) || $expiry === 0 || $expiry <= (time() + 60)) {
            if (empty($this->conn['refresh_token'])) {
                throw new GoogleException('Your Google connection expired. Please reconnect Google Business Profile.', 401,
                    'no refresh_token to renew access');
            }
            $tok = GoogleOAuth::refresh($this->conn['refresh_token']);
            $access = $tok['access_token'] ?? null;
            if (!$access) {
                throw new GoogleException('Your Google connection expired. Please reconnect Google Business Profile.', 401,
                    'refresh returned no access_token');
            }
            $newExpiry = date('Y-m-d H:i:s', time() + (int) ($tok['expires_in'] ?? 3600));
            $this->repo->setAccessToken($this->conn['user_id'], $access, $newExpiry);
            $this->conn['access_token'] = $access;
            $this->conn['token_expiry'] = $newExpiry;
        }
        return $this->conn['access_token'];
    }

    /** All GBP accounts the user manages. */
    public function listAccounts()
    {
        $res = GoogleHttp::get(self::ACCOUNTS_BASE . '/accounts', $this->accessToken());
        return $res['accounts'] ?? [];
    }

    /** Locations under an account (accountName like "accounts/123"). */
    public function listLocations($accountName)
    {
        $readMask = 'name,title,storefrontAddress';
        $url = self::INFO_BASE . '/' . $accountName . '/locations?pageSize=100&readMask=' . rawurlencode($readMask);
        $res = GoogleHttp::get($url, $this->accessToken());
        return $res['locations'] ?? [];
    }

    /** Full detail for one location (locationName like "locations/456"). */
    public function getLocation($locationName, $readMask)
    {
        $url = self::INFO_BASE . '/' . $locationName . '?readMask=' . rawurlencode($readMask);
        return GoogleHttp::get($url, $this->accessToken());
    }

    /** PATCH selected fields on a location. */
    public function patchLocation($locationName, array $updateMask, array $body)
    {
        $url = self::INFO_BASE . '/' . $locationName . '?updateMask=' . rawurlencode(implode(',', $updateMask));
        return GoogleHttp::patch($url, $this->accessToken(), $body);
    }

    /* ------------------------------------------------------------- reviews */

    /**
     * Reviews for a location, newest first.
     *
     * The v4 path needs BOTH the account and the location — unlike the v1 APIs,
     * a bare "locations/456" is not addressable here.
     *
     * @param string $accountName  "accounts/123"
     * @param string $locationName "locations/456"
     */
    public function listReviews($accountName, $locationName, $pageSize = 50)
    {
        $pageSize = max(1, min(50, (int)$pageSize));
        $url = self::REVIEWS_BASE . '/' . $accountName . '/' . $locationName
             . '/reviews?orderBy=' . rawurlencode('updateTime desc') . '&pageSize=' . $pageSize;
        $res = GoogleHttp::get($url, $this->accessToken());
        return [
            'reviews'      => $res['reviews'] ?? [],
            'total'        => (int)($res['totalReviewCount'] ?? 0),
            'average'      => isset($res['averageRating']) ? (float)$res['averageRating'] : null,
        ];
    }

    /**
     * Create or replace the owner's reply to a review.
     *
     * PUT is intentional: Google treats the reply as a single sub-resource, so
     * replying twice edits the existing reply rather than adding a second one.
     * There is no way to have two replies on one review.
     *
     * @param string $reviewName full name: "accounts/1/locations/2/reviews/3"
     */
    public function replyToReview($reviewName, $comment)
    {
        $url = self::REVIEWS_BASE . '/' . $reviewName . '/reply';
        return GoogleHttp::put($url, $this->accessToken(), ['comment' => (string)$comment]);
    }
}
