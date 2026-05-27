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
    
    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    $notifications = $stmt->fetchAll();

    $stmtUnread = $pdo->prepare("SELECT COUNT(*) as unread_count FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmtUnread->execute([$userId]);
    $unread = $stmtUnread->fetch();

    echo json_encode([
        'success' => true, 
        'data' => $notifications, 
        'unread_count' => $unread['unread_count']
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
