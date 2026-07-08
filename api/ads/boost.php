<?php
/**
 * POST /api/ads/boost.php
 * Body: {
 *   connection_id,           // the connected Facebook Page
 *   post_id,                 // page post id (or full pageid_postid)
 *   budget_inr,              // total ad budget in ₹ (>= ADS_MIN_BUDGET_INR)
 *   duration_days,           // 1..30
 *   targeting: { country_codes:['IN'], age_min, age_max, genders:[1,2] }
 * }
 * Charges the wallet (budget + commission) and launches the boost; refunds on failure.
 */
require_once __DIR__ . '/_bootstrap.php';

ads_run(function ($userId, $ads) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendError('Method not allowed', 405);
    }
    $result = $ads->boostPost($userId, getInput());
    sendSuccess('Boost launched', $result);
});
