<?php
/**
 * TAPIFY - Get My Business Profile
 * GET /api/business/get.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

try {
    $pdo    = getDB();
    $userId = getCurrentUserId();

    $stmt = $pdo->prepare("SELECT * FROM businesses WHERE user_id = ? LIMIT 1");
    $stmt->execute([$userId]);
    $business = $stmt->fetch();

    if ($business) {
        $business['listed']     = (bool)$business['listed'];
        $business['view_count'] = (int)$business['view_count'];
        $business['logo_url']   = $business['logo'] ? imgUrl($business['logo']) : null;
    }

    sendSuccess('Business profile loaded', ['business' => $business ?: null]);

} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
