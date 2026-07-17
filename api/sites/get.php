<?php
/**
 * GET /api/sites/get.php?id=123[&kind=draft|published]
 * GET /api/sites/get.php?slug=my-site
 *
 * Returns the site row plus one document.
 *   kind=draft     (default) -> the working copy. Requires ownership.
 *   kind=published           -> the live copy. Public (this is what the
 *                               renderer calls to build the public page).
 *
 * The returned `rev` is the optimistic-lock token: send it back with
 * save-draft.php so a concurrent web/app edit can't be silently overwritten.
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../builder/lib/SiteRepo.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'GET') sendError('Only GET allowed', 405);

$kind = ($_GET['kind'] ?? 'draft') === 'published' ? 'published' : 'draft';

try {
    // --- locate the site ---
    $site = null;
    if (!empty($_GET['id'])) {
        $site = SiteRepo::findById($_GET['id']);
    } elseif (!empty($_GET['slug'])) {
        $site = SiteRepo::findBySlug(trim($_GET['slug']));
    } else {
        sendError('id or slug is required');
    }
    if (!$site) sendError('Site not found', 404);

    // --- authorise ---
    if ($kind === 'draft') {
        // The draft is private working state.
        if (!isLoggedIn()) sendError('Not logged in', 401);
        if (!SiteRepo::ownedBy($site, getCurrentUserId()) && !isStaffOrAdmin()) {
            sendError('Access denied', 403);
        }
    } else {
        // Published documents are public, but a disabled site must not render.
        if (($site['status'] ?? '') === 'disabled') sendError('Site not found', 404);
    }

    // --- load the document ---
    $payload = $kind === 'published'
        ? SiteRepo::getPublished($site)
        : SiteRepo::getDraft($site);

    if (!$payload) {
        sendError($kind === 'published'
            ? 'This site has not been published yet'
            : 'This site has no draft', 404);
    }

    sendSuccess('Site loaded', [
        'site' => [
            'id'        => (int)$site['id'],
            'slug'      => $site['slug'],
            'name'      => $site['name'],
            'industry'  => $site['industry'],
            'status'    => $site['status'],
            'published' => !empty($site['published_version_id']),
            'published_at' => $site['published_at'],
        ],
        'kind' => $kind,
        'rev'  => $payload['rev'],   // <- send this back on save
        'doc'  => $payload['doc'],
    ]);
} catch (Exception $e) {
    sendError('Failed to load site: ' . $e->getMessage(), 500);
}
