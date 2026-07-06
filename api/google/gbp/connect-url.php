<?php
/**
 * GET /api/google/gbp/connect-url.php
 * Returns the Google consent URL for the app to open in a browser. The URL's
 * `state` is bound to this authenticated user, so the (cookie-less) browser
 * callback can still be attributed back to them.
 */
require_once __DIR__ . '/_bootstrap.php';

gbp_run(function ($userId, $service) {
    $url = $service->buildConnectUrl($userId);
    sendSuccess('Connect URL', ['auth_url' => $url]);
});
