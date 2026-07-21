<?php
/**
 * POST /api/sites/domain-setup.php   Body: { "site_id": 123 }
 *
 * Re-runs the address setup for a published site.
 *
 * Publishing never fails just because Vercel was unreachable, so a site can end
 * up published-but-not-yet-reachable. This lets the customer (or support) retry
 * without republishing, and is also how the UI answers "is my address ready?".
 *
 * Idempotent — safe to call repeatedly.
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../builder/lib/SiteRepo.php';
require_once __DIR__ . '/../../builder/lib/VercelDomains.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

requireAuth();

$input  = getInput();
$siteId = $input['site_id'] ?? null;
if (!$siteId || !is_numeric($siteId)) sendError('site_id is required');

try {
    $site = SiteRepo::findById($siteId);
    if (!$site) sendError('Site not found', 404);

    if (!SiteRepo::ownedBy($site, getCurrentUserId()) && !isStaffOrAdmin()) {
        sendError('Access denied', 403);
    }

    if (empty($site['published_version_id'])) {
        sendError('Publish the website first — there is nothing to point the address at.', 400);
    }

    // Builder sites are served from Railway via the *.tapify.co.in wildcard, so a
    // published site is already reachable — there is no per-site address to set up.
    $base = defined('PUBLIC_BASE_DOMAIN') ? PUBLIC_BASE_DOMAIN : 'tapify.co.in';
    sendSuccess('Address is set up', [
        'slug'   => $site['slug'],
        'url'    => 'https://' . $site['slug'] . '.' . $base,
        'domain' => ['configured' => true, 'ok' => true, 'host' => $site['slug'] . '.' . $base, 'message' => 'Live.'],
    ]);
} catch (Exception $e) {
    sendError('Address setup failed: ' . $e->getMessage(), 500);
}
