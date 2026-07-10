<?php
/**
 * TAPIFY - One-time WhatsApp Store Themes migration runner (idempotent).
 * Open in a browser: https://app.tapify.co.in/backend/run-store-themes-migration.php
 * Safe to run multiple times. DELETE this file afterwards.
 */
require_once __DIR__ . '/config/database.php';

header('Content-Type: text/html; charset=utf-8');
echo "<h2>Tapify — WhatsApp Store Themes migration</h2>";

function columnExists(PDO $pdo, string $table, string $col): bool {
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?"
    );
    $stmt->execute([$table, $col]);
    return (int)$stmt->fetchColumn() > 0;
}
function indexExists(PDO $pdo, string $table, string $index): bool {
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) FROM information_schema.STATISTICS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?"
    );
    $stmt->execute([$table, $index]);
    return (int)$stmt->fetchColumn() > 0;
}

try {
    $pdo = getDB();
    echo "<p style='color:green'>✅ Connected to database.</p><ul>";

    // column => full column definition
    $columns = [
        'accent_color'     => "VARCHAR(20) DEFAULT NULL",
        'text_color'       => "VARCHAR(20) DEFAULT NULL",
        'font_family'      => "VARCHAR(80) DEFAULT NULL",
        'theme_mode'       => "ENUM('light','dark','auto') NOT NULL DEFAULT 'light'",
        'enable_translate' => "TINYINT(1) NOT NULL DEFAULT 1",
        'enable_pwa'       => "TINYINT(1) NOT NULL DEFAULT 0",
        'seo_title'        => "VARCHAR(200) DEFAULT NULL",
        'seo_description'  => "TEXT DEFAULT NULL",
    ];

    foreach ($columns as $col => $def) {
        if (columnExists($pdo, 'whatsapp_stores', $col)) {
            echo "<li style='color:#6b7280'>• <b>$col</b> already exists — skipped</li>";
        } else {
            $pdo->exec("ALTER TABLE `whatsapp_stores` ADD COLUMN `$col` $def");
            echo "<li style='color:green'>✅ added <b>$col</b></li>";
        }
    }

    if (indexExists($pdo, 'whatsapp_store_products', 'idx_store_created')) {
        echo "<li style='color:#6b7280'>• index <b>idx_store_created</b> already exists — skipped</li>";
    } else {
        $pdo->exec("ALTER TABLE `whatsapp_store_products` ADD INDEX `idx_store_created` (`store_id`, `created_at`)");
        echo "<li style='color:green'>✅ added index <b>idx_store_created</b></li>";
    }

    echo "</ul><p style='color:green'>🎉 Migration complete.</p>";
    echo "<p style='color:#b45309'><b>Delete this file (run-store-themes-migration.php) now.</b></p>";
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
