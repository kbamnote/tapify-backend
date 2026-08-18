<?php
/**
 * POST /api/ai/review-reply.php
 * Body: { review*, business_name, reviewer, stars, regenerate? }
 *
 * reviewer and stars are what stop every review getting the same reply — see
 * the note in includes/ai/prompts/review_reply.php. They also make the cache
 * key differ per review, so two customers who both wrote "Good service" can no
 * longer be served the identical stored sentence.
 */
require_once __DIR__ . '/_bootstrap.php';

ai_run_feature(
    'review-reply',
    ['review'],
    ['review', 'business_name', 'reviewer', 'stars'],
    ['review' => 6000, 'reviewer' => 120]   // reviews can be long
);
