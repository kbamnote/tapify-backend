<?php
/**
 * TAPIFY - Test Push Notification for current logged-in user
 * GET /api/notifications/test-push.php
 * Use this to verify the full push pipeline. Remove after fixing.
 */
header('Content-Type: application/json');
ini_set('display_errors', 0);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/notifications.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

try {
    $pdo    = getDB();
    $userId = getCurrentUserId();

    // Fetch stored token
    $stmt = $pdo->prepare("SELECT fcm_token FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    $token = $user['fcm_token'] ?? null;

    if (empty($token)) {
        echo json_encode([
            'success' => false,
            'message' => 'No push token found for your account. Make sure the app ran at least once after login.',
        ]);
        exit;
    }

    // Send test push
    $result = sendExpoPushNotification(
        $token,
        'Test Push Notification',
        'If you see this popup, the full push pipeline is working!',
        ['test' => true, 'redirect_url' => '/dashboard']
    );

    echo json_encode([
        'success'     => $result['ok'],
        'token'       => $token,
        'push_result' => $result,
        'message'     => $result['ok']
            ? 'Push sent successfully! You should see a notification popup on your device.'
            : 'Push failed: ' . ($result['error'] ?? 'unknown error'),
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
