<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Only POST method allowed', 405);
}

try {
    $name = $_POST['name'] ?? '';

    if (empty($name)) {
        sendError('Category name is required');
    }

    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        sendError('Category image is required');
    }

    $imageUrl = null;

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

    $pdo = getDB();
    $stmt = $pdo->prepare("INSERT INTO categories (name, image_url) VALUES (?, ?)");
    $stmt->execute([$name, $imageUrl]);
    $id = $pdo->lastInsertId();

    sendSuccess('Category created successfully', [
        'category' => ['id' => $id, 'name' => $name, 'image_url' => $imageUrl]
    ]);
} catch (Exception $e) {
    sendError('Failed to create category: ' . $e->getMessage(), 500);
}
