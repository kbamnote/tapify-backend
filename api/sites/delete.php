<?php
/**
 * POST /api/sites/delete.php   { "site_id": 123 }
 *
 * Permanently deletes a builder site and all its versions.
 *
 * ADMIN ONLY. Staff may create and edit sites but never delete them; clients
 * can only edit/view the site assigned to them. This is enforced server-side so
 * hiding the button in the UI is a convenience, not the security boundary.
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../builder/lib/SiteRepo.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

requireAdmin();   // admins only — blocks staff and clients

$input  = getInput();
$siteId = (int)($input['site_id'] ?? 0);
if ($siteId <= 0) sendError('site_id is required');

try {
    $site = SiteRepo::findById($siteId);
    if (!$site) sendError('Site not found', 404);

    SiteRepo::deleteSite($siteId);

    sendSuccess('Website deleted', ['id' => $siteId]);
} catch (Exception $e) {
    error_log('sites/delete: ' . $e->getMessage());
    sendError('Failed to delete the website.', 500);
}
