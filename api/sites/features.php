<?php
/**
 * GET /api/sites/features.php   (auth)
 *
 * Which optional website features the current user actually uses, so the app /
 * dashboard can hide nav items that don't apply. Right now: whether any of their
 * sites has a "feedback" section (published OR draft).
 *
 *   -> { feedback: bool }
 *
 * Staff/admin always get everything (they manage all sites).
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../builder/lib/SiteRepo.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if (!isLoggedIn()) sendError('Not logged in', 401);

function docHasSection($doc, string $type): bool
{
    if (!is_array($doc)) return false;
    foreach (($doc['pages'] ?? []) as $pg) {
        foreach (($pg['sections'] ?? []) as $sec) {
            if (($sec['type'] ?? '') === $type) return true;
        }
    }
    return false;
}

try {
    if (isStaffOrAdmin()) { sendSuccess('OK', ['feedback' => true]); }

    $hasFeedback = false;
    foreach (SiteRepo::listForUser(getCurrentUserId()) as $row) {
        $site = SiteRepo::findById($row['id']);
        if (!$site) continue;
        foreach ([SiteRepo::getDraft($site), SiteRepo::getPublished($site)] as $v) {
            $doc = is_array($v) ? ($v['doc'] ?? null) : null;
            if (docHasSection($doc, 'feedback')) { $hasFeedback = true; break 2; }
        }
    }

    sendSuccess('OK', ['feedback' => $hasFeedback]);
} catch (Exception $e) {
    error_log('features: ' . $e->getMessage());
    sendSuccess('OK', ['feedback' => false]);
}
