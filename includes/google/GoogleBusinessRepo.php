<?php
/**
 * TAPIFY Google Business Profile — persistence for connections + OAuth states.
 */
class GoogleBusinessRepo
{
    /** @var PDO */
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // ── OAuth state (CSRF bridge between app session and browser) ────────────
    public function createState($userId)
    {
        $state = bin2hex(random_bytes(32));
        $stmt = $this->db->prepare("INSERT INTO google_oauth_states (state, user_id) VALUES (?, ?)");
        $stmt->execute([$state, $userId]);
        // opportunistic cleanup of states older than 1 hour
        $this->db->exec("DELETE FROM google_oauth_states WHERE created_at < (NOW() - INTERVAL 1 HOUR)");
        return $state;
    }

    /** Consume a state → user_id (single use). Returns null if invalid/expired. */
    public function consumeState($state)
    {
        $stmt = $this->db->prepare(
            "SELECT user_id FROM google_oauth_states WHERE state = ? AND created_at >= (NOW() - INTERVAL 1 HOUR) LIMIT 1"
        );
        $stmt->execute([$state]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $del = $this->db->prepare("DELETE FROM google_oauth_states WHERE state = ?");
        $del->execute([$state]);
        return $row ? (int) $row['user_id'] : null;
    }

    // ── Connection ────────────────────────────────────────────────────────────
    public function get($userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM google_business_connections WHERE user_id = ? LIMIT 1");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /** Upsert tokens on (re)connect. Keeps existing refresh_token if a new one isn't provided. */
    public function upsertTokens($userId, $accessToken, $refreshToken, $expiry, $scope)
    {
        $existing = $this->get($userId);
        if ($existing) {
            $refreshToken = $refreshToken ?: $existing['refresh_token'];
            $stmt = $this->db->prepare(
                "UPDATE google_business_connections
                 SET access_token = ?, refresh_token = ?, token_expiry = ?, scope = ?, updated_at = CURRENT_TIMESTAMP
                 WHERE user_id = ?"
            );
            $stmt->execute([$accessToken, $refreshToken, $expiry, $scope, $userId]);
        } else {
            $stmt = $this->db->prepare(
                "INSERT INTO google_business_connections (user_id, access_token, refresh_token, token_expiry, scope)
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->execute([$userId, $accessToken, $refreshToken, $expiry, $scope]);
        }
    }

    public function setAccessToken($userId, $accessToken, $expiry)
    {
        $stmt = $this->db->prepare(
            "UPDATE google_business_connections SET access_token = ?, token_expiry = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ?"
        );
        $stmt->execute([$accessToken, $expiry, $userId]);
    }

    public function setLocation($userId, $accountId, $accountName, $locationId, $locationTitle)
    {
        $stmt = $this->db->prepare(
            "UPDATE google_business_connections
             SET google_account_id = ?, account_name = ?, location_id = ?, location_title = ?, updated_at = CURRENT_TIMESTAMP
             WHERE user_id = ?"
        );
        $stmt->execute([$accountId, $accountName, $locationId, $locationTitle, $userId]);
    }

    public function delete($userId)
    {
        $stmt = $this->db->prepare("DELETE FROM google_business_connections WHERE user_id = ?");
        $stmt->execute([$userId]);
    }
}
