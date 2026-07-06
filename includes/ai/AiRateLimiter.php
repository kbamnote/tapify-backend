<?php
/**
 * TAPIFY AI Growth Center — per-user rate limiter.
 *
 * Guards LIVE provider generations (cache hits are free and never checked) so a
 * single authenticated account cannot drive unbounded paid provider spend by
 * varying its input to bypass the cache. Counts the user's own ai_history rows
 * in sliding windows — no extra table needed.
 *
 * Fails OPEN: if the count query errors (e.g. table missing), generation is
 * allowed rather than blocking the whole feature on an infra hiccup.
 */
class AiRateLimiter
{
    /** @var PDO */
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * @throws AiException (HTTP 429) when a window limit is exceeded.
     */
    public function assertWithinLimits($userId)
    {
        $perMin = defined('AI_RATE_PER_MIN') ? (int) AI_RATE_PER_MIN : 15;
        $perDay = defined('AI_RATE_PER_DAY') ? (int) AI_RATE_PER_DAY : 200;
        if ($perMin <= 0 && $perDay <= 0) {
            return; // limits disabled
        }

        try {
            $stmt = $this->db->prepare(
                "SELECT
                    SUM(created_at >= (NOW() - INTERVAL 60 SECOND)) AS last_min,
                    COUNT(*) AS last_day
                 FROM ai_history
                 WHERE user_id = ? AND created_at >= (NOW() - INTERVAL 1 DAY)"
            );
            $stmt->execute([$userId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
            $lastMin = (int) ($row['last_min'] ?? 0);
            $lastDay = (int) ($row['last_day'] ?? 0);
        } catch (Exception $e) {
            AiLogger::warn('ratelimit.count_failed', ['error' => $e->getMessage()]);
            return; // fail open
        }

        if ($perMin > 0 && $lastMin >= $perMin) {
            AiLogger::warn('ratelimit.per_minute', ['user_id' => $userId, 'count' => $lastMin]);
            throw new AiException('You are generating too fast. Please wait a moment and try again.', 429);
        }
        if ($perDay > 0 && $lastDay >= $perDay) {
            AiLogger::warn('ratelimit.per_day', ['user_id' => $userId, 'count' => $lastDay]);
            throw new AiException("You've reached today's AI generation limit. Please try again tomorrow.", 429);
        }
    }
}
