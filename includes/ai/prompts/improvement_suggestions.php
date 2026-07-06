<?php
/**
 * Prompt + shaper for the "AI Improvement Suggestions" feature.
 * Input : profile snapshot — business_name, category, city, services, plus
 *         presence flags: has_description, has_logo, has_cover_image,
 *         has_contact, has_website, has_business_hours, has_keywords.
 * Output: score, summary, checklist [{ area, status, priority, suggestion }]
 */

function ai_prompt_improvement_suggestions(array $input)
{
    $name     = PromptBuilder::field($input, 'business_name', 'the business');
    $category = PromptBuilder::field($input, 'category', 'unknown');
    $city     = PromptBuilder::field($input, 'city', 'unknown');
    $services = PromptBuilder::field($input, 'services', 'not provided');

    $flag = function ($k) use ($input) {
        return !empty($input[$k]) ? 'yes' : 'no';
    };

    $present = "- Has business description: " . $flag('has_description') . "\n"
             . "- Has logo: " . $flag('has_logo') . "\n"
             . "- Has cover image: " . $flag('has_cover_image') . "\n"
             . "- Has contact details: " . $flag('has_contact') . "\n"
             . "- Has website: " . $flag('has_website') . "\n"
             . "- Has business hours: " . $flag('has_business_hours') . "\n"
             . "- Has SEO keywords: " . $flag('has_keywords');

    return <<<PROMPT
You are a Google Business Profile & online-presence auditor. Analyse the profile below and produce an actionable improvement checklist.

Business:
- Name: {$name}
- Category: {$category}
- City / Service area: {$city}
- Services: {$services}

Profile completeness:
{$present}

Rules:
- For every item marked "no", add a checklist entry with status "missing".
- Also evaluate name formatting, service clarity and keyword coverage; use status "needs_improvement" where it could be better, or "good" where it is fine.
- Give one concrete, specific suggestion per item (not generic advice).
- Set priority to "high", "medium" or "low" by impact on discoverability.
- score = overall profile completeness/quality out of 100.

Return ONLY a valid JSON object (no markdown, no commentary) with EXACTLY these keys:
{
  "score": 0,
  "summary": "one-sentence overall assessment",
  "checklist": [
    { "area": "Business Description", "status": "missing|needs_improvement|good", "priority": "high|medium|low", "suggestion": "..." }
  ]
}
PROMPT;
}

function ai_shape_improvement_suggestions(array $d)
{
    $score = isset($d['score']) ? (int) $d['score'] : 0;
    $score = max(0, min(100, $score));

    $allowedStatus   = ['missing', 'needs_improvement', 'good'];
    $allowedPriority = ['high', 'medium', 'low'];

    $checklist = [];
    if (!empty($d['checklist']) && is_array($d['checklist'])) {
        foreach ($d['checklist'] as $item) {
            if (!is_array($item)) continue;
            $status   = strtolower(trim((string) ($item['status'] ?? 'needs_improvement')));
            $priority = strtolower(trim((string) ($item['priority'] ?? 'medium')));
            $checklist[] = [
                'area'       => trim((string) ($item['area'] ?? 'General')),
                'status'     => in_array($status, $allowedStatus, true) ? $status : 'needs_improvement',
                'priority'   => in_array($priority, $allowedPriority, true) ? $priority : 'medium',
                'suggestion' => trim((string) ($item['suggestion'] ?? '')),
            ];
        }
    }

    return [
        'score'     => $score,
        'summary'   => trim((string) ($d['summary'] ?? '')),
        'checklist' => $checklist,
    ];
}
