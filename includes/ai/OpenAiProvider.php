<?php
/**
 * TAPIFY AI Growth Center — OpenAI provider (future ready).
 * Chat Completions API keyed by OPENAI_API_KEY. Only used when AI_PROVIDER=openai.
 */
class OpenAiProvider extends BaseHttpProvider
{
    private $apiKey;
    private $model;

    public function __construct($apiKey = null, $model = null)
    {
        $this->apiKey = $apiKey !== null ? $apiKey : (defined('OPENAI_API_KEY') ? OPENAI_API_KEY : '');
        $this->model  = $model  !== null ? $model  : (defined('OPENAI_MODEL') ? OPENAI_MODEL : 'gpt-4o-mini');
    }

    public function name()  { return 'openai'; }
    public function model() { return $this->model; }

    public function generate($prompt, array $options = [])
    {
        if (empty($this->apiKey)) {
            throw new AiException('The AI service is not configured yet.', 500, 'OPENAI_API_KEY is empty');
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

        $decoded = $this->postJson(
            'https://api.openai.com/v1/chat/completions',
            ['Authorization: Bearer ' . $this->apiKey],
            $body,
            'openai'
        );

        $text = $decoded['choices'][0]['message']['content'] ?? null;
        if ($text === null || $text === '') {
            throw new AiException('The AI returned an empty response. Please try again.', 502,
                'openai missing message content');
        }

        return $text;
    }
}
