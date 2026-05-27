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
    $notificationId = isset($data['id']) ? (int)$data['id'] : 0;

    if ($notificationId) {
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
        $stmt->execute([$notificationId, $userId]);
    } else {
        // Mark all as read
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
        $stmt->execute([$userId]);
    }

    echo json_encode(['success' => true, 'message' => 'Notifications marked as read']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
