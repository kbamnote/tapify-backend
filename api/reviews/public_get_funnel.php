<?php
/**
 * TAPIFY - Public Get Funnel (For React App)
 * Fetches funnel by slug and logs a scan.
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Allow React app to fetch
header('Access-Control-Allow-Methods: GET, OPTIONS');
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    $slug = $_GET['slug'] ?? '';
    if (empty($slug)) {
        echo json_encode(['success' => false, 'message' => 'Slug is required']);
        exit;
    }

    $pdo = getDB();

    $stmt = $pdo->prepare("
        SELECT f.id, f.google_review_url, v.vcard_name as business_name, v.profile_image 
        FROM review_funnels f
        LEFT JOIN vcards v ON v.user_id = f.user_id AND v.status = 1
        WHERE f.slug = ? 
        LIMIT 1
    ");
    $stmt->execute([$slug]);
    $funnel = $stmt->fetch();

    if (!$funnel) {
        echo json_encode(['success' => false, 'message' => 'Funnel not found']);
        exit;
    }

    // Log scan
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $stmtLog = $pdo->prepare("INSERT INTO funnel_analytics (funnel_id, event_type, ip_address, user_agent) VALUES (?, 'scan', ?, ?)");
    $stmtLog->execute([$funnel['id'], $ip, $ua]);

    echo json_encode(['success' => true, 'data' => $funnel]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
