<?php
/**
 * GET /api/google/gbp/location.php
 * Returns the connected location's fields (editable + read-only), live from Google.
 */
require_once __DIR__ . '/_bootstrap.php';

gbp_run(function ($userId, $service) {
    $fields = $service->getFields($userId);
    sendSuccess('Location fields', ['fields' => $fields, 'editable' => FieldMap::editableFields()]);
});
