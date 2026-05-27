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
    
    $data = json_decode(file_get_contents("php://input"), true) ?? $_POST;
    $token = $data['token'] ?? '';

    if (empty($token)) {
        echo json_encode(['success' => false, 'message' => 'Token is required']);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE users SET fcm_token = ? WHERE id = ?");
    $stmt->execute([$token, $userId]);

    echo json_encode(['success' => true, 'message' => 'Push token updated successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
