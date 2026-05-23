<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT * FROM review_funnels");
    $funnels = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'count' => count($funnels),
        'funnels' => $funnels
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
