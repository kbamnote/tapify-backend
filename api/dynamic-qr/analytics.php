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
    
    $qr_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if (!$qr_id) {
        echo json_encode(['success' => false, 'message' => 'Missing QR ID']);
        exit;
    }

    // Verify access (admins + staff may view any QR; users only their own)
    if (isStaffOrAdmin()) {
        $stmtCheck = $pdo->prepare("SELECT id FROM dynamic_qrs WHERE id = ?");
        $stmtCheck->execute([$qr_id]);
    } else {
        $stmtCheck = $pdo->prepare("SELECT id FROM dynamic_qrs WHERE id = ? AND user_id = ?");
        $stmtCheck->execute([$qr_id, $userId]);
    }
    if (!$stmtCheck->fetch()) {
        echo json_encode(['success' => false, 'message' => 'QR not found or unauthorized']);
        exit;
    }

    // Fetch analytics
    $stats = [
        'total_scans' => 0,
        'unique_visitors' => 0,
        'devices' => [],
        'browsers' => [],
        'locations' => [],
        'recent_scans' => []
    ];

    // Totals
    $stmtTotal = $pdo->prepare("SELECT COUNT(*) as total, COUNT(DISTINCT ip_address) as unique_scans FROM dynamic_qr_scans WHERE qr_id = ?");
    $stmtTotal->execute([$qr_id]);
    $totalRow = $stmtTotal->fetch();
    $stats['total_scans'] = $totalRow['total'];
    $stats['unique_visitors'] = $totalRow['unique_scans'];

    // Devices
    $stmtDevices = $pdo->prepare("SELECT device_type, COUNT(*) as count FROM dynamic_qr_scans WHERE qr_id = ? GROUP BY device_type ORDER BY count DESC LIMIT 5");
    $stmtDevices->execute([$qr_id]);
    $stats['devices'] = $stmtDevices->fetchAll();

    // Browsers
    $stmtBrowsers = $pdo->prepare("SELECT browser, COUNT(*) as count FROM dynamic_qr_scans WHERE qr_id = ? GROUP BY browser ORDER BY count DESC LIMIT 5");
    $stmtBrowsers->execute([$qr_id]);
    $stats['browsers'] = $stmtBrowsers->fetchAll();

    // Locations
    $stmtLocations = $pdo->prepare("SELECT city, country, COUNT(*) as count FROM dynamic_qr_scans WHERE qr_id = ? AND city IS NOT NULL GROUP BY city, country ORDER BY count DESC LIMIT 5");
    $stmtLocations->execute([$qr_id]);
    $stats['locations'] = $stmtLocations->fetchAll();

    // Recent
    $stmtRecent = $pdo->prepare("SELECT device_type, browser, city, country, scanned_at FROM dynamic_qr_scans WHERE qr_id = ? ORDER BY scanned_at DESC LIMIT 10");
    $stmtRecent->execute([$qr_id]);
    $stats['recent_scans'] = $stmtRecent->fetchAll();

    echo json_encode(['success' => true, 'data' => $stats]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
