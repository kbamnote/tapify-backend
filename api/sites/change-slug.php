<?php
/**
 * POST /api/sites/change-slug.php   Body: { "site_id": 123, "slug": "new-address" }
 *
 * Changes a website's public address (<slug>.tapify.co.in).
 *
 * The owner may do this themselves. The change is live immediately — sites are
 * served from the *.tapify.co.in wildcard, so there is no DNS record to create
 * and nothing to wait for.
 *
 * The OLD address keeps working: it is recorded in site_slug_history and
 * index.php 301s it to the current one, so QR codes and business cards already
 * printed do not break.
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../builder/lib/SiteRepo.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

requireAuth();

$input  = getInput();
$siteId = $input['site_id'] ?? null;
$raw    = (string)($input['slug'] ?? '');

if (!$siteId || !is_numeric($siteId)) sendError('site_id is required');

// No name to fall back on here — a rename must be explicit, never guessed.
$slug = SiteRepo::normaliseSlug($raw);
if ($slug === '') sendError(SiteRepo::SLUG_RULE);

try {
    $site = SiteRepo::findById($siteId);
    if (!$site) sendError('Site not found', 404);

    if (!SiteRepo::ownedBy($site, getCurrentUserId()) && !isStaffOrAdmin()) {
        sendError('Access denied', 403);
    }

    $res = SiteRepo::changeSlug($site, $slug, getCurrentUserId());

    $base = defined('PUBLIC_BASE_DOMAIN') ? PUBLIC_BASE_DOMAIN : 'tapify.co.in';
    sendSuccess('Address updated', [
        'slug'          => $res['slug'],
        'previous_slug' => $res['previous_slug'],
        'url'           => 'https://' . $res['slug'] . '.' . $base,
        'previous_url'  => 'https://' . $res['previous_slug'] . '.' . $base,
        'note'          => 'The old address now redirects here, so existing links and QR codes keep working.',
    ]);

} catch (Exception $e) {
    // changeSlug throws customer-safe messages (taken / unchanged / raced).
    sendError($e->getMessage(), 409);
}
