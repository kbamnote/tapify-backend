<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Only POST method allowed', 405);
}

try {
    $id = $_POST['id'] ?? '';
    $type = $_POST['type'] ?? '';
    $textContent = $_POST['text_content'] ?? null;

    if (empty($id) || empty($type)) {
        sendError('ID and type are required');
    }

    if (!in_array($type, ['text', 'image', 'mixed'])) {
        sendError('Invalid type');
    }

    $pdo = getDB();
    $imageUrl = null;

    if (($type === 'image' || $type === 'mixed') && isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Try Cloudinary first (persistent storage)
        try {
            if (defined('CLOUDINARY_CLOUD_NAME') && defined('CLOUDINARY_API_KEY')) {
                $result = uploadToCloudinary($_FILES['image']);
                if (!empty($result['secure_url'])) {
                    $imageUrl = $result['secure_url'];
                } elseif (!empty($result['url'])) {
                    $imageUrl = $result['url'];
                }
            }
        } catch (Throwable $e) {
            $imageUrl = null;
        }

        // Fallback to local storage
        if (!$imageUrl) {
            $uploadDir = __DIR__ . '/../../../uploads/categories/content/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array($ext, $allowed)) {
                sendError('Invalid image format');
            }
            $filename = uniqid('cnt_') . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
                $imageUrl = '/uploads/categories/content/' . $filename;
            } else {
                sendError('Failed to upload image');
            }
        }
    }

    if ($imageUrl) {
        $stmt = $pdo->prepare("UPDATE category_content SET type = ?, text_content = ?, image_url = ? WHERE id = ?");
        $stmt->execute([$type, $textContent, $imageUrl, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE category_content SET type = ?, text_content = ? WHERE id = ?");
        $stmt->execute([$type, $textContent, $id]);
    }

    sendSuccess('Content updated successfully');
} catch (Exception $e) {
    sendError('Failed to update content: ' . $e->getMessage(), 500);
}
