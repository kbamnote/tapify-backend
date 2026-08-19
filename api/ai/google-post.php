<?php
/**
 * POST /api/ai/google-post.php
 * Body: { business_name*, category, city, occasion, offer, regenerate? }
 *   → { text }
 *
 * Writes a ready-to-publish Google Post. Called from the Posts screen, which
 * publishes the result through /api/google/gbp/posts.php.
 */
require_once __DIR__ . '/_bootstrap.php';

ai_run_feature(
    'google-post',
    ['business_name'],
    ['business_name', 'category', 'city', 'occasion', 'offer']
);
