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

    $isAdminUser = isAdmin();
    if ($isAdminUser) {
        // Admins see every user's QR codes, with the owner's name/email.
        $stmt = $pdo->query("SELECT q.*, u.name AS owner_name, u.email AS owner_email
                             FROM dynamic_qrs q
                             LEFT JOIN users u ON u.id = q.user_id
                             ORDER BY q.created_at DESC");
        $qrs = $stmt->fetchAll();
    } else {
        // Regular users only see their own.
        $stmt = $pdo->prepare("SELECT q.*, u.name AS owner_name, u.email AS owner_email
                               FROM dynamic_qrs q
                               LEFT JOIN users u ON u.id = q.user_id
                               WHERE q.user_id = ?
                               ORDER BY q.created_at DESC");
        $stmt->execute([$userId]);
        $qrs = $stmt->fetchAll();
    }

    // Fetch scan counts for each QR
    foreach ($qrs as &$qr) {
        $stmtScans = $pdo->prepare("SELECT COUNT(*) as total_scans, COUNT(DISTINCT ip_address) as unique_scans FROM dynamic_qr_scans WHERE qr_id = ?");
        $stmtScans->execute([$qr['id']]);
        $scans = $stmtScans->fetch();
        $qr['total_scans'] = $scans['total_scans'];
        $qr['unique_scans'] = $scans['unique_scans'];
    }

    echo json_encode(['success' => true, 'data' => $qrs, 'is_admin' => $isAdminUser]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
