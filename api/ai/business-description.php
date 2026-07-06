<?php
/**
 * POST /api/ai/business-description.php
 * Body: { business_name*, category*, city, services, target_customers, regenerate? }
 */
require_once __DIR__ . '/_bootstrap.php';

ai_run_feature(
    'business-description',
    ['business_name', 'category'],
    ['business_name', 'category', 'city', 'services', 'target_customers']
);
