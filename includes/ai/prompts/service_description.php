<?php
/**
 * Prompt + shaper for the "AI Service Description Generator" feature.
 * Input : service_name, business_name, category, city
 * Output: title, seo_description, short_version, long_version
 */

function ai_prompt_service_description(array $input)
{
    $service  = PromptBuilder::field($input, 'service_name');
    $name     = PromptBuilder::field($input, 'business_name', 'the business');
    $category = PromptBuilder::field($input, 'category', 'their industry');
    $city     = PromptBuilder::field($input, 'city', 'their city');

    return <<<PROMPT
You are an SEO copywriter writing a single service listing for a local business.

- Service: {$service}
- Business: {$name}
- Category: {$category}
- City / Service area: {$city}

Write clear, conversion-focused copy for THIS one service. Mention the city naturally where it helps local SEO. No keyword stuffing.

Return ONLY a valid JSON object (no markdown, no commentary) with EXACTLY these keys:
{
  "title": "a clean, professional service title",
  "seo_description": "an SEO-friendly meta-style description, 140-160 chars",
  "short_version": "1-2 sentences for a card or list",
  "long_version": "2 short paragraphs describing benefits and what's included"
}
PROMPT;
}

function ai_shape_service_description(array $d)
{
    $get = function ($k) use ($d) { return isset($d[$k]) ? trim((string) $d[$k]) : ''; };
    return [
        'title'           => $get('title'),
        'seo_description' => $get('seo_description'),
        'short_version'   => $get('short_version'),
        'long_version'    => $get('long_version'),
    ];
}
