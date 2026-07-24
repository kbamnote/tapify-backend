<?php
/**
 * GET /api/sites/google-auth-start.php?site=<slug>&next=<path>   (PUBLIC)
 *
 * Kicks off "Sign in with Google" for a builder-site customer. Builds Google's
 * consent URL (with the slug + return path carried in a signed `state`) and
 * 302-redirects the browser to it. The reply comes back to the fixed callback
 * on this same origin — see google-auth-callback.php.
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../builder/lib/SiteRepo.php';
require_once __DIR__ . '/../../builder/lib/CustomerGoogleAuth.php';

$slug = strtolower(trim((string)($_GET['site'] ?? '')));
$next = (string)($_GET['next'] ?? '/account');

if (!CustomerGoogleAuth::isConfigured()) {
    // Nothing to do — bounce back to the site's login page.
    header('Location: ' . CustomerGoogleAuth::publicSiteUrl($slug ?: 'app') . '/account');
    exit;
}
if ($slug === '') { http_response_code(400); echo 'Missing site.'; exit; }

// Confirm the site exists + is published before sending anyone to Google.
$site = SiteRepo::findBySlug($slug);
if (!$site || ($site['status'] ?? '') === 'disabled' || !SiteRepo::getPublished($site)) {
    http_response_code(404); echo 'This website is not available.'; exit;
}

$params = http_build_query([
    'client_id'     => GOOGLE_LOGIN_CLIENT_ID,
    'redirect_uri'  => GOOGLE_LOGIN_REDIRECT,
    'response_type' => 'code',
    'scope'         => 'openid email profile',
    'state'         => CustomerGoogleAuth::signState($slug, $next),
    'access_type'   => 'online',
    'prompt'        => 'select_account',
]);

header('Location: https://accounts.google.com/o/oauth2/v2/auth?' . $params);
exit;
