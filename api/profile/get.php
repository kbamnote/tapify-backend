<?php
/**
 * TAPIFY - Get User Profile
 * GET /backend/api/profile/get.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    $stmt = $pdo->prepare("SELECT id, name, email, phone, avatar, role, email_verified, last_login, created_at FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (!$user) sendError('User not found', 404);

    $user['email_verified'] = (bool)$user['email_verified'];
    if ($user['avatar']) $user['avatar_url'] = imgUrl($user['avatar']);
    if ($user['last_login']) $user['last_login_formatted'] = date('d M Y, h:i A', strtotime($user['last_login']));
    $user['created_at_formatted'] = date('d M Y', strtotime($user['created_at']));
    $user['member_since'] = date('M Y', strtotime($user['created_at']));

    // Stats
    $stats = [];

    $stmt = $pdo->prepare("SELECT COUNT(*) as c FROM vcards WHERE user_id = ?");
    $stmt->execute([$userId]);
    $stats['total_vcards'] = (int)$stmt->fetch()['c'];

    $stats['total_stores'] = 0;
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as c FROM whatsapp_stores WHERE user_id = ?");
        $stmt->execute([$userId]);
        $stats['total_stores'] = (int)$stmt->fetch()['c'];
    } catch (Exception $e) {}

    sendSuccess('Profile loaded', [
        'user' => $user,
        'stats' => $stats
    ]);
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
