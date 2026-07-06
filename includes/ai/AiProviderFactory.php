<?php
/**
 * TAPIFY AI Growth Center — provider factory.
 *
 * The ONE place that knows about concrete providers. Business logic asks for a
 * provider by id (or the configured default) and gets back an
 * AiProviderInterface. To add a provider: implement the interface and add a
 * case here — no other file changes.
 */
class AiProviderFactory
{
    /**
     * @param string|null $provider  Override id; null = AI_PROVIDER config default.
     * @return AiProviderInterface
     * @throws AiException on unknown provider id.
     */
    public static function make($provider = null)
    {
        $provider = strtolower(trim($provider ?: (defined('AI_PROVIDER') ? AI_PROVIDER : 'gemini')));

        switch ($provider) {
            case 'gemini':     return new GeminiProvider();
            case 'openai':     return new OpenAiProvider();
            case 'claude':     return new ClaudeProvider();
            case 'openrouter': return new OpenRouterProvider();
            default:
                throw new AiException('The AI service is misconfigured.', 500,
                    "Unknown AI_PROVIDER '{$provider}'");
        }
    }

    /** Ids that can be selected via AI_PROVIDER. */
    public static function supported()
    {
        return ['gemini', 'openai', 'claude', 'openrouter'];
    }
}
