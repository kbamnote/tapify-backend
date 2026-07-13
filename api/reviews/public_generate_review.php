<?php
/**
 * TAPIFY - Public Generate SEO Review
 * POST /backend/api/reviews/public_generate_review.php
 * Body: { business_name }
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method not allowed', 405);
}

try {
    $input = getInput();
    $businessName = sanitize($input['business_name'] ?? '');

    if (empty($businessName)) {
        $businessName = 'this business';
    }

    $apiKey = OPENROUTER_API_KEY;
    if (empty($apiKey)) {
        sendError('OpenRouter API key is not configured.', 500);
    }

    // Vary the prompt on every call so the model doesn't keep returning the same review.
    $aspects = [
        'the quality of their service',
        'how friendly and professional the staff were',
        'the overall experience from start to finish',
        'the value for money',
        'how smooth and hassle-free everything was',
        'their attention to detail',
        'how quickly and clearly they communicated',
        'the warm, welcoming atmosphere',
        'how they went above and beyond',
    ];
    $tones   = ['warm and genuine', 'enthusiastic and upbeat', 'calm and sincere', 'casual and friendly', 'appreciative and heartfelt'];
    $lengths = ['1 to 2 sentences', 'about 2 sentences', '2 to 3 short sentences'];

    $aspect = $aspects[array_rand($aspects)];
    $tone   = $tones[array_rand($tones)];
    $length = $lengths[array_rand($lengths)];

    $prompt = "Write a unique, natural-sounding 5-star Google review for a business named '{$businessName}'. "
        . "Make it {$tone} in tone, {$length} long, and specifically highlight {$aspect}. "
        . "Vary the wording, the opening and the sentence structure so it never sounds templated or repeated. "
        . "Write as a real, happy customer would. No hashtags, no emojis, no quotation marks. Output only the review text.";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://openrouter.ai/api/v1/chat/completions");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    
    $payload = json_encode([
        "model" => "google/gemini-2.5-flash",
        "messages" => [
            ["role" => "user", "content" => $prompt]
        ],
        "temperature" => 1.05,   // higher sampling → more varied wording
        "top_p"       => 0.95,
        "max_tokens"  => 220,
    ]);

    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    $headers = [
        "Authorization: Bearer {$apiKey}",
        "HTTP-Referer: " . SITE_URL, // Optional, for OpenRouter rankings
        "X-Title: Tapify Reviews", // Optional, for OpenRouter rankings
        "Content-Type: application/json"
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        sendError("cURL Error #:" . $err, 500);
    }

    $response = json_decode($result, true);

    if (isset($response['error'])) {
        sendError("OpenRouter Error: " . ($response['error']['message'] ?? json_encode($response['error'])), 500);
    }

    if (isset($response['choices'][0]['message']['content'])) {
        $text = trim($response['choices'][0]['message']['content']);
        // Strip quotes if AI wraps them
        $text = trim($text, '"\'');
        sendSuccess('Review generated', ['text' => $text]);
    } else {
        sendError('Failed to parse AI response', 500);
    }

} catch (Exception $e) {
    sendError('Server error: ' . $e->getMessage(), 500);
}
