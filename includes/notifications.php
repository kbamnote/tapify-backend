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
        'sound'     => 'notification_sound.mpeg',
        'title'     => $title,
        'body'      => $body,
        'data'      => $data,
        'channelId' => 'tapify_alerts',
        'priority'  => 'high',          // triggers heads-up popup on Android
        'ttl'       => 86400,           // 24 hours — deliver even if device is offline
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
    $notificationId = null;

    // ── Step 1: Ensure tables / columns exist ──────────────────────────────
    try { $pdo->exec("ALTER TABLE users ADD COLUMN fcm_token VARCHAR(255) DEFAULT NULL"); } catch (Exception $e) {}

    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS notifications (
                id           INT AUTO_INCREMENT PRIMARY KEY,
                user_id      INT          NOT NULL,
                title        VARCHAR(255) NOT NULL,
                message      TEXT         NOT NULL,
                type         VARCHAR(50)  NOT NULL DEFAULT 'system',
                target_id    INT          NULL,
                redirect_url VARCHAR(500) NULL,
                image_url    VARCHAR(500) NULL,
                is_read      TINYINT(1)   NOT NULL DEFAULT 0,
                created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_user_id (user_id),
                INDEX idx_read    (user_id, is_read)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    } catch (Exception $e) {
        error_log("[TapifyPush] Table create failed: " . $e->getMessage());
    }

    // ── Step 2: Persist in-app notification (non-blocking) ─────────────────
    try {
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message, type, target_id, redirect_url, image_url, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$userId, $title, $message, $type, $targetId, $redirectUrl, $imageUrl]);
        $notificationId = $pdo->lastInsertId();
    } catch (Exception $e) {
        error_log("[TapifyPush] DB insert failed: " . $e->getMessage());
        // Continue — push can still be sent even if DB write fails
    }

    // ── Step 3: Send Expo push notification ────────────────────────────────
    $pushResult = null;
    try {
        $stmtToken = $pdo->prepare("SELECT fcm_token FROM users WHERE id = ? LIMIT 1");
        $stmtToken->execute([$userId]);
        $user = $stmtToken->fetch();

        if ($user && !empty($user['fcm_token'])) {
            $pushData = [
                'notification_id' => $notificationId,
                'type'            => $type,
                'target_id'       => $targetId,
                'redirect_url'    => $redirectUrl,
            ];
            $pushResult = sendExpoPushNotification($user['fcm_token'], $title, $message, $pushData);
        }
    } catch (Exception $e) {
        error_log("[TapifyPush] Push send failed: " . $e->getMessage());
    }

    return ['db' => ($notificationId !== null), 'push' => $pushResult];
}
