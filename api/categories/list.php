<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError('Only GET method allowed', 405);
}

try {
    $pdo = getDB();

    if (isset($_GET['parent_id'])) {
        // Return sub-categories of the given parent
        $parentId = (int) $_GET['parent_id'];
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE parent_id = ? ORDER BY id ASC");
        $stmt->execute([$parentId]);
    } else {
        // Return only top-level categories (no parent)
        $stmt = $pdo->query("SELECT * FROM categories WHERE parent_id IS NULL ORDER BY id DESC");
    }

    $categories = $stmt->fetchAll();

    sendSuccess('Categories retrieved successfully', [
        'categories' => $categories
    ]);
} catch (Exception $e) {
    sendError('Failed to fetch categories: ' . $e->getMessage(), 500);
}
