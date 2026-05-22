<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError('Only GET method allowed', 405);
}

try {
    $categoryId = $_GET['category_id'] ?? '';
    
    if (empty($categoryId)) {
        sendError('Category ID is required');
    }

    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM category_content WHERE category_id = ? ORDER BY id DESC");
    $stmt->execute([$categoryId]);
    $content = $stmt->fetchAll();

    sendSuccess('Content retrieved successfully', [
        'content' => $content
    ]);
} catch (Exception $e) {
    sendError('Failed to fetch content: ' . $e->getMessage(), 500);
}
