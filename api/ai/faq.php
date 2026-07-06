<?php
/**
 * POST /api/ai/faq.php
 * Body: { business_name*, category*, city, services, regenerate? }
 */
require_once __DIR__ . '/_bootstrap.php';

ai_run_feature(
    'faq',
    ['business_name', 'category'],
    ['business_name', 'category', 'city', 'services']
);
