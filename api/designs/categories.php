<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';
ini_set('display_errors', 0);
require_once __DIR__ . '/../../includes/functions.php';

// Public endpoint - no auth required
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError('Method not allowed', 405);
}

try {
    $pdo = getDB();

    $stmt = $pdo->prepare(
        "SELECT id, name, slug, icon, bg_color, text_color, image_url, sort_order
         FROM design_categories
         WHERE is_active = 1
         ORDER BY sort_order ASC, id ASC"
    );
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    sendSuccess('Categories fetched', ['categories' => $categories]);
} catch (Exception $e) {
    sendError('Failed to fetch categories: ' . $e->getMessage(), 500);
}
