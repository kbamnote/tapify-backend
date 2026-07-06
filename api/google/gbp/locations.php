<?php
/**
 * GET /api/google/gbp/locations.php
 * Lists all locations under the connected account (for the location picker).
 */
require_once __DIR__ . '/_bootstrap.php';

gbp_run(function ($userId, $service) {
    sendSuccess('Locations', ['locations' => $service->listLocations($userId)]);
});
