<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Only POST method allowed', 405);
}

try {
    $id = $_POST['id'] ?? '';
    $name = $_POST['name'] ?? '';

    if (empty($id) || empty($name)) {
        sendError('ID and name are required');
    }

    $pdo = getDB();
    $imageUrl = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
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
            $uploadDir = __DIR__ . '/../../uploads/categories/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array($ext, $allowed)) {
                sendError('Invalid image format');
            }
            $filename = uniqid('cat_') . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
                $imageUrl = '/uploads/categories/' . $filename;
            } else {
                sendError('Failed to upload image');
            }
        }
    }

    if ($imageUrl) {
        $stmt = $pdo->prepare("UPDATE categories SET name = ?, image_url = ? WHERE id = ?");
        $stmt->execute([$name, $imageUrl, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
        $stmt->execute([$name, $id]);
    }

    sendSuccess('Category updated successfully');
} catch (Exception $e) {
    sendError('Failed to update category: ' . $e->getMessage(), 500);
}
