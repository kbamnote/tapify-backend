<?php
/**
 * Prompt + shaper for the "AI Keyword Generator" feature.
 * Input : business_name, category, city, services
 * Output: primary, secondary, long_tail, local_seo, voice_search (string arrays)
 */

function ai_prompt_keywords(array $input)
{
    $name     = PromptBuilder::field($input, 'business_name');
    $category = PromptBuilder::field($input, 'category');
    $city     = PromptBuilder::field($input, 'city', 'their city');
    $services = PromptBuilder::field($input, 'services', 'their core services');

    return <<<PROMPT
You are a local-SEO keyword strategist.

Business details:
- Name: {$name}
- Category: {$category}
- City / Service area: {$city}
- Services: {$services}

Suggest realistic keywords a real customer might type or say. IMPORTANT: do NOT invent or include search-volume numbers, difficulty scores or any fake metrics — only the keyword phrases themselves. Make local keywords use the city name.

Return ONLY a valid JSON object (no markdown, no commentary) with EXACTLY these keys, each an array of 6-10 lowercase keyword strings:
{
  "primary": [],
  "secondary": [],
  "long_tail": [],
  "local_seo": [],
  "voice_search": []
}
PROMPT;
}

function ai_shape_keywords(array $d)
{
    return [
        'primary'      => AiResponse::stringList($d['primary'] ?? []),
        'secondary'    => AiResponse::stringList($d['secondary'] ?? []),
        'long_tail'    => AiResponse::stringList($d['long_tail'] ?? ($d['longtail'] ?? [])),
        'local_seo'    => AiResponse::stringList($d['local_seo'] ?? ($d['local'] ?? [])),
        'voice_search' => AiResponse::stringList($d['voice_search'] ?? ($d['voice'] ?? [])),
    ];
}
