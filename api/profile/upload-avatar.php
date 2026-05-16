<?php
/**
 * TAPIFY - Upload Avatar
 * POST /backend/api/profile/upload-avatar.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    sendError('Upload error');
}

$file = $_FILES['file'];

$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) sendError('Invalid file type');
if ($file['size'] > 2 * 1024 * 1024) sendError('File too large (max 2MB)');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    // --- CLOUDINARY UPLOAD ---
    $uploadResult = uploadToCloudinary($file);
    if (!$uploadResult['success']) {
        sendError('Cloudinary Upload Failed: ' . $uploadResult['message']);
    }

    $relativePath = $uploadResult['url']; // Store full secure URL
    $publicUrl = $uploadResult['url'];

    // Get old avatar to delete
    $stmt = $pdo->prepare("SELECT avatar FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $oldAvatar = $stmt->fetch()['avatar'] ?? null;

    // Update DB
    $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?")->execute([$relativePath, $userId]);

    // Delete old
    if ($oldAvatar && file_exists(__DIR__ . '/../../../' . $oldAvatar)) {
        @unlink(__DIR__ . '/../../../' . $oldAvatar);
    }

    sendSuccess('Avatar uploaded', ['avatar_url' => $publicUrl]);
} catch (Exception $e) {
    if (isset($newPath) && file_exists($newPath)) @unlink($newPath);
    sendError('Failed: ' . $e->getMessage(), 500);
}
