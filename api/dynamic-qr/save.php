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
    
    $ownerId = (isStaffOrAdmin() && !empty($data['user_id'])) ? (int)$data['user_id'] : $userId;

    $id = isset($data['id']) && $data['id'] ? (int)$data['id'] : null;
    $name = $data['name'] ?? 'My QR';
    $destination_url = $data['destination_url'] ?? '';
    $qr_type = $data['qr_type'] ?? 'website';
    $status = isset($data['status']) ? (int)$data['status'] : 1;
    $password = !empty($data['password']) ? $data['password'] : null;
    $expiry_date = !empty($data['expiry_date']) ? $data['expiry_date'] : null;
    $custom_color = $data['custom_color'] ?? '#000000';
    $logo = $data['logo'] ?? null;
    // Visual style (dot type, corner styles, corner color, etc.) stored as JSON.
    $style_json = null;
    if (isset($data['style_json'])) {
        $style_json = is_string($data['style_json']) ? $data['style_json'] : json_encode($data['style_json']);
    }

    // The style_json column is added by migration_qr_style.sql. Detect it so
    // saving keeps working even if the migration hasn't been run yet.
    $hasStyleCol = false;
    try {
        $colCheck = $pdo->query("SHOW COLUMNS FROM dynamic_qrs LIKE 'style_json'");
        $hasStyleCol = $colCheck && $colCheck->fetch();
    } catch (Exception $e) {
        $hasStyleCol = false;
    }
    
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
        // Update — admins + staff editors may update ANY QR; users only their own.
        $editAny = isStaffOrAdmin();
        $ownSql  = $editAny ? "WHERE id = ?" : "WHERE id = ? AND user_id = ?";
        if ($hasStyleCol) {
            $stmt = $pdo->prepare("
                UPDATE dynamic_qrs
                SET user_id = ?, name = ?, short_url = ?, destination_url = ?, qr_type = ?, status = ?,
                    password = ?, expiry_date = ?, custom_color = ?, logo = ?, style_json = ?, updated_at = NOW()
                $ownSql
            ");
            $params = [$ownerId, $name, $short_url, $destination_url, $qr_type, $status, $password, $expiry_date, $custom_color, $logo, $style_json, $id];
            if (!$editAny) { $params[] = $userId; }
            $stmt->execute($params);
        } else {
            $stmt = $pdo->prepare("
                UPDATE dynamic_qrs
                SET user_id = ?, name = ?, short_url = ?, destination_url = ?, qr_type = ?, status = ?,
                    password = ?, expiry_date = ?, custom_color = ?, logo = ?, updated_at = NOW()
                $ownSql
            ");
            $params = [$ownerId, $name, $short_url, $destination_url, $qr_type, $status, $password, $expiry_date, $custom_color, $logo, $id];
            if (!$editAny) { $params[] = $userId; }
            $stmt->execute($params);
        }
        $msg = 'QR updated successfully';
    } else {
        // Insert
        if ($hasStyleCol) {
            $stmt = $pdo->prepare("
                INSERT INTO dynamic_qrs
                (user_id, name, short_url, destination_url, qr_type, status, password, expiry_date, custom_color, logo, style_json, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$ownerId, $name, $short_url, $destination_url, $qr_type, $status, $password, $expiry_date, $custom_color, $logo, $style_json]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO dynamic_qrs
                (user_id, name, short_url, destination_url, qr_type, status, password, expiry_date, custom_color, logo, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$ownerId, $name, $short_url, $destination_url, $qr_type, $status, $password, $expiry_date, $custom_color, $logo]);
        }
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
