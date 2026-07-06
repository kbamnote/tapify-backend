<?php
/**
 * GET /api/google/gbp/status.php
 * { configured, connected, location: { id, title } | null }
 */
require_once __DIR__ . '/_bootstrap.php';

gbp_run(function ($userId, $service) {
    sendSuccess('Status', $service->getStatus($userId));
});
