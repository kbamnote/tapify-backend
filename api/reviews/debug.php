<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = getDB();
    
    $stmt = $pdo->query("SELECT * FROM review_funnels");
    $funnels = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'database_funnel_count' => count($funnels),
        'all_funnels_in_database' => $funnels
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
