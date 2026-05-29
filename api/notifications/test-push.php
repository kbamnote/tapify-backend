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

try {
    $pdo = getDB();

    // Allow passing the token directly via ?token= for browser testing.
    // Falls back to the logged-in user's stored token if no param given.
    $token = $_GET['token'] ?? null;

    if (empty($token)) {
        if (!isLoggedIn()) {
            echo json_encode([
                'success' => false,
                'message' => 'Not logged in. Either log in via the app session, or pass ?token=ExponentPushToken[...] in the URL.',
            ]);
            exit;
        }
        $userId = getCurrentUserId();
        $stmt = $pdo->prepare("SELECT fcm_token FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        $token = $user['fcm_token'] ?? null;
    }

    if (empty($token)) {
        echo json_encode([
            'success' => false,
            'message' => 'No push token found. Pass ?token=ExponentPushToken[...] in the URL, or run the app after login so it saves a token.',
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
