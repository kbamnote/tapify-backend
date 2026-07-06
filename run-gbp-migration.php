<?php
/**
 * TAPIFY - One-time Google Business Profile migration runner.
 * Creates `google_business_connections` and `google_oauth_states`.
 * Open in a browser:  https://app.tapify.co.in/backend/run-gbp-migration.php
 * DELETE this file afterwards.
 */
require_once __DIR__ . '/config/database.php';

header('Content-Type: text/html; charset=utf-8');
echo "<h2>Tapify — Google Business Profile migration</h2>";

try {
    $pdo = getDB();
    echo "<p style='color:green'>✅ Connected to database.</p>";

    $sqlFile = __DIR__ . '/migration_google_business.sql';
    if (!file_exists($sqlFile)) {
        die("<p style='color:red'>❌ migration_google_business.sql not found.</p>");
    }
    $pdo->exec(file_get_contents($sqlFile));
    echo "<p style='color:green'>🎉 Migration executed.</p><h3>Verification</h3><ul>";
    foreach (['google_business_connections', 'google_oauth_states'] as $table) {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        $exists = $stmt->fetchColumn() !== false;
        $c = $exists ? 'green' : 'red';
        $m = $exists ? '✅' : '❌';
        echo "<li style='color:$c'>$m <b>$table</b> " . ($exists ? 'exists' : 'MISSING') . "</li>";
    }
    echo "</ul><p style='color:#b45309'><b>Delete this file (run-gbp-migration.php) now.</b></p>";
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
