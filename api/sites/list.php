<?php
/**
 * GET /api/sites/list.php
 *
 * The customer's sites (newest first). Powers the "My Websites" screen on both
 * the web dashboard and the mobile app.
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../builder/lib/SiteRepo.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'GET') sendError('Only GET allowed', 405);

requireAuth();

try {
    // Admins & staff manage every site (and see who owns each); a client only
    // ever sees the site assigned to them.
    $elevated = isStaffOrAdmin();
    $sites = $elevated ? SiteRepo::listAll() : SiteRepo::listForUser(getCurrentUserId());

    $out = array_map(function ($s) use ($elevated) {
        $row = [
            'id'           => (int)$s['id'],
            'slug'         => $s['slug'],
            'name'         => $s['name'],
            'industry'     => $s['industry'],
            'status'       => $s['status'],
            'published_at' => $s['published_at'],
            'updated_at'   => $s['updated_at'],
        ];
        if ($elevated) {
            $row['owner_id']    = isset($s['user_id']) ? (int)$s['user_id'] : null;
            $row['owner_name']  = $s['owner_name']  ?? null;
            $row['owner_email'] = $s['owner_email'] ?? null;
        }
        return $row;
    }, $sites);

    sendSuccess('Sites retrieved', [
        'sites'    => $out,
        'can_create' => $elevated,          // client UI hides "+ New website" when false
        'can_delete' => isAdmin(),          // only admins may delete
    ]);
} catch (Exception $e) {
    sendError('Failed to list sites: ' . $e->getMessage(), 500);
}
