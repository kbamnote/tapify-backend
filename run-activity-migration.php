<?php
/**
 * TAPIFY - One-time migration for customer activity tracking.
 * Open in a browser: https://app.tapify.co.in/backend/run-activity-migration.php
 *
 * Safe to run more than once — every step checks before it changes anything,
 * and nothing is dropped or overwritten. Delete this file afterwards, like the
 * other run-*.php runners.
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/activity/ActivitySchema.php';

header('Content-Type: text/html; charset=utf-8');
echo '<h2>Tapify — Customer activity tracking migration</h2>';

try {
    $pdo = getDB();
    echo "<p style='color:green'>✅ Connected to database.</p><ul>";
    foreach (ActivitySchema::migrate($pdo) as $line) {
        echo '<li>' . htmlspecialchars($line) . '</li>';
    }
    echo '</ul>';
    echo ActivitySchema::isReady($pdo)
        ? "<p style='color:green'><b>🎉 Tracking is live.</b> Customer activity is recorded from now on.</p>"
        : "<p style='color:red'><b>❌ Something is still missing — see above.</b></p>";
    echo "<p style='color:#b45309'><b>Delete this file (run-activity-migration.php) now.</b></p>";
} catch (Throwable $e) {
    echo "<p style='color:red'>❌ Error: " . htmlspecialchars($e->getMessage()) . '</p>';
}
