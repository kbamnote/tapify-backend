<?php
/**
 * Helper file to send Expo Push Notifications
 */

function sendExpoPushNotification($token, $title, $body, $data = []) {
    if (!$token || !is_string($token) || strpos($token, 'ExponentPushToken') === false) {
        return false;
    }

    $message = [
        'to' => $token,
        'sound' => 'default',
        'title' => $title,
        'body' => $body,
        'data' => $data,
    ];

    $ch = curl_init('https://exp.host/--/api/v2/push/send');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Accept-encoding: gzip, deflate',
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return $response;
}

/**
 * Creates a notification in the database and sends a push notification
 */
function createAndSendNotification($pdo, $userId, $title, $message, $type, $targetId = null, $redirectUrl = null, $imageUrl = null) {
    try {
        // 1. Insert into database
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message, type, target_id, redirect_url, image_url, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$userId, $title, $message, $type, $targetId, $redirectUrl, $imageUrl]);
        $notificationId = $pdo->lastInsertId();

        // 2. Fetch user's Expo Push Token
        $stmtToken = $pdo->prepare("SELECT fcm_token FROM users WHERE id = ?");
        $stmtToken->execute([$userId]);
        $user = $stmtToken->fetch();

        // 3. Send Push Notification if token exists
        if ($user && !empty($user['fcm_token'])) {
            $data = [
                'notification_id' => $notificationId,
                'type' => $type,
                'target_id' => $targetId,
                'redirect_url' => $redirectUrl
            ];
            sendExpoPushNotification($user['fcm_token'], $title, $message, $data);
        }

        return true;
    } catch (Exception $e) {
        error_log("Notification Error: " . $e->getMessage());
        return false;
    }
}
