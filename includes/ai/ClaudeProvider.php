<?php
/**
 * TAPIFY AI Growth Center — Anthropic Claude provider (future ready).
 * Messages API keyed by ANTHROPIC_API_KEY. Only used when AI_PROVIDER=claude.
 * https://docs.anthropic.com/en/api/messages
 */
class ClaudeProvider extends BaseHttpProvider
{
    private $apiKey;
    private $model;

    public function __construct($apiKey = null, $model = null)
    {
        $this->apiKey = $apiKey !== null ? $apiKey : (defined('ANTHROPIC_API_KEY') ? ANTHROPIC_API_KEY : '');
        $this->model  = $model  !== null ? $model  : (defined('ANTHROPIC_MODEL') ? ANTHROPIC_MODEL : 'claude-haiku-4-5-20251001');
    }

    public function name()  { return 'claude'; }
    public function model() { return $this->model; }

    public function generate($prompt, array $options = [])
    {
        if (empty($this->apiKey)) {
            throw new AiException('The AI service is not configured yet.', 500, 'ANTHROPIC_API_KEY is empty');
        }

        // Claude has no JSON mode; the prompt itself must ask for JSON. We nudge
        // it by prefilling the assistant turn with "{" when json is requested.
        $messages = [['role' => 'user', 'content' => $prompt]];
        $prefill  = !empty($options['json']);
        if ($prefill) {
            $messages[] = ['role' => 'assistant', 'content' => '{'];
        }

        $body = [
            'model'       => $this->model,
            'max_tokens'  => isset($options['max_tokens']) ? (int) $options['max_tokens'] : 2048,
            'temperature' => isset($options['temperature']) ? (float) $options['temperature'] : 0.7,
            'messages'    => $messages,
        ];

        $decoded = $this->postJson(
            'https://api.anthropic.com/v1/messages',
            [
                'x-api-key: ' . $this->apiKey,
                'anthropic-version: 2023-06-01',
            ],
            $body,
            'claude'
        );

        $text = $decoded['content'][0]['text'] ?? null;
        if ($text === null || $text === '') {
            throw new AiException('The AI returned an empty response. Please try again.', 502,
                'claude missing content text');
        }

        // Re-attach the "{" we prefilled so downstream JSON parsing sees valid JSON.
        return $prefill ? '{' . $text : $text;
    }
}
