<?php
/**
 * GET /api/ads/insights.php?campaign_id=XX
 * Live spend/reach/impressions/clicks for one of the user's campaigns.
 */
require_once __DIR__ . '/_bootstrap.php';

ads_run(function ($userId, $ads) {
    $campaignId = trim((string) ($_GET['campaign_id'] ?? ''));
    if ($campaignId === '') {
        sendError('campaign_id is required', 422);
    }
    sendSuccess('Insights', ['insights' => $ads->insights($userId, $campaignId)]);
});
