<?php
/**
 * GET /api/google/gbp/marketing-score.php
 *   → { score, max, earned, possible, band, summary, groups[], items[] }
 *
 * The activity score, as opposed to the completeness score at score.php.
 * Several items decay, so this one never finishes — see the note at the top of
 * includes/google/MarketingScore.php for why that is deliberate.
 *
 * Gathering it touches several Google APIs. Any that fails omits its item
 * rather than scoring zero, so an outage shows as a smaller `possible` and not
 * as a penalty the customer cannot explain.
 */
require_once __DIR__ . '/_bootstrap.php';

gbp_run(function ($userId, $service) {
    sendSuccess('Marketing score', $service->getMarketingScore($userId));
});
