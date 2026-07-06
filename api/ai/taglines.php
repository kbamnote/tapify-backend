<?php
/**
 * POST /api/ai/taglines.php
 * Body: { business_name*, category*, city, services, target_customers, regenerate? }
 */
require_once __DIR__ . '/_bootstrap.php';

ai_run_feature(
    'taglines',
    ['business_name', 'category'],
    ['business_name', 'category', 'city', 'services', 'target_customers']
);
