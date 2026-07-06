<?php
/**
 * TAPIFY AI Growth Center — Google Gemini provider (default).
 * Uses the native Generative Language API keyed by GEMINI_API_KEY.
 * https://ai.google.dev/api/generate-content
 */
class GeminiProvider extends BaseHttpProvider
{
    private $apiKey;
    private $model;

    public function __construct($apiKey = null, $model = null)
    {
        $this->apiKey = $apiKey !== null ? $apiKey : (defined('GEMINI_API_KEY') ? GEMINI_API_KEY : '');
        $this->model  = $model  !== null ? $model  : (defined('GEMINI_MODEL') ? GEMINI_MODEL : 'gemini-2.5-flash');
    }

    public function name()  { return 'gemini'; }
    public function model() { return $this->model; }

    public function generate($prompt, array $options = [])
    {
        if (empty($this->apiKey)) {
            throw new AiException('The AI service is not configured yet.', 500,
                'GEMINI_API_KEY is empty');
        }

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/'
             . rawurlencode($this->model) . ':generateContent?key=' . urlencode($this->apiKey);

        $generationConfig = [
            'temperature'     => isset($options['temperature']) ? (float) $options['temperature'] : 0.7,
            'maxOutputTokens' => isset($options['max_tokens']) ? (int) $options['max_tokens'] : 2048,
        ];
        if (!empty($options['json'])) {
            $generationConfig['responseMimeType'] = 'application/json';
        }

        $body = [
            'contents'         => [['parts' => [['text' => $prompt]]]],
            'generationConfig' => $generationConfig,
        ];

        $decoded = $this->postJson($url, [], $body, 'gemini');

        // Blocked by safety filters (no candidates returned).
        if (empty($decoded['candidates'])) {
            $reason = $decoded['promptFeedback']['blockReason'] ?? 'no candidates';
            throw new AiException('The AI could not generate a response for this input. Try rephrasing.',
                502, 'gemini empty candidates: ' . $reason);
        }

        $text = $decoded['candidates'][0]['content']['parts'][0]['text'] ?? null;
        if ($text === null || $text === '') {
            throw new AiException('The AI returned an empty response. Please try again.', 502,
                'gemini missing candidate text');
        }

        return $text;
    }
}
