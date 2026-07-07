<?php
/**
 * POST /api/social/connect-url.php   Body: { platform }
 * Returns the OAuth consent URL for the app to open in a browser.
 */
require_once __DIR__ . '/_bootstrap.php';

social_run(function ($userId, $service) {
    $input = getInput();
    $platform = strtolower(trim($input['platform'] ?? ($_GET['platform'] ?? '')));
    if ($platform === '') {
        sendError('platform is required', 422);
    }
    $url = $service->buildConnectUrl($userId, $platform);
    sendSuccess('Connect URL', ['auth_url' => $url]);
});
