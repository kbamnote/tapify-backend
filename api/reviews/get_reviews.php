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

    // First get the user's funnel
    $stmt = $pdo->prepare("SELECT id FROM review_funnels WHERE user_id = ? LIMIT 1");
    $stmt->execute([$userId]);
    $funnel = $stmt->fetch();

    if (!$funnel) {
        echo json_encode(['success' => true, 'data' => []]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM funnel_reviews WHERE funnel_id = ? ORDER BY created_at DESC");
    $stmt->execute([$funnel['id']]);
    $reviews = $stmt->fetchAll();

    echo json_encode(['success' => true, 'data' => $reviews]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
