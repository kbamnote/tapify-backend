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

    $prompt = "Write a short, glowing, SEO-friendly 5-star Google review for a business named '{$businessName}'. It should sound natural, like a real customer wrote it. Do not include hashtags. Keep it under 3 sentences. Output only the review text.";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://openrouter.ai/api/v1/chat/completions");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    
    $payload = json_encode([
        "model" => "google/gemini-2.5-flash", 
        "messages" => [
            ["role" => "user", "content" => $prompt]
        ]
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
