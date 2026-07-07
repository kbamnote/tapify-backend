<?php
/**
 * GET /api/social/connections.php
 * Lists the user's connected accounts (no tokens) + which platforms are
 * available to connect.
 */
require_once __DIR__ . '/_bootstrap.php';

social_run(function ($userId, $service) {
    sendSuccess('Connections', [
        'connections' => $service->listConnections($userId),
        'connectable' => SocialProviderFactory::connectable(),
    ]);
});
