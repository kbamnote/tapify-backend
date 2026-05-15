<?php
/**
 * TAPIFY - Get Current User (Bulletproof Version)
 */

// Force JSON response even if there is a PHP error
header('Content-Type: application/json');
ini_set('display_errors', 0); // Hide HTML errors that break JSON

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
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
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    // Get primary vCard
    $stmt = $pdo->prepare("SELECT id, url_alias, vcard_name, view_count, status FROM vcards WHERE user_id = ? LIMIT 1");
    $stmt->execute([$userId]);
    $vcard = $stmt->fetch();

    $user['vcard'] = $vcard;

    // Clean response
    echo json_encode([
        'success' => true,
        'message' => 'User retrieved',
        'data' => [
            'user' => $user,
            'vcard' => $vcard
        ]
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
