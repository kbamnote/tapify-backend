<?php
/**
 * GET /api/sites/versions.php?site_id=123
 *
 * Publish history for a site (newest first). Each row is an immutable snapshot
 * that revert.php can restore. Draft autosaves are NOT listed — they update the
 * draft in place, so history stays meaningful instead of one row per keystroke.
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../builder/lib/SiteRepo.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'GET') sendError('Only GET allowed', 405);

requireAuth();

$siteId = $_GET['site_id'] ?? null;
if (!$siteId || !is_numeric($siteId)) sendError('site_id is required');

try {
    $site = SiteRepo::findById($siteId);
    if (!$site) sendError('Site not found', 404);

    if (!SiteRepo::ownedBy($site, getCurrentUserId()) && !isStaffOrAdmin()) {
        sendError('Access denied', 403);
    }

    $rows = SiteRepo::listVersions($siteId);
    $liveId = (int)($site['published_version_id'] ?? 0);

    $out = array_map(function ($v) use ($liveId) {
        return [
            'id'         => (int)$v['id'],
            'rev'        => (int)$v['rev'],
            'label'      => $v['label'],
            'source'     => $v['source'],
            'size_kb'    => isset($v['doc_bytes']) ? round(((int)$v['doc_bytes']) / 1024, 1) : null,
            'created_at' => $v['created_at'],
            'is_live'    => (int)$v['id'] === $liveId,
        ];
    }, $rows);

    sendSuccess('Versions retrieved', [
        'site_id'  => (int)$site['id'],
        'live_version_id' => $liveId ?: null,
        'versions' => $out,
    ]);
} catch (Exception $e) {
    sendError('Failed to list versions: ' . $e->getMessage(), 500);
}
