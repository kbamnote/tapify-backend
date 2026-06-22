<?php
header('Content-Type: application/json');
ini_set('display_errors', 0);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

// Card-editor (staff) accounts may not delete anything.
if (isStaff()) {
    echo json_encode(['success' => false, 'message' => 'Card-editor accounts are not allowed to delete. Please ask an admin.']);
    exit;
}

try {
    $pdo = getDB();
    $userId = getCurrentUserId();
    
    $data = json_decode(file_get_contents("php://input"), true) ?? $_POST;
    $id = isset($data['id']) ? (int)$data['id'] : 0;

    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Missing QR ID']);
        exit;
    }

    // Verify ownership
    $stmtCheck = $pdo->prepare("SELECT id FROM dynamic_qrs WHERE id = ? AND user_id = ?");
    $stmtCheck->execute([$id, $userId]);
    if (!$stmtCheck->fetch()) {
        echo json_encode(['success' => false, 'message' => 'QR not found or unauthorized']);
        exit;
    }

    // Delete associated scans
    $stmtScans = $pdo->prepare("DELETE FROM dynamic_qr_scans WHERE qr_id = ?");
    $stmtScans->execute([$id]);

    // Delete QR
    $stmt = $pdo->prepare("DELETE FROM dynamic_qrs WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(['success' => true, 'message' => 'QR deleted successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
