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
    
    // Process input
    $data = json_decode(file_get_contents("php://input"), true) ?? $_POST;
    
    $id = isset($data['id']) && $data['id'] ? (int)$data['id'] : null;
    $name = $data['name'] ?? 'My QR';
    $destination_url = $data['destination_url'] ?? '';
    $qr_type = $data['qr_type'] ?? 'website';
    $status = isset($data['status']) ? (int)$data['status'] : 1;
    $password = !empty($data['password']) ? $data['password'] : null;
    $expiry_date = !empty($data['expiry_date']) ? $data['expiry_date'] : null;
    $custom_color = $data['custom_color'] ?? '#000000';
    $logo = $data['logo'] ?? null;
    
    // Generate unique short slug if not exists
    $short_url = $data['short_url'] ?? '';
    if (empty($short_url)) {
        $short_url = substr(md5(uniqid(rand(), true)), 0, 8);
    }
    
    // Ensure slug is unique
    $stmtCheck = $pdo->prepare("SELECT id FROM dynamic_qrs WHERE short_url = ? AND id != ?");
    $stmtCheck->execute([$short_url, $id ?? 0]);
    if ($stmtCheck->fetch()) {
        echo json_encode(['success' => false, 'message' => 'This short URL is already taken.']);
        exit;
    }

    if ($id) {
        // Update
        $stmt = $pdo->prepare("
            UPDATE dynamic_qrs 
            SET name = ?, short_url = ?, destination_url = ?, qr_type = ?, status = ?, 
                password = ?, expiry_date = ?, custom_color = ?, logo = ?, updated_at = NOW()
            WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([$name, $short_url, $destination_url, $qr_type, $status, $password, $expiry_date, $custom_color, $logo, $id, $userId]);
        $msg = 'QR updated successfully';
    } else {
        // Insert
        $stmt = $pdo->prepare("
            INSERT INTO dynamic_qrs 
            (user_id, name, short_url, destination_url, qr_type, status, password, expiry_date, custom_color, logo, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$userId, $name, $short_url, $destination_url, $qr_type, $status, $password, $expiry_date, $custom_color, $logo]);
        $id = $pdo->lastInsertId();
        $msg = 'QR created successfully';
    }

    // Return the saved record
    $stmt = $pdo->prepare("SELECT * FROM dynamic_qrs WHERE id = ?");
    $stmt->execute([$id]);
    $qr = $stmt->fetch();

    echo json_encode(['success' => true, 'message' => $msg, 'data' => $qr]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
