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
    $sites = SiteRepo::listForUser(getCurrentUserId());

    $out = array_map(function ($s) {
        return [
            'id'           => (int)$s['id'],
            'slug'         => $s['slug'],
            'name'         => $s['name'],
            'industry'     => $s['industry'],
            'status'       => $s['status'],
            'published_at' => $s['published_at'],
            'updated_at'   => $s['updated_at'],
        ];
    }, $sites);

    sendSuccess('Sites retrieved', ['sites' => $out]);
} catch (Exception $e) {
    sendError('Failed to list sites: ' . $e->getMessage(), 500);
}
