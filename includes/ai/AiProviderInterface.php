<?php
/**
 * TAPIFY AI Growth Center — provider contract.
 *
 * The whole point of this interface: business logic (AiService) depends ONLY on
 * this contract, never on Gemini/OpenAI/Claude specifics. Adding a new provider
 * = implement this interface + register it in AiProviderFactory. Nothing else
 * in the app changes.
 */
interface AiProviderInterface
{
    /**
     * Generate a completion for a single prompt.
     *
     * @param string $prompt  Fully-built prompt (see PromptBuilder).
     * @param array  $options Optional hints:
     *                        - json (bool)        request strict-JSON output when supported
     *                        - temperature (float)
     *                        - max_tokens (int)
     * @return string Raw model text (may be wrapped in ```json fences — the
     *                caller is responsible for parsing via AiResponse).
     * @throws AiException on misconfiguration, rate-limit, timeout or API error.
     */
    public function generate($prompt, array $options = []);

    /** Short provider id, e.g. "gemini". Stored on cache/history rows. */
    public function name();

    /** The concrete model this provider will call, e.g. "gemini-2.5-flash". */
    public function model();
}
