<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError('Only GET method allowed', 405);
}

try {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY id DESC");
    $categories = $stmt->fetchAll();

    sendSuccess('Categories retrieved successfully', [
        'categories' => $categories
    ]);
} catch (Exception $e) {
    sendError('Failed to fetch categories: ' . $e->getMessage(), 500);
}
