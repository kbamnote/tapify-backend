<?php
/**
 * POST /api/sites/revert.php
 * Body: { "site_id": 123, "version_id": 45, "publish": false }
 *
 * Restores an old published snapshot INTO THE DRAFT. Non-destructive by design:
 * the live site does not change unless the caller also asks to publish, so a
 * customer can inspect an old version safely before making it live again.
 *
 * Nothing is ever deleted — reverting just copies an old doc forward.
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../builder/lib/SiteRepo.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

requireAuth();

$input     = getInput();
$siteId    = $input['site_id'] ?? null;
$versionId = $input['version_id'] ?? null;
$alsoPublish = !empty($input['publish']);
$source    = ($input['source'] ?? 'web') === 'app' ? 'app' : 'web';

if (!$siteId || !is_numeric($siteId))       sendError('site_id is required');
if (!$versionId || !is_numeric($versionId)) sendError('version_id is required');

try {
    $site = SiteRepo::findById($siteId);
    if (!$site) sendError('Site not found', 404);

    if (!SiteRepo::ownedBy($site, getCurrentUserId()) && !isStaffOrAdmin()) {
        sendError('Access denied', 403);
    }

    $result = SiteRepo::revertDraftTo($site, $versionId, getCurrentUserId(), $source);

    $published = null;
    if ($alsoPublish) {
        $fresh = SiteRepo::findById($siteId);
        $published = SiteRepo::publish($fresh, getCurrentUserId(), 'Reverted to version #' . (int)$versionId, $source);
    }

    $fresh = SiteRepo::findById($siteId);
    $draft = SiteRepo::getDraft($fresh);

    sendSuccess($alsoPublish ? 'Reverted and published' : 'Reverted into your draft', [
        'rev' => $result['rev'],
        'doc' => $draft['doc'] ?? null,
        'published_version_id' => $published['version_id'] ?? null,
    ]);

} catch (SiteConflictException $e) {
    sendError('Someone else is editing this site right now. Reload and try again.', 409);
} catch (SiteNotFoundException $e) {
    sendError($e->getMessage(), 404);
} catch (Exception $e) {
    sendError('Failed to revert: ' . $e->getMessage(), 500);
}
