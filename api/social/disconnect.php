<?php
/**
 * POST /api/social/disconnect.php   Body: { connection_id }
 */
require_once __DIR__ . '/_bootstrap.php';

social_run(function ($userId, $service) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendError('Method not allowed', 405);
    }
    $input = getInput();
    $id = (int) ($input['connection_id'] ?? 0);
    if ($id <= 0) {
        sendError('connection_id is required', 422);
    }
    $ok = $service->disconnect($userId, $id);
    if (!$ok) {
        sendError('Connection not found.', 404);
    }
    sendSuccess('Disconnected', ['connection_id' => $id]);
});
