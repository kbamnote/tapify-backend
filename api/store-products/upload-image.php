<?php
/**
 * TAPIFY - Upload Store Logo / Cover / Product Image
 * POST /backend/api/store-products/upload-image.php
 * Form data: file, store_id, type=(logo|cover|favicon|product), target_id
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$storeId = (int)($_POST['store_id'] ?? 0);
$type = $_POST['type'] ?? '';
$targetId = (int)($_POST['target_id'] ?? 0);

$validTypes = ['logo', 'cover', 'favicon', 'product', 'category'];
if (!in_array($type, $validTypes)) sendError('Invalid type');
if (!$storeId) sendError('Store ID required');

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    sendError('Upload error');
}

$file = $_FILES['file'];

$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) sendError('Invalid file type');

if ($file['size'] > 5 * 1024 * 1024) sendError('File too large (max 5MB)');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    $stmt = $pdo->prepare("SELECT id FROM whatsapp_stores WHERE id = ? AND user_id = ? LIMIT 1");
    $stmt->execute([$storeId, $userId]);
    if (!$stmt->fetch()) sendError('Access denied', 403);

    // --- CLOUDINARY UPLOAD ---
    $uploadResult = uploadToCloudinary($file);
    if (!$uploadResult['success']) {
        sendError('Cloudinary Upload Failed: ' . $uploadResult['message']);
    }

    $relativePath = $uploadResult['url']; // Store full secure URL
    $publicUrl = $uploadResult['url'];

    // Update DB based on type
    $oldImage = null;
    switch ($type) {
        case 'logo':
            $stmt = $pdo->prepare("SELECT logo_image FROM whatsapp_stores WHERE id = ?");
            $stmt->execute([$storeId]);
            $oldImage = $stmt->fetch()['logo_image'] ?? null;
            $pdo->prepare("UPDATE whatsapp_stores SET logo_image = ? WHERE id = ?")->execute([$relativePath, $storeId]);
            break;
        case 'cover':
            $stmt = $pdo->prepare("SELECT cover_image FROM whatsapp_stores WHERE id = ?");
            $stmt->execute([$storeId]);
            $oldImage = $stmt->fetch()['cover_image'] ?? null;
            $pdo->prepare("UPDATE whatsapp_stores SET cover_image = ? WHERE id = ?")->execute([$relativePath, $storeId]);
            break;
        case 'favicon':
            $stmt = $pdo->prepare("SELECT favicon_image FROM whatsapp_stores WHERE id = ?");
            $stmt->execute([$storeId]);
            $oldImage = $stmt->fetch()['favicon_image'] ?? null;
            $pdo->prepare("UPDATE whatsapp_stores SET favicon_image = ? WHERE id = ?")->execute([$relativePath, $storeId]);
            break;
        case 'product':
            if ($targetId > 0) {
                $stmt = $pdo->prepare("SELECT image FROM whatsapp_store_products WHERE id = ? AND store_id = ?");
                $stmt->execute([$targetId, $storeId]);
                $row = $stmt->fetch();
                if ($row) {
                    $oldImage = $row['image'];
                    $pdo->prepare("UPDATE whatsapp_store_products SET image = ? WHERE id = ? AND store_id = ?")
                        ->execute([$relativePath, $targetId, $storeId]);
                }
            }
            break;
        case 'category':
            if ($targetId > 0) {
                $stmt = $pdo->prepare("SELECT image FROM whatsapp_store_categories WHERE id = ? AND store_id = ?");
                $stmt->execute([$targetId, $storeId]);
                $row = $stmt->fetch();
                if ($row) {
                    $oldImage = $row['image'];
                    $pdo->prepare("UPDATE whatsapp_store_categories SET image = ? WHERE id = ? AND store_id = ?")
                        ->execute([$relativePath, $targetId, $storeId]);
                }
            }
            break;
    }

    // Delete old
    if ($oldImage) {
        $oldFullPath = __DIR__ . '/../../../' . $oldImage;
        if (file_exists($oldFullPath) && is_file($oldFullPath)) @unlink($oldFullPath);
    }

    sendSuccess('Image uploaded', [
        'path' => $relativePath,
        'url' => $publicUrl,
        'type' => $type
    ]);
} catch (Exception $e) {
    if (isset($newPath) && file_exists($newPath)) @unlink($newPath);
    sendError('Failed: ' . $e->getMessage(), 500);
}
