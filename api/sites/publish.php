<?php
/**
 * POST /api/sites/publish.php
 * Body: { "site_id": 123, "label": "optional note" }
 *
 * Takes an immutable snapshot of the current draft and points the site at it.
 * This is the ONLY action that changes what the public sees — editing/autosave
 * never does. The draft is left intact so the customer keeps editing right after.
 *
 * Rollback = revert.php (publish an older snapshot again).
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../builder/lib/SiteRepo.php';
require_once __DIR__ . '/../../builder/lib/SiteValidator.php';
require_once __DIR__ . '/../../builder/lib/VercelDomains.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

requireAuth();

$input  = getInput();
$siteId = $input['site_id'] ?? null;
$label  = isset($input['label']) ? sanitize($input['label']) : null;
$source = ($input['source'] ?? 'web') === 'app' ? 'app' : 'web';

if (!$siteId || !is_numeric($siteId)) sendError('site_id is required');

try {
    $site = SiteRepo::findById($siteId);
    if (!$site) sendError('Site not found', 404);

    if (!SiteRepo::ownedBy($site, getCurrentUserId()) && !isStaffOrAdmin()) {
        sendError('Access denied', 403);
    }

    $draft = SiteRepo::getDraft($site);
    if (!$draft) sendError('Nothing to publish — this site has no draft yet', 400);

    // Re-validate at the gate: a draft could pre-date a schema change, and we
    // must never publish something the renderer cannot draw.
    $errors = (new SiteValidator())->validate($draft['doc']);
    if ($errors) {
        http_response_code(422);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Cannot publish — the draft is invalid. Fix these first.',
            'errors'  => array_slice($errors, 0, 50),
        ]);
        exit;
    }

    $result = SiteRepo::publish($site, getCurrentUserId(), $label, $source);
    $fresh  = SiteRepo::findById($siteId);

    // Builder sites are served from Railway through the *.tapify.co.in wildcard —
    // the same path as vCards — so a published site is instantly live with NO
    // per-site DNS or Vercel setup. Nothing to provision here.
    $domain = [
        'configured' => true,
        'ok'         => true,
        'host'       => $fresh['slug'] . '.' . (defined('PUBLIC_BASE_DOMAIN') ? PUBLIC_BASE_DOMAIN : 'tapify.co.in'),
        'message'    => 'Live.',
    ];

    sendSuccess('Site published', [
        'version_id'   => $result['version_id'],
        'rev'          => $result['rev'],
        'slug'         => $fresh['slug'],
        'status'       => $fresh['status'],
        'published_at' => $fresh['published_at'],
        'url'          => 'https://' . $fresh['slug'] . '.' . (defined('PUBLIC_BASE_DOMAIN') ? PUBLIC_BASE_DOMAIN : 'tapify.co.in'),
        'domain'       => $domain,
    ]);

} catch (SiteNotFoundException $e) {
    sendError($e->getMessage(), 404);
} catch (Exception $e) {
    sendError('Failed to publish: ' . $e->getMessage(), 500);
}
