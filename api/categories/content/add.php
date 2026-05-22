<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Only POST method allowed', 405);
}

try {
    $categoryId = $_POST['category_id'] ?? '';
    $type = $_POST['type'] ?? ''; // 'text', 'image', 'mixed'
    $textContent = $_POST['text_content'] ?? null;
    
    if (empty($categoryId) || empty($type)) {
        sendError('Category ID and type are required');
    }

    if (!in_array($type, ['text', 'image', 'mixed'])) {
        sendError('Invalid type');
    }

    $imageUrl = null;
    if (($type === 'image' || $type === 'mixed') && isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../../uploads/categories/content/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileInfo = pathinfo($_FILES['image']['name']);
        $ext = strtolower($fileInfo['extension']);
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (!in_array($ext, $allowed)) {
            sendError('Invalid image format');
        }
        
        $filename = uniqid('cnt_') . '.' . $ext;
        $destination = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
            $imageUrl = '/uploads/categories/content/' . $filename;
        } else {
            sendError('Failed to upload image');
        }
    } else if ($type === 'image' || $type === 'mixed') {
        sendError('Image file is required for this type');
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
