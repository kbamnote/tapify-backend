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

    $imageUrl = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../uploads/categories/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileInfo = pathinfo($_FILES['image']['name']);
        $ext = strtolower($fileInfo['extension']);
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (!in_array($ext, $allowed)) {
            sendError('Invalid image format');
        }
        
        $filename = uniqid('cat_') . '.' . $ext;
        $destination = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
            $imageUrl = '/uploads/categories/' . $filename;
        } else {
            sendError('Failed to upload image');
        }
    } else {
        sendError('Category image is required');
    }

    $pdo = getDB();
    $stmt = $pdo->prepare("INSERT INTO categories (name, image_url) VALUES (?, ?)");
    $stmt->execute([$name, $imageUrl]);
    $id = $pdo->lastInsertId();

    sendSuccess('Category created successfully', [
        'category' => [
            'id' => $id,
            'name' => $name,
            'image_url' => $imageUrl
        ]
    ]);
} catch (Exception $e) {
    sendError('Failed to create category: ' . $e->getMessage(), 500);
}
