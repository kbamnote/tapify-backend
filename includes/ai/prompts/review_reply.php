<?php
/**
 * Prompt + shaper for the "AI Review Reply Generator" feature.
 * Input : review, business_name, reviewer, stars
 * Output: professional, friendly, formal, short
 *
 * WHY reviewer AND stars ARE INPUTS, not decoration:
 *
 * This prompt used to receive only the review text and the business name. Most
 * Google reviews on a local business are one to three words — "Good", "Nice
 * service", "Best salon" — so with nothing else to work from, every generation
 * converged on the same enthusiastic thank-you. Customers saw the identical
 * reply, usually opening "Wow", suggested for review after review.
 *
 * Worse, the AI cache keys on the exact input. Two customers both writing
 * "Good service" hashed identically, so the second one was served the first
 * one's stored reply byte for byte — the same sentence posted publicly under
 * two different reviews, which is precisely the pattern Google's review
 * guidelines treat as automated.
 *
 * Passing the reviewer's name and the star rating fixes both at once: the model
 * gets something real to personalise on, and the cache key differs per review
 * even when the review text is identical.
 */

function ai_prompt_review_reply(array $input)
{
    $review = PromptBuilder::field($input, 'review');
    $name   = PromptBuilder::field($input, 'business_name', 'the business');
    $who    = PromptBuilder::field($input, 'reviewer', '');
    $stars  = (int) (isset($input['stars']) ? $input['stars'] : 0);

    // First name only. "Thank you, Rahul" reads human; "Thank you, Rahul
    // Sharma" reads like a mail merge.
    $first = '';
    if ($who !== '' && stripos($who, 'a google user') === false) {
        $parts = preg_split('/\s+/', trim($who));
        $first = $parts[0] ?? '';
    }
    $greeting = $first !== ''
        ? "The reviewer's first name is {$first}. Use it naturally in at least two of the four replies."
        : "The reviewer's name is not available, so do not invent one and do not write \"Dear Customer\".";

    $rating = $stars > 0
        ? "They gave {$stars} out of 5 stars."
        : "The star rating is not available.";

    // A 5-star "Nice" and a 2-star "Nice" need opposite handling, and the model
    // cannot tell them apart from the text alone.
    $register = $stars >= 4
        ? "This is a positive review. Be warm and specific, but do not gush."
        : ($stars > 0
            ? "This is a critical review. Do not be cheerful. Acknowledge the problem plainly, take it seriously, and invite them to contact the business directly to put it right. Do not be defensive and do not argue."
            : "Match the tone of the review itself.");

    return <<<PROMPT
You are the owner of "{$name}" replying personally to a Google review.

Customer review:
'''
{$review}
'''

{$rating} {$greeting}
{$register}

Rules:
- Never open with "Wow", "Thank you so much for the wonderful review", "We are thrilled", "We are delighted" or any other stock review-reply opener. Start differently in each of the four variations.
- If the review is very short and says nothing specific, do not pretend it did. A brief, genuine thank-you is better than inventing detail the customer never mentioned.
- Sound like a person who runs the business, not a support desk.
- Do not offer refunds, discounts or compensation, and do not promise anything the business has not authorised.
- No hashtags, no emoji, no marketing slogans.
- The four variations must be genuinely different from each other in wording and opening, not the same sentence reworded.

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
