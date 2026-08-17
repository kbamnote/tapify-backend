<?php
/**
 * GET  /api/google/gbp/attributes.php  → { groups:[{group, items:[{id,label,value}]}], set, available }
 * POST /api/google/gbp/attributes.php  Body: { changes: { "attributes/xxx": true, … } }
 *                                      → the refreshed list
 *
 * Only the attributes the customer actually changed need to be sent; anything
 * left out of `changes` is untouched on Google.
 */
require_once __DIR__ . '/_bootstrap.php';

gbp_run(function ($userId, $service) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input   = getInput();
        $changes = $input['changes'] ?? null;
        if (!is_array($changes) || !$changes) {
            sendError('No changes to save.', 422);
        }
        sendSuccess('Saved to your Google listing', $service->setAttributes($userId, $changes));
    }

    sendSuccess('Attributes', $service->listAttributes($userId));
});
