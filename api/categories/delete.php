<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Only POST method allowed', 405);
}

try {
    $id = $_POST['id'] ?? '';

    if (empty($id)) {
        sendError('Category ID is required');
    }

    $pdo = getDB();
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);

    sendSuccess('Category deleted successfully');
} catch (Exception $e) {
    sendError('Failed to delete category: ' . $e->getMessage(), 500);
}
