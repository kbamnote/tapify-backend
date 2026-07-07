<?php
/**
 * TAPIFY Social Publishing — orchestration service.
 * The single entry point endpoints use: build connect URLs, complete OAuth,
 * list/remove connections, and publish (now or scheduled) with per-target
 * results. Provider-agnostic — talks only to the interface + factory + repo.
 */
class SocialService
{
    /** @var PDO */ private $db;
    /** @var SocialRepo */ private $repo;

    public function __construct(PDO $db)
    {
        $this->db   = $db;
        $this->repo = new SocialRepo($db);
    }

    // ── Connect ───────────────────────────────────────────────────────────────
    public function buildConnectUrl($userId, $platform)
    {
        if (!in_array($platform, SocialProviderFactory::connectable(), true)) {
            throw new SocialException('That platform is not available yet.', 400, "not connectable: {$platform}");
        }
        $provider = SocialProviderFactory::make($platform);
        if (!$provider->isConfigured()) {
            throw new SocialException(ucfirst($platform) . ' is not configured on the server yet.', 503, 'app creds empty');
        }
        $state = $this->repo->createState($userId, $platform);
        return $provider->buildAuthUrl($state);
    }

    public function completeOAuth($code, $state)
    {
        $st = $this->repo->consumeState($state);
        if (!$st) {
            throw new SocialException('This sign-in link has expired. Please try connecting again.', 400, 'bad state');
        }
        $provider = SocialProviderFactory::make($st['platform']);
        $connections = $provider->authorize($code);
        foreach ($connections as $c) {
            $this->repo->upsertConnection($st['user_id'], $c);
        }
        return $st['user_id'];
    }

    /** Connections without tokens (safe for the app). */
    public function listConnections($userId)
    {
        return array_map(function ($c) {
            return [
                'id'           => (int) $c['id'],
                'platform'     => $c['platform'],
                'account_id'   => $c['account_id'],
                'account_name' => $c['account_name'],
                'avatar'       => $c['account_avatar'],
                'connected_at' => $c['connected_at'],
            ];
        }, $this->repo->listConnections($userId));
    }

    public function disconnect($userId, $connectionId)
    {
        return $this->repo->deleteConnection($userId, $connectionId);
    }

    // ── Publish ───────────────────────────────────────────────────────────────
    /**
     * @param array $media         [['type'=>'image|video','url'=>...]]
     * @param int[] $connectionIds target connections (must belong to the user)
     * @param string|null $scheduledAt  future 'Y-m-d H:i:s' or parseable time; null = now
     */
    public function publish($userId, $caption, array $media, array $connectionIds, $scheduledAt = null)
    {
        $caption = trim((string) $caption);
        if ($caption === '' && !$media) {
            throw new SocialException('Add a caption or media before posting.', 422, 'empty post');
        }

        // Keep only connections the user actually owns.
        $conns = [];
        foreach (array_unique(array_map('intval', $connectionIds)) as $cid) {
            $c = $this->repo->getConnection($userId, $cid);
            if ($c) $conns[] = $c;
        }
        if (!$conns) {
            throw new SocialException('Select at least one connected account.', 422, 'no valid targets');
        }

        // Scheduled (future) — store as pending, the cron worker delivers it.
        // Store the instant in UTC (via gmdate) so the cron's UTC_TIMESTAMP()
        // comparison is correct regardless of the DB/PHP session timezone.
        // ($when is a real epoch — strtotime parses the app's local time in PHP's
        // Asia/Kolkata zone, which matches the user's intent.)
        $when = $scheduledAt ? strtotime($scheduledAt) : 0;
        if ($when && $when > time() + 30) {
            $at = gmdate('Y-m-d H:i:s', $when);
            $postId = $this->repo->createPost($userId, $caption, $media, 'scheduled', $at);
            foreach ($conns as $c) $this->repo->addTarget($postId, $c['id'], $c['platform']);
            return ['post_id' => $postId, 'status' => 'scheduled', 'scheduled_at' => $at];
        }

        // Post now.
        $postId = $this->repo->createPost($userId, $caption, $media, 'publishing', null);
        foreach ($conns as $c) $this->repo->addTarget($postId, $c['id'], $c['platform']);
        $status = $this->deliverPost([
            'id' => $postId, 'user_id' => $userId, 'caption' => $caption, 'media_json' => json_encode($media),
        ]);
        return ['post_id' => $postId, 'status' => $status];
    }

    /** Deliver a post's pending targets. Shared by publish-now and the cron. */
    private function deliverPost(array $post)
    {
        $userId  = $post['user_id'];
        $caption = $post['caption'];
        $media   = $post['media_json'] ? (json_decode($post['media_json'], true) ?: []) : [];
        $content = ['caption' => $caption, 'media' => $media];

        $ok = 0; $fail = 0;
        foreach ($this->repo->pendingTargets($post['id']) as $t) {
            $conn = $this->repo->getConnection($userId, $t['connection_id']);
            if (!$conn) {
                $this->repo->updateTarget($t['id'], 'failed', null, null, 'Account was disconnected.');
                $fail++;
                continue;
            }
            try {
                $provider = SocialProviderFactory::make($conn['platform']);
                $res = $provider->publish($conn, $content);
                $this->repo->updateTarget($t['id'], 'published', $res['remote_post_id'] ?? null, $res['remote_url'] ?? null, null);
                $ok++;
            } catch (SocialException $e) {
                $this->repo->updateTarget($t['id'], 'failed', null, null, $e->getSafeMessage());
                SocialLogger::warn('publish.target_failed', ['platform' => $conn['platform'], 'post' => $post['id']]);
                $fail++;
            } catch (Exception $e) {
                $this->repo->updateTarget($t['id'], 'failed', null, null, 'Unexpected error.');
                SocialLogger::error('publish.target_error', ['error' => $e->getMessage()]);
                $fail++;
            }
        }

        $status = ($ok > 0 && $fail === 0) ? 'published' : ($ok > 0 ? 'partial' : 'failed');
        $this->repo->updatePostStatus($post['id'], $status, gmdate('Y-m-d H:i:s')); // store UTC
        return $status;
    }

    public function listPosts($userId, $limit = 30)
    {
        return $this->repo->listPosts($userId, $limit);
    }

    /** Publish all due scheduled posts (called by the cron worker). Returns count. */
    public function runDuePosts()
    {
        $due = $this->repo->dueScheduledPosts();
        foreach ($due as $post) {
            $this->repo->updatePostStatus($post['id'], 'publishing');
            $this->deliverPost($post);
        }
        return count($due);
    }
}
