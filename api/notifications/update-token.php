<?php
header('Content-Type: application/json');
ini_set('display_errors', 0);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    // ── Ensure fcm_token column exists (was missing from original schema) ──
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN fcm_token VARCHAR(255) DEFAULT NULL");
    } catch (Exception $e) {
        // Column already exists — ignore
    }

    $data  = json_decode(file_get_contents("php://input"), true) ?? $_POST;
    $token = trim($data['token'] ?? '');

    if (empty($token)) {
        echo json_encode(['success' => false, 'message' => 'Token is required']);
        exit;
    }

    // Validate it looks like an Expo push token
    if (strpos($token, 'ExponentPushToken') === false) {
        echo json_encode(['success' => false, 'message' => 'Invalid push token format']);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE users SET fcm_token = ? WHERE id = ?");
    $stmt->execute([$token, $userId]);

    echo json_encode(['success' => true, 'message' => 'Push token saved successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
