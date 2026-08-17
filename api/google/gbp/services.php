<?php
/**
 * GET  /api/google/gbp/services.php  → { services[], suggested[], category, category_id }
 * POST /api/google/gbp/services.php  Body: { services: [{name?, service_type_id?, description?, price?}] }
 *                                    → the refreshed list
 *
 * The POST replaces the WHOLE list — Google has no per-service endpoint — so
 * the app must send every service it wants to keep, not just the new one.
 */
require_once __DIR__ . '/_bootstrap.php';

gbp_run(function ($userId, $service) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = getInput();
        $list  = $input['services'] ?? null;
        if (!is_array($list)) {
            sendError('Send the full list of services to save.', 422);
        }
        sendSuccess('Saved to your Google listing', $service->setServices($userId, $list));
    }

    sendSuccess('Services', $service->listServices($userId));
});
