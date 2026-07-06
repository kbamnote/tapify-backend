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
}
