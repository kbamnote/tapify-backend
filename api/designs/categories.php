<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

require_once __DIR__ . '/../../config/database.php';
ini_set('display_errors', 0);
require_once __DIR__ . '/../../includes/functions.php';

// Public endpoint - no auth required
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError('Method not allowed', 405);
}

try {
    $pdo = getDB();

    // Use SELECT * so it works even if columns are added/changed
    $stmt = $pdo->prepare(
        "SELECT * FROM design_categories
         WHERE is_active = 1
         ORDER BY sort_order ASC, id ASC"
    );
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    sendSuccess('Categories fetched', ['categories' => $categories]);
} catch (Exception $e) {
    // Return empty instead of crashing the app
    sendSuccess('Categories fetched', ['categories' => []]);
}
