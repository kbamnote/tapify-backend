<?php
/**
 * Helper file to send Expo Push Notifications
 */

/**
 * Sends a single push notification via Expo Push API.
 * Returns ['ok' => true] on success or ['ok' => false, 'error' => string] on failure.
 */
function sendExpoPushNotification($token, $title, $body, $data = []) {
    if (!$token || !is_string($token) || strpos($token, 'ExponentPushToken') === false) {
        error_log("[Push] Skipped — invalid or missing token: " . var_export($token, true));
        return ['ok' => false, 'error' => 'invalid_token'];
    }

    $message = [
        'to'        => $token,
        'sound'     => 'default',
        'title'     => $title,
        'body'      => $body,
        'data'      => $data,
        'channelId' => 'default', // Required for Android 8+
    ];

    $ch = curl_init('https://exp.host/--/api/v2/push/send');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING, '');          // Let cURL handle decompression automatically
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response  = curl_exec($ch);
    $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    // cURL-level failure (network, SSL, timeout, etc.)
    if ($response === false) {
        error_log("[Push] cURL error sending to $token: $curlError");
        return ['ok' => false, 'error' => "curl_error: $curlError"];
    }

    // HTTP-level failure
    if ($httpCode !== 200) {
        error_log("[Push] HTTP $httpCode from Expo for token $token. Body: $response");
        return ['ok' => false, 'error' => "http_$httpCode"];
    }

    // Parse Expo's JSON response and check for push-level errors
    $decoded = json_decode($response, true);
    if (isset($decoded['data'][0]['status']) && $decoded['data'][0]['status'] === 'error') {
        $expoError = $decoded['data'][0]['message'] ?? 'unknown';
        error_log("[Push] Expo error for token $token: $expoError");
        return ['ok' => false, 'error' => $expoError];
    }

    return ['ok' => true];
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
        $pushResult = null;
        if ($user && !empty($user['fcm_token'])) {
            $pushData = [
                'notification_id' => $notificationId,
                'type'            => $type,
                'target_id'       => $targetId,
                'redirect_url'    => $redirectUrl,
            ];
            $pushResult = sendExpoPushNotification($user['fcm_token'], $title, $message, $pushData);
        }

        return ['db' => true, 'push' => $pushResult];
    } catch (Exception $e) {
        error_log("Notification Error: " . $e->getMessage());
        return false;
    }
}
