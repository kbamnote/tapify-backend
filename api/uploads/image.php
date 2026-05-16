<?php
/**
 * TAPIFY - Image Upload API
 * POST /backend/api/uploads/image.php
 * Multipart form-data with:
 *   - file: the image file
 *   - vcard_id: which vCard owns it
 *   - type: 'cover' | 'profile' | 'favicon' | 'product' | 'blog' | 'testimonial' | 'gallery'
 *   - target_id: (optional) ID of product/blog/testimonial/gallery to update
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Only POST allowed', 405);
}

$vcardId = (int)($_POST['vcard_id'] ?? 0);
$type = $_POST['type'] ?? '';
$targetId = (int)($_POST['target_id'] ?? 0);

$validTypes = ['cover', 'profile', 'favicon', 'product', 'blog', 'testimonial', 'gallery'];
if (!in_array($type, $validTypes)) {
    sendError('Invalid type. Must be: ' . implode(', ', $validTypes));
}

if (!$vcardId) sendError('vCard ID required');
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    $errMsgs = [
        UPLOAD_ERR_INI_SIZE => 'File too large (server limit)',
        UPLOAD_ERR_FORM_SIZE => 'File too large (form limit)',
        UPLOAD_ERR_PARTIAL => 'File partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Server temp dir missing',
        UPLOAD_ERR_CANT_WRITE => 'Server cannot write',
    ];
    $errCode = $_FILES['file']['error'] ?? UPLOAD_ERR_NO_FILE;
    sendError($errMsgs[$errCode] ?? 'Upload error');
}

$file = $_FILES['file'];

// Validate file type
$allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowedExts)) {
    sendError('Invalid file type. Allowed: ' . implode(', ', $allowedExts));
}

// Validate MIME type
$allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);
if (!in_array($mime, $allowedMimes)) {
    sendError('Invalid image format');
}

// Size limit: 5MB
if ($file['size'] > 5 * 1024 * 1024) {
    sendError('File too large. Max 5MB');
}

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    // Verify vCard ownership
    $stmt = $pdo->prepare("SELECT id FROM vcards WHERE id = ? AND user_id = ? LIMIT 1");
    $stmt->execute([$vcardId, $userId]);
    if (!$stmt->fetch()) sendError('Access denied', 403);

    // --- CLOUDINARY UPLOAD ---
    $uploadResult = uploadToCloudinary($file);
    if (!$uploadResult['success']) {
        sendError('Cloudinary Upload Failed: ' . $uploadResult['message']);
    }

    $relativePath = $uploadResult['url']; // Store full secure URL
    $publicUrl = $uploadResult['url'];

    // Update database based on type
    $oldImageToDelete = null;

    switch ($type) {
        case 'cover':
            // Get old image to delete
            $stmt = $pdo->prepare("SELECT cover_image FROM vcards WHERE id = ?");
            $stmt->execute([$vcardId]);
            $oldImageToDelete = $stmt->fetch()['cover_image'] ?? null;

            $stmt = $pdo->prepare("UPDATE vcards SET cover_image = ? WHERE id = ?");
            $stmt->execute([$relativePath, $vcardId]);
            break;

        case 'profile':
            $stmt = $pdo->prepare("SELECT profile_image FROM vcards WHERE id = ?");
            $stmt->execute([$vcardId]);
            $oldImageToDelete = $stmt->fetch()['profile_image'] ?? null;

            $stmt = $pdo->prepare("UPDATE vcards SET profile_image = ? WHERE id = ?");
            $stmt->execute([$relativePath, $vcardId]);
            break;

        case 'favicon':
            $stmt = $pdo->prepare("SELECT favicon_image FROM vcards WHERE id = ?");
            $stmt->execute([$vcardId]);
            $oldImageToDelete = $stmt->fetch()['favicon_image'] ?? null;

            $stmt = $pdo->prepare("UPDATE vcards SET favicon_image = ? WHERE id = ?");
            $stmt->execute([$relativePath, $vcardId]);
            break;

        case 'product':
            if ($targetId > 0) {
                $stmt = $pdo->prepare("SELECT image FROM vcard_products WHERE id = ? AND vcard_id = ?");
                $stmt->execute([$targetId, $vcardId]);
                $row = $stmt->fetch();
                if ($row) {
                    $oldImageToDelete = $row['image'];
                    $stmt = $pdo->prepare("UPDATE vcard_products SET image = ? WHERE id = ? AND vcard_id = ?");
                    $stmt->execute([$relativePath, $targetId, $vcardId]);
                }
            }
            break;

        case 'blog':
            if ($targetId > 0) {
                $stmt = $pdo->prepare("SELECT image FROM vcard_blogs WHERE id = ? AND vcard_id = ?");
                $stmt->execute([$targetId, $vcardId]);
                $row = $stmt->fetch();
                if ($row) {
                    $oldImageToDelete = $row['image'];
                    $stmt = $pdo->prepare("UPDATE vcard_blogs SET image = ? WHERE id = ? AND vcard_id = ?");
                    $stmt->execute([$relativePath, $targetId, $vcardId]);
                }
            }
            break;

        case 'testimonial':
            if ($targetId > 0) {
                $stmt = $pdo->prepare("SELECT image FROM vcard_testimonials WHERE id = ? AND vcard_id = ?");
                $stmt->execute([$targetId, $vcardId]);
                $row = $stmt->fetch();
                if ($row) {
                    $oldImageToDelete = $row['image'];
                    $stmt = $pdo->prepare("UPDATE vcard_testimonials SET image = ? WHERE id = ? AND vcard_id = ?");
                    $stmt->execute([$relativePath, $targetId, $vcardId]);
                }
            }
            break;

        case 'gallery':
            if ($targetId > 0) {
                // Verify gallery belongs to vCard
                $stmt = $pdo->prepare("SELECT id FROM vcard_galleries WHERE id = ? AND vcard_id = ?");
                $stmt->execute([$targetId, $vcardId]);
                if (!$stmt->fetch()) sendError('Gallery not found', 404);

                // Get current max display_order
                $stmt = $pdo->prepare("SELECT COALESCE(MAX(display_order), -1) + 1 AS next_order FROM vcard_gallery_images WHERE gallery_id = ?");
                $stmt->execute([$targetId]);
                $nextOrder = (int)$stmt->fetch()['next_order'];

                // Insert new image
                $stmt = $pdo->prepare("INSERT INTO vcard_gallery_images (gallery_id, image_url, display_order) VALUES (?, ?, ?)");
                $stmt->execute([$targetId, $relativePath, $nextOrder]);
            }
            break;
    }

    // Delete old image file (if exists)
    if ($oldImageToDelete) {
        $oldFullPath = __DIR__ . '/../../../' . $oldImageToDelete;
        if (file_exists($oldFullPath) && is_file($oldFullPath)) {
            @unlink($oldFullPath);
        }
    }

    sendSuccess('Image uploaded successfully', [
        'path' => $relativePath,
        'url' => $publicUrl,
        'type' => $type
    ]);

} catch (Exception $e) {
    // Cleanup on error
    if (isset($newPath) && file_exists($newPath)) @unlink($newPath);
    sendError('Upload failed: ' . $e->getMessage(), 500);
}
