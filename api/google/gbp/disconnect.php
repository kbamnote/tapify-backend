<?php
/**
 * POST /api/google/gbp/disconnect.php
 * Removes the stored Google connection for the current user.
 */
require_once __DIR__ . '/_bootstrap.php';

gbp_run(function ($userId, $service) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendError('Method not allowed', 405);
    }
    $service->disconnect($userId);
    sendSuccess('Disconnected', ['connected' => false]);
});
