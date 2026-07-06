<?php
/**
 * TAPIFY AI Growth Center — OpenRouter provider (bonus).
 * OpenRouter exposes an OpenAI-compatible endpoint and can route to Gemini,
 * GPT or Claude by model id. Reuses the OPENROUTER_API_KEY already configured
 * for the reviews feature. Select with AI_PROVIDER=openrouter.
 */
class OpenRouterProvider extends BaseHttpProvider
{
    private $apiKey;
    private $model;

    public function __construct($apiKey = null, $model = null)
    {
        $this->apiKey = $apiKey !== null ? $apiKey : (defined('OPENROUTER_API_KEY') ? OPENROUTER_API_KEY : '');
        $this->model  = $model  !== null ? $model  : (getenv('OPENROUTER_MODEL') ?: 'google/gemini-2.5-flash');
    }

    public function name()  { return 'openrouter'; }
    public function model() { return $this->model; }

    public function generate($prompt, array $options = [])
    {
        if (empty($this->apiKey)) {
            throw new AiException('The AI service is not configured yet.', 500, 'OPENROUTER_API_KEY is empty');
        }

        $body = [
            'model'       => $this->model,
            'messages'    => [['role' => 'user', 'content' => $prompt]],
            'temperature' => isset($options['temperature']) ? (float) $options['temperature'] : 0.7,
            'max_tokens'  => isset($options['max_tokens']) ? (int) $options['max_tokens'] : 2048,
        ];
        if (!empty($options['json'])) {
            $body['response_format'] = ['type' => 'json_object'];
        }

        $headers = ['Authorization: Bearer ' . $this->apiKey];
        if (defined('SITE_URL')) {
            $headers[] = 'HTTP-Referer: ' . SITE_URL;
            $headers[] = 'X-Title: Tapify AI Growth Center';
        }

        $decoded = $this->postJson('https://openrouter.ai/api/v1/chat/completions', $headers, $body, 'openrouter');

        $text = $decoded['choices'][0]['message']['content'] ?? null;
        if ($text === null || $text === '') {
            throw new AiException('The AI returned an empty response. Please try again.', 502,
                'openrouter missing message content');
        }

        return $text;
    }
}
