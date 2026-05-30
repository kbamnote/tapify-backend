<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Only POST method allowed', 405);
}

try {
    $categoryId = $_POST['category_id'] ?? '';
    $type = $_POST['type'] ?? '';
    $textContent = $_POST['text_content'] ?? null;

    if (empty($categoryId) || empty($type)) {
        sendError('Category ID and type are required');
    }

    if (!in_array($type, ['text', 'image', 'mixed'])) {
        sendError('Invalid type');
    }

    $imageUrl = null;

    if ($type === 'image' || $type === 'mixed') {
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            sendError('Image file is required for this type');
        }

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

    if (($type === 'text' || $type === 'mixed') && empty($textContent)) {
        sendError('Text content is required for this type');
    }

    $pdo = getDB();
    $stmt = $pdo->prepare("INSERT INTO category_content (category_id, type, text_content, image_url) VALUES (?, ?, ?, ?)");
    $stmt->execute([$categoryId, $type, $textContent, $imageUrl]);
    $id = $pdo->lastInsertId();

    sendSuccess('Content added successfully', [
        'content' => [
            'id' => $id,
            'category_id' => $categoryId,
            'type' => $type,
            'text_content' => $textContent,
            'image_url' => $imageUrl
        ]
    ]);
} catch (Exception $e) {
    sendError('Failed to add content: ' . $e->getMessage(), 500);
}
