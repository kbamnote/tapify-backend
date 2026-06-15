<?php
/**
 * TAPIFY - Get Funnel Reviews (Private 1-3 stars)
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    // Admins may request a specific funnel's reviews via ?funnel_id; regular
    // users always get their own funnel's reviews.
    if (isAdmin() && isset($_GET['funnel_id'])) {
        $funnelId = (int)$_GET['funnel_id'];
    } else {
        $stmt = $pdo->prepare("SELECT id FROM review_funnels WHERE user_id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $funnel = $stmt->fetch();
        if (!$funnel) {
            echo json_encode(['success' => true, 'data' => []]);
            exit;
        }
        $funnelId = $funnel['id'];
    }

    $stmt = $pdo->prepare("SELECT * FROM funnel_reviews WHERE funnel_id = ? ORDER BY created_at DESC");
    $stmt->execute([$funnelId]);
    $reviews = $stmt->fetchAll();

    echo json_encode(['success' => true, 'data' => $reviews]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
