<?php
/**
 * TAPIFY Social Publishing — persistence (connections, states, posts, targets).
 */
class SocialRepo
{
    /** @var PDO */
    private $db;

    public function __construct(PDO $db) { $this->db = $db; }

    // ── OAuth state ───────────────────────────────────────────────────────────
    public function createState($userId, $platform)
    {
        $state = bin2hex(random_bytes(32));
        $stmt = $this->db->prepare("INSERT INTO social_oauth_states (state, user_id, platform) VALUES (?, ?, ?)");
        $stmt->execute([$state, $userId, $platform]);
        $this->db->exec("DELETE FROM social_oauth_states WHERE created_at < (NOW() - INTERVAL 1 HOUR)");
        return $state;
    }

    /** @return array|null ['user_id','platform'] */
    public function consumeState($state)
    {
        $stmt = $this->db->prepare(
            "SELECT user_id, platform FROM social_oauth_states WHERE state = ? AND created_at >= (NOW() - INTERVAL 1 HOUR) LIMIT 1"
        );
        $stmt->execute([$state]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $del = $this->db->prepare("DELETE FROM social_oauth_states WHERE state = ?");
        $del->execute([$state]);
        return $row ? ['user_id' => (int) $row['user_id'], 'platform' => $row['platform']] : null;
    }

    // ── Connections ───────────────────────────────────────────────────────────
    public function upsertConnection($userId, array $c)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO social_connections
               (user_id, platform, account_id, account_name, account_avatar, access_token, refresh_token, token_expiry, scope, extra_json, is_active)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
             ON DUPLICATE KEY UPDATE
               account_name = VALUES(account_name),
               account_avatar = VALUES(account_avatar),
               access_token = VALUES(access_token),
               refresh_token = VALUES(refresh_token),
               token_expiry = VALUES(token_expiry),
               scope = VALUES(scope),
               extra_json = VALUES(extra_json),
               is_active = 1,
               updated_at = CURRENT_TIMESTAMP"
        );
        $stmt->execute([
            $userId, $c['platform'], $c['account_id'], $c['account_name'] ?? null, $c['account_avatar'] ?? null,
            $c['access_token'] ?? null, $c['refresh_token'] ?? null, $c['token_expiry'] ?? null,
            $c['scope'] ?? null, isset($c['extra']) ? json_encode($c['extra']) : null,
        ]);
    }

    public function listConnections($userId)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM social_connections WHERE user_id = ? AND is_active = 1 ORDER BY platform, account_name"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getConnection($userId, $connectionId)
    {
        $stmt = $this->db->prepare("SELECT * FROM social_connections WHERE id = ? AND user_id = ? LIMIT 1");
        $stmt->execute([(int) $connectionId, $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function deleteConnection($userId, $connectionId)
    {
        $stmt = $this->db->prepare("DELETE FROM social_connections WHERE id = ? AND user_id = ?");
        $stmt->execute([(int) $connectionId, $userId]);
        return $stmt->rowCount() > 0;
    }

    // ── Posts + targets ───────────────────────────────────────────────────────
    public function createPost($userId, $caption, array $media, $status, $scheduledAt = null)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO social_posts (user_id, caption, media_json, status, scheduled_at) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$userId, $caption, json_encode($media), $status, $scheduledAt]);
        return (int) $this->db->lastInsertId();
    }

    public function addTarget($postId, $connectionId, $platform)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO social_post_targets (post_id, connection_id, platform, status) VALUES (?, ?, ?, 'pending')"
        );
        $stmt->execute([$postId, (int) $connectionId, $platform]);
        return (int) $this->db->lastInsertId();
    }

    public function updateTarget($targetId, $status, $remoteId = null, $remoteUrl = null, $error = null)
    {
        $stmt = $this->db->prepare(
            "UPDATE social_post_targets SET status = ?, remote_post_id = ?, remote_url = ?, error = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?"
        );
        $stmt->execute([$status, $remoteId, $remoteUrl, $error, (int) $targetId]);
    }

    public function updatePostStatus($postId, $status, $publishedAt = null)
    {
        $stmt = $this->db->prepare(
            "UPDATE social_posts SET status = ?, published_at = COALESCE(?, published_at), updated_at = CURRENT_TIMESTAMP WHERE id = ?"
        );
        $stmt->execute([$status, $publishedAt, (int) $postId]);
    }

    public function listPosts($userId, $limit = 30)
    {
        $limit = max(1, min(100, (int) $limit));
        $stmt = $this->db->prepare("SELECT * FROM social_posts WHERE user_id = ? ORDER BY id DESC LIMIT " . $limit);
        $stmt->execute([$userId]);
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($posts as &$p) {
            $p['media'] = $p['media_json'] ? json_decode($p['media_json'], true) : [];
            unset($p['media_json']);
            $ts = $this->db->prepare(
                "SELECT t.platform, t.status, t.remote_url, t.error, c.account_name
                 FROM social_post_targets t LEFT JOIN social_connections c ON c.id = t.connection_id
                 WHERE t.post_id = ?"
            );
            $ts->execute([$p['id']]);
            $p['targets'] = $ts->fetchAll(PDO::FETCH_ASSOC);
        }
        return $posts;
    }

    /** Scheduled posts whose time has arrived (for the cron worker). */
    public function dueScheduledPosts($limit = 20)
    {
        $limit = max(1, min(100, (int) $limit));
        // scheduled_at is stored in UTC (see SocialService::publish), so compare
        // against UTC_TIMESTAMP() — correct regardless of the DB session timezone.
        $stmt = $this->db->query(
            "SELECT * FROM social_posts WHERE status = 'scheduled' AND scheduled_at <= UTC_TIMESTAMP() ORDER BY scheduled_at ASC LIMIT " . $limit
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function pendingTargets($postId)
    {
        $stmt = $this->db->prepare("SELECT * FROM social_post_targets WHERE post_id = ? AND status = 'pending'");
        $stmt->execute([(int) $postId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
