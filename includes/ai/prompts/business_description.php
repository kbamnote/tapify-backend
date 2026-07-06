<?php
/**
 * Prompt + shaper for the "AI Business Description" feature.
 * Input : business_name, category, city, services, target_customers
 * Output: google_description, about_us, short_description, professional_summary
 */

function ai_prompt_business_description(array $input)
{
    $name     = PromptBuilder::field($input, 'business_name');
    $category = PromptBuilder::field($input, 'category');
    $city     = PromptBuilder::field($input, 'city', 'their city');
    $services = PromptBuilder::field($input, 'services', 'their core services');
    $audience = PromptBuilder::field($input, 'target_customers', 'local customers');

    return <<<PROMPT
You are an expert local-SEO copywriter helping a small business improve its online presence and Google Business Profile.

Business details:
- Name: {$name}
- Category: {$category}
- City / Service area: {$city}
- Services: {$services}
- Target customers: {$audience}

Write natural, benefit-driven marketing copy in four formats. Be specific to the business; avoid clichés, hype and keyword stuffing. Use the city name where it reads naturally for local SEO.

Return ONLY a valid JSON object (no markdown, no commentary) with EXACTLY these keys:
{
  "google_description": "≈700-750 chars, optimised for a Google Business Profile 'from the business' description",
  "about_us": "2 short paragraphs suitable for a website About Us section",
  "short_description": "one punchy sentence, max 150 chars",
  "professional_summary": "a formal 2-3 sentence company summary for directories/proposals"
}
PROMPT;
}

function ai_shape_business_description(array $d)
{
    $get = function ($k) use ($d) { return isset($d[$k]) ? trim((string) $d[$k]) : ''; };
    return [
        'google_description'   => $get('google_description'),
        'about_us'             => $get('about_us'),
        'short_description'    => $get('short_description'),
        'professional_summary' => $get('professional_summary'),
    ];
}
