<?php
/**
 * POST /api/google/gbp/select-location.php
 * Body: { location_id*, title? }
 * Choose which GBP location to manage when the account has several.
 */
require_once __DIR__ . '/_bootstrap.php';

gbp_run(function ($userId, $service) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendError('Method not allowed', 405);
    }
    $input      = getInput();
    $locationId = trim((string) ($input['location_id'] ?? ''));
    if ($locationId === '') {
        sendError('location_id is required', 422);
    }
    $service->selectLocation($userId, $locationId, $input['title'] ?? null);
    sendSuccess('Location selected', ['location' => ['id' => $locationId, 'title' => $input['title'] ?? null]]);
});
