<?php
/**
 * POST /api/ai/service-description.php
 * Body: { service_name*, business_name, category, city, regenerate? }
 */
require_once __DIR__ . '/_bootstrap.php';

ai_run_feature(
    'service-description',
    ['service_name'],
    ['service_name', 'business_name', 'category', 'city']
);
