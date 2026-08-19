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

    /* --------------------------------------------------------- score history */

    /** Most recent recorded score for this user, or null. */
    /**
     * Most recent recorded score of one kind, or null.
     *
     * @param string $kind 'profile' | 'marketing'. Both live in one table, so
     *   without this filter the delta would compare a marketing score against
     *   yesterday's profile score and report a meaningless jump.
     *
     * Falls back to an unfiltered read if the `kind` column has not been
     * migrated yet, so history keeps working on an un-migrated database rather
     * than silently returning null forever.
     */
    public function lastScore($userId, $kind = 'profile')
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT score, created_at FROM google_business_scores
                  WHERE user_id = ? AND kind = ? ORDER BY id DESC LIMIT 1"
            );
            $stmt->execute([$userId, $kind]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Exception $e) {
            try {
                $stmt = $this->db->prepare(
                    "SELECT score, created_at FROM google_business_scores WHERE user_id = ? ORDER BY id DESC LIMIT 1"
                );
                $stmt->execute([$userId]);
                return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
            } catch (Exception $e2) {
                // Table not migrated at all — a missing history must not break
                // the score itself, which is useful on its own.
                return null;
            }
        }
    }

    public function recordScore($userId, $locationId, $score, array $breakdown, $kind = 'profile')
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO google_business_scores (user_id, location_id, score, breakdown, kind)
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->execute([$userId, $locationId, (int)$score, json_encode($breakdown), $kind]);
        } catch (Exception $e) {
            try {
                // Pre-migration database: write without the discriminator rather
                // than lose the row entirely.
                $stmt = $this->db->prepare(
                    "INSERT INTO google_business_scores (user_id, location_id, score, breakdown) VALUES (?, ?, ?, ?)"
                );
                $stmt->execute([$userId, $locationId, (int)$score, json_encode($breakdown)]);
            } catch (Exception $e2) {
                // Recording is a nicety, the score is the product.
            }
        }
    }

    /* ------------------------------------------------------- review replies */

    /** True when we have already replied to this review (any source). */
    public function hasReplied($reviewId)
    {
        try {
            $stmt = $this->db->prepare("SELECT id FROM google_review_replies WHERE review_id = ? LIMIT 1");
            $stmt->execute([$reviewId]);
            return (bool)$stmt->fetch();
        } catch (Exception $e) {
            // If we cannot tell, assume we HAVE replied. Skipping a reply is
            // recoverable; double-posting in public under the customer's name
            // is not.
            return true;
        }
    }

    public function recordReply($userId, $reviewId, $stars, $text, $source = 'manual')
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO google_review_replies (user_id, review_id, star_rating, reply_text, source)
                 VALUES (?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE reply_text = VALUES(reply_text), source = VALUES(source)"
            );
            $stmt->execute([$userId, $reviewId, $stars !== null ? (int)$stars : null, $text, $source]);
        } catch (Exception $e) {
            GoogleLogger::warn('reply.record_failed', ['review' => substr((string)$reviewId, -24)]);
        }
    }

    public function setAutoReply($userId, $enabled, $minStars)
    {
        $stmt = $this->db->prepare(
            "UPDATE google_business_connections SET auto_reply_enabled = ?, auto_reply_min_stars = ? WHERE user_id = ?"
        );
        $stmt->execute([(int)$enabled, (int)$minStars, $userId]);
    }

    /* ------------------------------------------------------ review requests */

    /**
     * Record that a customer was asked for a review.
     *
     * Recorded on the owner's say-so, not on delivery: the message leaves from
     * their own WhatsApp or SMS app, so we genuinely cannot know whether it was
     * sent. Logging the intent is honest and still does the job the log exists
     * for, which is preventing the same person being asked twice.
     */
    public function logReviewRequest($userId, $locationId, $name, $phone, $channel)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO google_review_requests (user_id, location_id, customer_name, phone, channel)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$userId, $locationId, $name !== '' ? $name : null, $phone, $channel]);
        return (int)$this->db->lastInsertId();
    }

    /** This user's requests, newest first. */
    public function reviewRequests($userId, $limit = 100)
    {
        try {
            $limit = max(1, min(500, (int)$limit));
            // Interpolated because MySQL will not bind a LIMIT under emulated
            // prepares; cast above makes it safe.
            $stmt = $this->db->prepare(
                "SELECT id, customer_name, phone, channel, created_at
                   FROM google_review_requests
                  WHERE user_id = ?
                  ORDER BY id DESC
                  LIMIT {$limit}"
            );
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Table not migrated yet — an empty log must not break sending.
            return [];
        }
    }

    /** When this user last asked this number, or null. */
    public function lastRequestTo($userId, $phone)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT created_at FROM google_review_requests
                  WHERE user_id = ? AND phone = ? ORDER BY id DESC LIMIT 1"
            );
            $stmt->execute([$userId, $phone]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? $row['created_at'] : null;
        } catch (Exception $e) {
            return null;
        }
    }

    /** Connections that have opted in to automatic replying (for the cron). */
    public function autoReplyUsers()
    {
        $stmt = $this->db->query(
            "SELECT user_id, auto_reply_min_stars FROM google_business_connections
              WHERE auto_reply_enabled = 1 AND location_id IS NOT NULL AND location_id <> ''"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
