<?php
/**
 * POST /api/ai/review-reply.php
 * Body: { review*, business_name, regenerate? }
 */
require_once __DIR__ . '/_bootstrap.php';

ai_run_feature(
    'review-reply',
    ['review'],
    ['review', 'business_name'],
    ['review' => 6000]   // reviews can be long
);
