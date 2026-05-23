<?php
require_once __DIR__ . '/config/database.php';

try {
    $pdo = getDB();
    echo "<h1>Database Table Checker</h1>";
    echo "<p>Connected to database successfully.</p>";

    // Fetch all tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "<h3>Found " . count($tables) . " tables:</h3>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li><strong>$table</strong></li>";
        
        // Show columns for each table
        $colStmt = $pdo->query("SHOW COLUMNS FROM `$table`");
        $columns = $colStmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<ul>";
        foreach ($columns as $col) {
            echo "<li>" . htmlspecialchars($col['Field']) . " (" . htmlspecialchars($col['Type']) . ")</li>";
        }
        echo "</ul>";
    }
    echo "</ul>";

} catch (Exception $e) {
    echo "<h1>Error</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
