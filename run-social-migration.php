<?php
/**
 * TAPIFY - One-time Social Publishing migration runner.
 * Open in a browser: https://app.tapify.co.in/backend/run-social-migration.php
 * DELETE this file afterwards.
 */
require_once __DIR__ . '/config/database.php';

header('Content-Type: text/html; charset=utf-8');
echo "<h2>Tapify — Social Publishing migration</h2>";

try {
    $pdo = getDB();
    echo "<p style='color:green'>✅ Connected to database.</p>";
    $sqlFile = __DIR__ . '/migration_social.sql';
    if (!file_exists($sqlFile)) {
        die("<p style='color:red'>❌ migration_social.sql not found.</p>");
    }
    $pdo->exec(file_get_contents($sqlFile));
    echo "<p style='color:green'>🎉 Migration executed.</p><h3>Verification</h3><ul>";
    foreach (['social_connections', 'social_posts', 'social_post_targets', 'social_oauth_states'] as $t) {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$t]);
        $exists = $stmt->fetchColumn() !== false;
        $c = $exists ? 'green' : 'red';
        echo "<li style='color:$c'>" . ($exists ? '✅' : '❌') . " <b>$t</b> " . ($exists ? 'exists' : 'MISSING') . "</li>";
    }
    echo "</ul><p style='color:#b45309'><b>Delete this file (run-social-migration.php) now.</b></p>";
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
