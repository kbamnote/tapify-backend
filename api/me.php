<?php
/**
 * TAPIFY - Get Current User
 * GET /backend/api/me.php
 * Returns currently logged in user info
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isLoggedIn()) {
    sendError('Not logged in', 401);
}

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    // Get user with subscription
    $stmt = $pdo->prepare("
        SELECT
            u.id, u.name, u.email, u.phone, u.avatar, u.role, u.status, u.created_at,
            s.plan_name, s.vcards_limit, s.stores_limit, s.expiry_date AS subscription_expires
        FROM users u
        LEFT JOIN subscriptions s ON s.user_id = u.id AND s.status = 'active'
        WHERE u.id = ?
        LIMIT 1
    ");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (!$user) {
        sendError('User not found', 404);
    }

    // Get primary vCard
    $stmt = $pdo->prepare("SELECT id, url_alias, vcard_name, view_count, status FROM vcards WHERE user_id = ? LIMIT 1");
    $stmt->execute([$userId]);
    $vcard = $stmt->fetch();

    $user['vcard'] = $vcard;

    sendSuccess('User retrieved', ['user' => $user, 'vcard' => $vcard]);

} catch (Exception $e) {
    sendError('Failed to get user: ' . $e->getMessage(), 500);
}
