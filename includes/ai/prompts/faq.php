<?php
/**
 * Prompt + shaper for the "AI FAQ Generator" feature.
 * Input : business_name, category, city, services
 * Output: faqs (array of { question, answer })
 */

function ai_prompt_faq(array $input)
{
    $name     = PromptBuilder::field($input, 'business_name', 'the business');
    $category = PromptBuilder::field($input, 'category');
    $city     = PromptBuilder::field($input, 'city', 'their city');
    $services = PromptBuilder::field($input, 'services', 'their core services');

    return <<<PROMPT
You are helping a local business publish an FAQ section that also targets common Google searches.

Business details:
- Name: {$name}
- Category: {$category}
- City / Service area: {$city}
- Services: {$services}

Write 10 frequently asked questions a real customer in this category would ask, each with a clear, helpful answer (2-4 sentences). Cover pricing approach, process, timelines, service area, guarantees and booking where relevant. Answers must be honest and generic enough to be true without inventing specific prices.

Return ONLY a valid JSON object (no markdown, no commentary) with EXACTLY this
key, an array of exactly 10 objects:
{
  "faqs": [ { "question": "...", "answer": "..." } ]
}
PROMPT;
}

function ai_shape_faq(array $d)
{
    $items = $d['faqs'] ?? ($d['faq'] ?? []);
    $out = [];
    if (is_array($items)) {
        foreach ($items as $item) {
            if (!is_array($item)) continue;
            $q = trim((string) ($item['question'] ?? ''));
            $a = trim((string) ($item['answer'] ?? ''));
            if ($q === '' && $a === '') continue;
            $out[] = ['question' => $q, 'answer' => $a];
        }
    }
    return ['faqs' => array_slice($out, 0, 10)];
}
