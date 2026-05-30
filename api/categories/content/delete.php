<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Only POST method allowed', 405);
}

try {
    $id = $_POST['id'] ?? '';

    if (empty($id)) {
        sendError('Content ID is required');
    }

    $pdo = getDB();
    $stmt = $pdo->prepare("DELETE FROM category_content WHERE id = ?");
    $stmt->execute([$id]);

    sendSuccess('Content deleted successfully');
} catch (Exception $e) {
    sendError('Failed to delete content: ' . $e->getMessage(), 500);
}
