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

    // Ensure table exists (first-boot migration guard)
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
                INDEX idx_user_id (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    } catch (Exception $e) {}

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
