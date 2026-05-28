<?php
/**
 * TAPIFY - Admin Broadcast Notifications API
 */
header('Content-Type: application/json');
ini_set('display_errors', 0);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

// Verify Admin - Assuming there's a way to check if user is admin.
// If the app doesn't have an explicit isAdmin() function, we can check role.
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

try {
    $pdo = getDB();
    $userId = getCurrentUserId();
    
    // Check if the current user is an admin (assuming role = 'admin' or id = 1 for now)
    $stmtAdmin = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $stmtAdmin->execute([$userId]);
    $user = $stmtAdmin->fetch();
    
    if (!$user || $user['role'] !== 'admin') {
        // Fallback for demo if no role column: allow user id 1
        if ($userId != 1) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized. Admin access required.']);
            exit;
        }
    }

    $data = json_decode(file_get_contents("php://input"), true) ?? $_POST;
    
    $title = $data['title'] ?? '';
    $message = $data['message'] ?? '';
    $targetUser = $data['target_user'] ?? 'all'; // 'all' or user_id
    $redirectUrl = $data['redirect_url'] ?? null;
    $imageUrl = $data['image_url'] ?? null;

    if (empty($title) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Title and message are required.']);
        exit;
    }

    require_once __DIR__ . '/../../includes/notifications.php';

    $totalCount   = 0;
    $pushedCount  = 0;
    $pushErrors   = [];

    if ($targetUser === 'all') {
        // Broadcast to all users with tokens
        $stmtUsers = $pdo->query("SELECT id, fcm_token FROM users WHERE fcm_token IS NOT NULL AND fcm_token != ''");
        while ($u = $stmtUsers->fetch()) {
            $result = createAndSendNotification($pdo, $u['id'], $title, $message, 'broadcast', null, $redirectUrl, $imageUrl);
            $totalCount++;
            if (isset($result['push']['ok']) && $result['push']['ok']) {
                $pushedCount++;
            } elseif (isset($result['push']['error'])) {
                $pushErrors[] = "user {$u['id']}: {$result['push']['error']}";
            }
        }
    } else {
        $result = createAndSendNotification($pdo, (int)$targetUser, $title, $message, 'admin_message', null, $redirectUrl, $imageUrl);
        $totalCount = 1;
        if (isset($result['push']['ok']) && $result['push']['ok']) {
            $pushedCount = 1;
        } elseif (isset($result['push']['error'])) {
            $pushErrors[] = $result['push']['error'];
        }
    }

    $msg = "Saved to DB: $totalCount user(s). Push delivered: $pushedCount.";
    if (!empty($pushErrors)) {
        $msg .= " Errors: " . implode('; ', $pushErrors);
    }

    echo json_encode(['success' => true, 'message' => $msg, 'push_errors' => $pushErrors]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
