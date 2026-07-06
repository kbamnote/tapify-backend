<?php
/**
 * POST /api/google/gbp/update.php
 * Body: { business_name?, description?, phone?, website? }
 * PATCHes the provided editable fields to Google, returns the refreshed fields.
 */
require_once __DIR__ . '/_bootstrap.php';

gbp_run(function ($userId, $service) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendError('Method not allowed', 405);
    }
    $input  = getInput();
    $fields = $service->updateFields($userId, $input);
    sendSuccess('Updated on Google', ['fields' => $fields, 'editable' => FieldMap::editableFields()]);
});
