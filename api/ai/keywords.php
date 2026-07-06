<?php
/**
 * POST /api/ai/keywords.php
 * Body: { business_name*, category*, city, services, regenerate? }
 */
require_once __DIR__ . '/_bootstrap.php';

ai_run_feature(
    'keywords',
    ['business_name', 'category'],
    ['business_name', 'category', 'city', 'services']
);
