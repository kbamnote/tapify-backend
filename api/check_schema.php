<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

try {
    $pdo = getDB();
    
    // Check tables
    $tables = [];
    $stmt = $pdo->query("SHOW TABLES");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }
    
    // Check columns of whatsapp_stores if it exists
    $columns = [];
    if (in_array('whatsapp_stores', $tables)) {
        $stmt = $pdo->query("DESCRIBE whatsapp_stores");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Check count of stores
    $storesCount = 0;
    $storesData = [];
    if (in_array('whatsapp_stores', $tables)) {
        $stmt = $pdo->query("SELECT id, user_id, store_name, status FROM whatsapp_stores LIMIT 10");
        $storesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $storesCount = count($storesData);
    }

    echo json_encode([
        'success' => true,
        'tables' => $tables,
        'whatsapp_stores_columns' => $columns,
        'stores_sample' => $storesData,
        'total_stores_in_db' => $storesCount
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
