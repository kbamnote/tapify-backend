<?php
/**
 * Prompt + shaper for the Google Post writer.
 * Input : business_name, category, city, occasion, offer
 * Output: text (a single post body, ready to publish)
 *
 * Written to be posted as-is. The whole friction with Google Posts is that
 * nobody knows what to say weekly, so a draft that still needs editing has not
 * solved the problem — it has moved it.
 */

function ai_prompt_google_post(array $input)
{
    $name     = PromptBuilder::field($input, 'business_name');
    $category = PromptBuilder::field($input, 'category', 'a local business');
    $city     = PromptBuilder::field($input, 'city', 'their city');
    $occasion = PromptBuilder::field($input, 'occasion', 'a general update — something a regular customer would find worth knowing this week');
    $offer    = PromptBuilder::field($input, 'offer', 'no specific offer');

    return <<<PROMPT
You write Google Business Profile posts for Indian small businesses. These
appear on the business's Google listing when someone searches for them.

Business:
- Name: {$name}
- Category: {$category}
- City: {$city}
- What this post is about: {$occasion}
- Offer, if any: {$offer}

Write ONE post of 60-100 words.

Rules:
- Plain, warm, direct. Write the way a shop owner would speak to a regular, not
  the way an agency writes an advertisement.
- Open with the actual news or offer. Do not open with the business name, and
  never open with "Wow", "Exciting news", "We are thrilled" or "Looking for".
- Indian English. Use the rupee symbol for any price. No American idioms.
- Concrete over vague: a real reason to walk in beats "best quality service".
- At most one emoji, only if it genuinely fits. No hashtags. No links in the
  text — the post carries a separate button for that.
- End with a short, specific nudge to visit, call or order.

Return ONLY a valid JSON object (no markdown, no commentary) with EXACTLY this
key:
{
  "text": "the post"
}
PROMPT;
}

function ai_shape_google_post(array $d)
{
    $text = '';
    if (isset($d['text']) && is_string($d['text'])) {
        $text = $d['text'];
    } elseif (isset($d['post']) && is_string($d['post'])) {
        $text = $d['post'];
    }
    $text = trim($text);
    // Google caps a post at 1500 characters; trimming here means the app never
    // has to explain a rejection it could have prevented.
    if (function_exists('mb_substr') && mb_strlen($text) > 1500) {
        $text = mb_substr($text, 0, 1500);
    }
    return ['text' => $text];
}
