<?php
/**
 * TAPIFY - Public Track Redirect (For React App)
 * Logs a 'redirect' event when a user gives 4-5 stars.
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $funnelId = $input['funnel_id'] ?? null;

    if (!$funnelId) {
        echo json_encode(['success' => false, 'message' => 'Funnel ID is required']);
        exit;
    }

    $pdo = getDB();
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

    $stmt = $pdo->prepare("INSERT INTO funnel_analytics (funnel_id, event_type, ip_address, user_agent) VALUES (?, 'redirect', ?, ?)");
    $stmt->execute([$funnelId, $ip, $ua]);

    // A 4-5 star rating being sent on to Google is the review card working —
    // the Customer Manager's best proof it is worth using.
    require_once __DIR__ . '/../../includes/engagement/Engagement.php';
    $ownerStmt = $pdo->prepare("SELECT user_id FROM review_funnels WHERE id = ? LIMIT 1");
    $ownerStmt->execute([$funnelId]);
    Engagement::record((int)$ownerStmt->fetchColumn(), 'review_card', (int)$funnelId, 'redirect_google', ['dedupe' => false]);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
