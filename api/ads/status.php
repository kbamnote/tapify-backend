<?php
/**
 * POST /api/ads/status.php   Body: { campaign_id, active: true|false }
 * Pause / resume a campaign.
 */
require_once __DIR__ . '/_bootstrap.php';

ads_run(function ($userId, $ads) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendError('Method not allowed', 405);
    }
    $input = getInput();
    $campaignId = trim((string) ($input['campaign_id'] ?? ''));
    if ($campaignId === '') {
        sendError('campaign_id is required', 422);
    }
    $active = !empty($input['active']);
    $status = $ads->setStatus($userId, $campaignId, $active);
    sendSuccess($active ? 'Resumed' : 'Paused', ['campaign_id' => $campaignId, 'status' => $status]);
});
