<?php
/**
 * Prompt + shaper for the "AI Business Description" feature.
 * Input : business_name, category, city, services, target_customers
 * Output: google_description
 *
 * Single output on purpose. This used to also return about_us,
 * short_description and professional_summary, but nothing in the product could
 * write those anywhere — they were generated, shown, and copied out by hand, if
 * at all. The Google description is the one that has an Apply button and the one
 * the profile score measures, so it is the only one worth the tokens.
 *
 * The constraints below mirror what Google actually enforces (and what
 * FieldMap::sanitizeDescription strips before the write): 750 characters hard
 * cap, no links. Asking for 550-700 leaves headroom so a good answer is not
 * truncated on the way out, which is what makes the "we shortened this" notice
 * appear in the app.
 */

function ai_prompt_business_description(array $input)
{
    $name     = PromptBuilder::field($input, 'business_name');
    $category = PromptBuilder::field($input, 'category');
    $city     = PromptBuilder::field($input, 'city', 'their city');
    $services = PromptBuilder::field($input, 'services', 'their core services');
    $audience = PromptBuilder::field($input, 'target_customers', 'local customers');

    return <<<PROMPT
You are an expert local-SEO copywriter writing the "from the business" description for a Google Business Profile.

Business details:
- Name: {$name}
- Category: {$category}
- City / Service area: {$city}
- Services: {$services}
- Target customers: {$audience}

Write ONE description, 550-700 characters. Requirements:
- Open with what the business does and where, so it reads well in search results.
- Name the actual services and the city naturally — no keyword stuffing, no lists of comma-separated search terms.
- Be specific to this business. Avoid clichés ("we pride ourselves", "one-stop solution", "state of the art").
- No URLs or web addresses of any kind — Google rejects descriptions that contain them.
- No prices, discounts or special offers — these are against Google's content policy for descriptions.
- Plain text only. No markdown, no emoji, no HTML.

Return ONLY a valid JSON object (no markdown, no commentary) with EXACTLY this key:
{
  "google_description": "the description"
}
PROMPT;
}

function ai_shape_business_description(array $d)
{
    return [
        'google_description' => isset($d['google_description']) ? trim((string) $d['google_description']) : '',
    ];
}
