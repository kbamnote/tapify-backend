<?php
/**
 * GET /api/google/gbp/insights.php?days=30
 *   → { days, range, prev_range, lag_days, split, cards[], series{} }
 *
 * Needs the "Business Profile Performance API" enabled on the Cloud project —
 * it is a separate API from the ones used for the listing itself.
 */
require_once __DIR__ . '/_bootstrap.php';

gbp_run(function ($userId, $service) {
    $days = isset($_GET['days']) ? (int)$_GET['days'] : 30;
    sendSuccess('Insights', $service->getInsights($userId, $days));
});
