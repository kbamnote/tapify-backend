<?php
/**
 * TAPIFY - One-time migration for public engagement tracking.
 * Open in a browser: https://app.tapify.co.in/backend/run-engagement-migration.php
 *
 * Creates the three tables behind "how many people opened the card, tapped the
 * NFC tag, visited the website, scanned the review card". Safe to run more than
 * once — nothing is dropped or overwritten. Delete this file afterwards, like
 * the other run-*.php runners.
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/engagement/EngagementSchema.php';

header('Content-Type: text/html; charset=utf-8');
echo '<h2>Tapify — Public engagement tracking migration</h2>';

try {
    $pdo = getDB();
    echo "<p style='color:green'>✅ Connected to database.</p><ul>";
    foreach (EngagementSchema::migrate($pdo) as $line) {
        echo '<li>' . htmlspecialchars($line) . '</li>';
    }
    echo '</ul>';

    $missing = [];
    foreach (['engagement_events', 'engagement_daily', 'engagement_visitors'] as $table) {
        $st = $pdo->prepare('SELECT COUNT(*) FROM information_schema.tables
                              WHERE table_schema = DATABASE() AND table_name = ?');
        $st->execute([$table]);
        if ((int)$st->fetchColumn() === 0) {
            $missing[] = $table;
        }
    }
    echo empty($missing)
        ? "<p style='color:green'><b>🎉 Tracking is live.</b> Card views, NFC taps, website visits, review-card scans and every tap are recorded from now on.</p>"
        : "<p style='color:red'><b>❌ Still missing: " . htmlspecialchars(implode(', ', $missing)) . '</b></p>';
    echo "<p style='color:#b45309'><b>Delete this file (run-engagement-migration.php) now.</b></p>";
} catch (Throwable $e) {
    echo "<p style='color:red'>❌ Error: " . htmlspecialchars($e->getMessage()) . '</p>';
}
