<?php
/**
 * TAPIFY - Upload a center logo for a Dynamic QR
 * POST /api/dynamic-qr/upload-logo.php  (multipart: file)
 * Returns the Cloudinary URL; the caller stores it in dynamic_qrs.logo.
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Only POST allowed']);
    exit;
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit;
}

$file = $_FILES['file'];
$ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Allowed: jpg, jpeg, png, gif, webp, svg']);
    exit;
}
if ($file['size'] > 2 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'File too large (max 2MB)']);
    exit;
}

try {
    $uploadResult = uploadToCloudinary($file);
    if (!$uploadResult['success']) {
        echo json_encode(['success' => false, 'message' => 'Upload failed: ' . $uploadResult['message']]);
        exit;
    }
    echo json_encode(['success' => true, 'url' => $uploadResult['url']]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
