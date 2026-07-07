<?php
/**
 * GET /api/social/callback.php?code=...&state=...
 * Shared OAuth callback for all platforms (the state row carries the platform).
 * Validates state, exchanges the code, stores the connection(s), then hands
 * back to the app via its deep link. Not JSON, not session-authed.
 */
require_once __DIR__ . '/../../config/database.php';
ini_set('display_errors', '0');
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/social/autoload.php';

$deepLink = defined('SOCIAL_DEEP_LINK') ? SOCIAL_DEEP_LINK : 'tapifapp://social-connected';

function social_handoff($deepLink, array $params)
{
    $sep = strpos($deepLink, '?') === false ? '?' : '&';
    $target = $deepLink . $sep . http_build_query($params);
    $safe = htmlspecialchars($target, ENT_QUOTES, 'UTF-8');
    $ok = ($params['status'] ?? '') === 'success';
    $msg = $ok ? 'Account connected ✅' : 'Could not connect. Please try again.';
    header('Content-Type: text/html; charset=utf-8');
    echo "<!doctype html><html><head><meta name='viewport' content='width=device-width, initial-scale=1'>"
       . "<title>Tapify</title><script>window.location.replace(" . json_encode($target) . ");</script>"
       . "<style>body{font-family:-apple-system,Segoe UI,Roboto,sans-serif;background:#f6eee6;color:#153e3f;"
       . "display:flex;min-height:100vh;align-items:center;justify-content:center;margin:0;text-align:center;padding:24px}"
       . "a{display:inline-block;margin-top:18px;background:#153e3f;color:#fff;padding:12px 22px;border-radius:10px;"
       . "text-decoration:none;font-weight:700}</style></head><body><div><h2>{$msg}</h2>"
       . "<p>Returning you to the Tapify app…</p><a href='{$safe}'>Open Tapify</a></div></body></html>";
    exit;
}

// Facebook returns error/error_reason on denial.
if (isset($_GET['error'])) {
    social_handoff($deepLink, ['status' => 'error', 'reason' => 'access_denied']);
}

$code  = $_GET['code']  ?? '';
$state = $_GET['state'] ?? '';
if ($code === '' || $state === '') {
    social_handoff($deepLink, ['status' => 'error', 'reason' => 'missing_params']);
}

try {
    $service = new SocialService(getDB());
    $service->completeOAuth($code, $state);
    social_handoff($deepLink, ['status' => 'success']);
} catch (SocialException $e) {
    SocialLogger::warn('callback.failed', ['status' => $e->getHttpStatus()]);
    social_handoff($deepLink, ['status' => 'error', 'reason' => 'oauth_failed']);
} catch (Exception $e) {
    SocialLogger::error('callback.error', ['error' => $e->getMessage()]);
    social_handoff($deepLink, ['status' => 'error', 'reason' => 'server_error']);
}
