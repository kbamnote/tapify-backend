<?php
/**
 * GET /api/ads/campaigns.php?limit=30
 * The user's boosts (local mirror), most recent first.
 */
require_once __DIR__ . '/_bootstrap.php';

ads_run(function ($userId, $ads) {
    $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 30;
    sendSuccess('Campaigns', ['campaigns' => $ads->listCampaigns($userId, $limit)]);
});
