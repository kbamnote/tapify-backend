<?php
/**
 * TAPIFY - One-time Database Importer
 * This script imports database.sql into your Railway MySQL database.
 */

require_once __DIR__ . '/config/database.php';

echo "<h2>Tapify Database Importer</h2>";

try {
    $pdo = getDB();
    echo "<p style='color:green'>✅ Connected to database successfully.</p>";

    $sqlFile = __DIR__ . '/database.sql';
    if (!file_exists($sqlFile)) {
        die("<p style='color:red'>❌ Error: database.sql not found in backend folder.</p>");
    }

    $sql = file_get_contents($sqlFile);
    
    // Railway/PHP might have timeout issues with huge files, but this one is small
    // We split by semicolon to execute one by one (basic approach)
    // Note: This works for simple SQL files like yours
    $pdo->exec($sql);

    echo "<p style='color:green'>🎉 <b>Success!</b> Database tables imported successfully.</p>";
    echo "<p>You can now delete this file (import-db.php) for security.</p>";
    echo "<a href='index.php'>Go to API Home</a>";

} catch (Exception $e) {
    echo "<p style='color:red'>❌ Error during import: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
