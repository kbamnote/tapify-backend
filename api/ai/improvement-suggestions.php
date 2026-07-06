<?php
/**
 * POST /api/ai/improvement-suggestions.php
 * Body: {
 *   business_name*, category, city, services,
 *   has_description, has_logo, has_cover_image, has_contact,
 *   has_website, has_business_hours, has_keywords,   // booleans
 *   regenerate?
 * }
 */
require_once __DIR__ . '/_bootstrap.php';

ai_run_feature(
    'improvement-suggestions',
    ['business_name'],
    [
        'business_name', 'category', 'city', 'services',
        'has_description', 'has_logo', 'has_cover_image', 'has_contact',
        'has_website', 'has_business_hours', 'has_keywords',
    ]
);
