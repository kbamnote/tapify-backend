<?php
/**
 * POST /api/sites/save-draft.php
 * Body: { "site_id": 123, "rev": 7, "doc": { ...Site Document... }, "source": "web|app" }
 *
 * Autosave endpoint for both the web builder and the mobile app.
 *
 * Two guarantees:
 *  1. VALIDATION — the document is validated server-side on every save
 *     (document shape + every section's props against its manifest). Invalid
 *     documents are rejected, never stored. The client is never trusted.
 *  2. NO LOST EDITS — `rev` is an optimistic lock. If the site changed since the
 *     client loaded it (e.g. edited on the phone while open on the web), we
 *     return 409 WITH the current document so the UI can offer
 *     "keep mine / load theirs" instead of silently overwriting.
 *
 * Publishing is separate (publish.php) — saving never touches the live site.
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../builder/lib/SiteRepo.php';
require_once __DIR__ . '/../../builder/lib/SiteValidator.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

requireAuth();

$input  = getInput();
$siteId = $input['site_id'] ?? null;
$rev    = $input['rev'] ?? null;
$doc    = $input['doc'] ?? null;
$source = ($input['source'] ?? 'web') === 'app' ? 'app' : 'web';

if (!$siteId || !is_numeric($siteId)) sendError('site_id is required');
if ($rev === null || !is_numeric($rev)) sendError('rev is required (send back the rev you loaded)');
if (!is_array($doc))                    sendError('doc must be a JSON object');

try {
    $site = SiteRepo::findById($siteId);
    if (!$site) sendError('Site not found', 404);

    if (!SiteRepo::ownedBy($site, getCurrentUserId()) && !isStaffOrAdmin()) {
        sendError('Access denied', 403);
    }

    // ---- validate before touching the database ----
    // Drafts validate LENIENTLY: structure is enforced (ids, slugs, types,
    // limits) but "required content" (a headline, etc.) is not, so a
    // work-in-progress is never blocked from saving. publish.php re-validates
    // strictly, so an incomplete site still cannot go live.
    $validator = new SiteValidator();
    $errors = $validator->validate($doc, false);
    if ($errors) {
        http_response_code(422);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Document is invalid (' . count($errors) . ' problem' . (count($errors) === 1 ? '' : 's') . ')',
            'errors'  => array_slice($errors, 0, 50),
        ]);
        exit;
    }

    // ---- save (optimistic lock inside) ----
    $result = SiteRepo::saveDraft($site, $doc, (int)$rev, getCurrentUserId(), $source);

    sendSuccess('Draft saved', [
        'rev'        => $result['rev'],      // client must adopt this for the next save
        'version_id' => $result['version_id'],
        'saved_at'   => date('c'),
    ]);

} catch (SiteConflictException $e) {
    // Somebody else saved while this client was editing. Hand back the current
    // state so the UI can resolve it explicitly rather than losing work.
    $fresh = SiteRepo::findById($siteId);
    $draft = $fresh ? SiteRepo::getDraft($fresh) : null;

    http_response_code(409);
    header('Content-Type: application/json');
    echo json_encode([
        'success'  => false,
        'conflict' => true,
        'message'  => $e->getMessage(),
        'current'  => $draft ? ['rev' => $draft['rev'], 'doc' => $draft['doc'], 'updated_at' => $draft['updated_at']] : null,
    ]);
    exit;

} catch (Exception $e) {
    sendError('Failed to save draft: ' . $e->getMessage(), 500);
}
