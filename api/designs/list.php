<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';
ini_set('display_errors', 0);
require_once __DIR__ . '/../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError('Method not allowed', 405);
}

$isSavedMode         = isset($_GET['saved']) && $_GET['saved'] == '1';
$category_id         = isset($_GET['category_id'])         ? (int) $_GET['category_id']         : 0;
$content_category_id = isset($_GET['content_category_id']) ? (int) $_GET['content_category_id'] : 0;

// Saved mode requires authentication
if ($isSavedMode) {
    requireAuth();
}

if (!$isSavedMode && !$category_id && !$content_category_id) {
    sendError('category_id, content_category_id, or saved=1 is required', 400);
}

try {
    $pdo = getDB();

    $designs = [];

    if ($isSavedMode) {
        // Return designs saved by the current user
        $user_id = getCurrentUserId();

        $stmt = $pdo->prepare(
            "SELECT d.*, dc.name AS category_name, dc.icon AS category_icon
             FROM designs d
             JOIN design_categories dc ON dc.id = d.category_id
             JOIN user_saved_designs usd ON usd.design_id = d.id
             WHERE usd.user_id = ? AND d.is_active = 1
             ORDER BY usd.saved_at DESC"
        );
        $stmt->execute([$user_id]);
        $designs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // saved_ids for saved mode is just all returned design IDs
        $saved_ids = array_map(fn($d) => (int) $d['id'], $designs);

    } elseif ($content_category_id) {
        // Return active designs linked to a specific content category
        $stmt = $pdo->prepare(
            "SELECT d.*, dc.name AS category_name, dc.icon AS category_icon
             FROM designs d
             JOIN design_categories dc ON dc.id = d.category_id
             WHERE d.is_active = 1 AND d.content_category_id = ?
             ORDER BY d.sort_order ASC, d.id DESC"
        );
        $stmt->execute([$content_category_id]);
        $designs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Return active designs for a specific design category
        $stmt = $pdo->prepare(
            "SELECT d.*, dc.name AS category_name, dc.icon AS category_icon
             FROM designs d
             JOIN design_categories dc ON dc.id = d.category_id
             WHERE d.is_active = 1 AND d.category_id = ?
             ORDER BY d.sort_order ASC, d.id DESC"
        );
        $stmt->execute([$category_id]);
        $designs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch saved_ids for the logged-in user (if authenticated) so the app
        // can mark which designs the user has already saved.
        $saved_ids = [];
        // Check if user is logged in without hard-failing if not
        $user_id = null;
        try {
            // Some implementations expose a helper like isLoggedIn(); fall back
            // to manually checking the session/token.
            if (function_exists('isLoggedIn') && isLoggedIn()) {
                $user_id = getCurrentUserId();
            } elseif (function_exists('getCurrentUserId')) {
                $user_id = getCurrentUserId();
            }
        } catch (Exception $authEx) {
            $user_id = null;
        }

        if ($user_id) {
            $savedStmt = $pdo->prepare(
                "SELECT design_id FROM user_saved_designs WHERE user_id = ?"
            );
            $savedStmt->execute([$user_id]);
            $savedRows = $savedStmt->fetchAll(PDO::FETCH_ASSOC);
            $saved_ids = array_map(fn($r) => (int) $r['design_id'], $savedRows);
        }
    }

    sendSuccess('Designs fetched', [
        'designs'   => $designs,
        'saved_ids' => $saved_ids,
    ]);

} catch (Exception $e) {
    sendError('Failed to fetch designs: ' . $e->getMessage(), 500);
}
