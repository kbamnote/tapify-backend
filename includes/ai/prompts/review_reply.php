<?php
/**
 * Prompt + shaper for the "AI Review Reply Generator" feature.
 * Input : review (the customer review text), business_name
 * Output: professional, friendly, formal, short
 */

function ai_prompt_review_reply(array $input)
{
    $review = PromptBuilder::field($input, 'review');
    $name   = PromptBuilder::field($input, 'business_name', 'the business');

    return <<<PROMPT
You are a customer-experience manager replying to a Google review for "{$name}".

Customer review:
'''
{$review}
'''

Write four reply variations to the SAME review. Thank the customer, sound authentic and human, address specifics from the review, and keep it appropriate whether the review is positive or negative. Do not offer refunds or make promises the business hasn't authorised. No hashtags.

Return ONLY a valid JSON object (no markdown, no commentary) with EXACTLY these keys:
{
  "professional": "a polished, professional reply",
  "friendly": "a warm, casual, friendly reply",
  "formal": "a formal, corporate-tone reply",
  "short": "a brief 1-2 sentence reply"
}
PROMPT;
}

function ai_shape_review_reply(array $d)
{
    $get = function ($k) use ($d) { return isset($d[$k]) ? trim((string) $d[$k]) : ''; };
    return [
        'professional' => $get('professional'),
        'friendly'     => $get('friendly'),
        'formal'       => $get('formal'),
        'short'        => $get('short'),
    ];
}
