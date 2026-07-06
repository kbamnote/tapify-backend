<?php
/**
 * TAPIFY - One-time AI Growth Center migration runner.
 *
 * Creates the `ai_cache` and `ai_history` tables from migration_ai_growth.sql.
 * Safe to run more than once (CREATE TABLE IF NOT EXISTS).
 *
 * HOW TO RUN: deploy this file with the backend, then open it in a browser:
 *   https://app.tapify.co.in/backend/run-ai-migration.php
 * DELETE this file afterwards.
 */

require_once __DIR__ . '/config/database.php';

header('Content-Type: text/html; charset=utf-8');
echo "<h2>Tapify — AI Growth Center migration</h2>";

try {
    $pdo = getDB();
    echo "<p style='color:green'>✅ Connected to database.</p>";

    $sqlFile = __DIR__ . '/migration_ai_growth.sql';
    if (!file_exists($sqlFile)) {
        die("<p style='color:red'>❌ migration_ai_growth.sql not found in the backend folder.</p>");
    }

    $sql = file_get_contents($sqlFile);
    $pdo->exec($sql);
    echo "<p style='color:green'>🎉 Migration executed.</p>";

    // Verify both tables now exist.
    echo "<h3>Verification</h3><ul>";
    foreach (['ai_cache', 'ai_history'] as $table) {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        $exists = $stmt->fetchColumn() !== false;
        $mark = $exists ? "✅" : "❌";
        $color = $exists ? "green" : "red";
        echo "<li style='color:$color'>$mark <b>$table</b> " . ($exists ? "exists" : "MISSING") . "</li>";
    }
    echo "</ul>";

    echo "<p style='color:#b45309'><b>Important:</b> delete this file "
       . "(run-ai-migration.php) now for security.</p>";
    echo "<a href='index.php'>← API Home</a>";

} catch (Exception $e) {
    echo "<p style='color:red'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
