<?php
/**
 * GET /api/google/gbp/reviews.php?limit=50
 *
 * { reviews[], total, average, unanswered, auto_reply:{enabled,min_stars} }
 *
 * Reviews come from the legacy v4 API, which is a SEPARATE API in Google Cloud
 * Console ("Google My Business API"). The OAuth scope already covers it, but
 * calls 404 until that API is enabled on the project.
 */
require_once __DIR__ . '/_bootstrap.php';

gbp_run(function ($userId, $service) {
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
    sendSuccess('Reviews', $service->listReviews($userId, $limit));
});
