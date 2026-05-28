<?php
/**
 * TAPIFY - Upload Business Logo
 * POST /api/business/upload-logo.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    sendError('No file uploaded or upload error occurred');
}

$file = $_FILES['file'];
$ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
    sendError('Invalid file type. Allowed: jpg, jpeg, png, gif, webp');
}
if ($file['size'] > 2 * 1024 * 1024) {
    sendError('File too large (max 2MB)');
}

try {
    $pdo    = getDB();
    $userId = getCurrentUserId();

    // Upload to Cloudinary
    $uploadResult = uploadToCloudinary($file);
    if (!$uploadResult['success']) {
        sendError('Upload failed: ' . $uploadResult['message']);
    }

    $logoUrl = $uploadResult['url'];

    // Upsert: update logo if record exists, insert stub if not
    $stmt = $pdo->prepare("SELECT id FROM businesses WHERE user_id = ?");
    $stmt->execute([$userId]);
    $existing = $stmt->fetch();

    if ($existing) {
        $pdo->prepare("UPDATE businesses SET logo = ? WHERE user_id = ?")
            ->execute([$logoUrl, $userId]);
    } else {
        // Create a minimal record so logo is saved; user completes rest in save.php
        $pdo->prepare("INSERT INTO businesses (user_id, business_name, logo) VALUES (?, '', ?)")
            ->execute([$userId, $logoUrl]);
    }

    sendSuccess('Logo uploaded successfully', ['logo_url' => $logoUrl]);

} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
