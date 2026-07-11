<?php
/**
 * TAPIFY - Get Funnel Analytics
 * Staff + Admin can query any user's funnel via ?user_id param.
 * Regular users always get their own funnel analytics.
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

    // Staff/Admin may request a specific user's funnel analytics via ?user_id
    $targetUserId = (isStaffOrAdmin() && isset($_GET['user_id'])) ? (int)$_GET['user_id'] : $userId;

    $stmt = $pdo->prepare("SELECT id FROM review_funnels WHERE user_id = ? LIMIT 1");
    $stmt->execute([$targetUserId]);
    $funnel = $stmt->fetch();

    if (!$funnel) {
        echo json_encode(['success' => true, 'data' => ['scans' => 0, 'redirects' => 0]]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT event_type, COUNT(*) as count FROM funnel_analytics WHERE funnel_id = ? GROUP BY event_type");
    $stmt->execute([$funnel['id']]);
    $results = $stmt->fetchAll();

    $analytics = ['scans' => 0, 'redirects' => 0];
    foreach ($results as $row) {
        if ($row['event_type'] === 'scan') $analytics['scans'] = (int)$row['count'];
        if ($row['event_type'] === 'redirect') $analytics['redirects'] = (int)$row['count'];
    }

    echo json_encode(['success' => true, 'data' => $analytics]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
