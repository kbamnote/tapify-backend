<?php
/**
 * TAPIFY AI Growth Center — orchestration service.
 *
 * The single entry point business code uses. It is provider-agnostic: it talks
 * to AiProviderInterface (via the factory), PromptBuilder, AiCache and
 * AiHistory only. Swapping Gemini -> OpenAI/Claude changes nothing here.
 */
class AiService
{
    /** @var PDO */
    private $db;
    /** @var AiCache */
    private $cache;
    /** @var AiHistory */
    private $history;
    /** @var AiRateLimiter */
    private $rateLimiter;

    public function __construct(PDO $db)
    {
        $this->db          = $db;
        $this->cache       = new AiCache($db);
        $this->history     = new AiHistory($db);
        $this->rateLimiter = new AiRateLimiter($db);
    }

    /**
     * Generate (or return cached) AI content for a feature.
     *
     * @param int    $userId
     * @param string $feature     endpoint key, e.g. 'business-description'
     * @param array  $input       normalised, validated request fields
     * @param bool   $regenerate  bypass + overwrite cache
     * @return array response envelope for the app
     * @throws AiException
     */
    public function generate($userId, $feature, array $input, $regenerate = false)
    {
        $normalized = $this->normalize($input);
        $hash       = hash('sha256', $feature . '|' . json_encode($normalized, JSON_UNESCAPED_UNICODE));

        // 1) Serve from cache unless the user asked to regenerate.
        if (!$regenerate) {
            $cached = $this->cache->get($userId, $feature, $hash);
            if ($cached !== null) {
                return $this->envelope($feature, $cached['output'], true, false,
                    $cached['provider'], $cached['model'], $cached['history_id'], $cached['created_at']);
            }
        }

        // 2) Cache miss → this will be a live (billable) call. Enforce per-user
        //    limits here so cache hits stay free/unthrottled.
        $this->rateLimiter->assertWithinLimits($userId);

        // 3) Build the prompt (feature-owned) and call the configured provider.
        $prompt   = PromptBuilder::build($feature, $normalized);
        $provider = AiProviderFactory::make();

        try {
            $maxTokens = defined('AI_MAX_OUTPUT_TOKENS') ? (int) AI_MAX_OUTPUT_TOKENS : 8192;
            $raw     = $provider->generate($prompt, ['json' => true, 'max_tokens' => $maxTokens]);
            $decoded = AiResponse::json($raw);
            $result  = PromptBuilder::shape($feature, $decoded);
        } catch (AiException $e) {
            // Record the failure for observability, then surface it.
            $this->history->log($userId, $feature, $normalized, null,
                $provider->name(), $provider->model(), 'error', $e->getMessage());
            throw $e;
        }

        // 4) Persist history + cache the fresh result.
        $historyId = $this->history->log($userId, $feature, $normalized, $result,
            $provider->name(), $provider->model(), 'success', null);
        $this->cache->put($userId, $feature, $hash, $normalized, $result,
            $provider->name(), $provider->model(), $historyId);

        return $this->envelope($feature, $result, false, $regenerate,
            $provider->name(), $provider->model(), $historyId, date('Y-m-d H:i:s'));
    }

    /** Trim strings, drop empties, sort keys → stable cache key. */
    private function normalize(array $input)
    {
        $out = [];
        foreach ($input as $k => $v) {
            if (is_string($v)) {
                $v = trim($v);
                if ($v === '') continue;
            }
            $out[$k] = $v;
        }
        ksort($out);
        return $out;
    }

    private function envelope($feature, $result, $cached, $regenerated, $provider, $model, $historyId, $generatedAt)
    {
        return [
            'feature'      => $feature,
            'result'       => $result,
            'cached'       => (bool) $cached,
            'regenerated'  => (bool) $regenerated,
            'provider'     => $provider,
            'model'        => $model,
            'history_id'   => $historyId !== null ? (int) $historyId : null,
            'generated_at' => $generatedAt,
        ];
    }
}
