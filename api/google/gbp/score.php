<?php
/**
 * GET /api/google/gbp/score.php
 *
 * Health score for the connected listing:
 * { score, max, band, summary, previous, delta, since, items[] }
 *
 * Each item carries the AI tool key that fixes it, so the app can put a
 * "Fix this" button next to the problem rather than describing it and stopping.
 */
require_once __DIR__ . '/_bootstrap.php';

gbp_run(function ($userId, $service) {
    sendSuccess('Profile score', $service->getScore($userId));
});
