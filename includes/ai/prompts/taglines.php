<?php
/**
 * Prompt + shaper for the "AI Tagline Generator" feature.
 * Input : business_name, category, city, services, target_customers
 * Output: taglines (array of 10 strings)
 */

function ai_prompt_taglines(array $input)
{
    $name     = PromptBuilder::field($input, 'business_name');
    $category = PromptBuilder::field($input, 'category');
    $city     = PromptBuilder::field($input, 'city', 'their city');
    $services = PromptBuilder::field($input, 'services', 'their core services');
    $audience = PromptBuilder::field($input, 'target_customers', 'local customers');

    return <<<PROMPT
You are a brand copywriter. Create memorable taglines for this business.

Business details:
- Name: {$name}
- Category: {$category}
- City / Service area: {$city}
- Services: {$services}
- Target customers: {$audience}

Write 10 short, professional, distinct taglines (each under 8 words). Vary the angle: value, trust, speed, quality, local pride, results. No hashtags, no quotation marks inside the text.

Return ONLY a valid JSON object (no markdown, no commentary) with EXACTLY this key:
{
  "taglines": ["tagline 1", "tagline 2", "... up to 10"]
}
PROMPT;
}

function ai_shape_taglines(array $d)
{
    $list = AiResponse::stringList($d['taglines'] ?? $d);
    return ['taglines' => array_slice($list, 0, 10)];
}
