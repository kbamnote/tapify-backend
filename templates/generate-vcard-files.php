<?php
/**
 * One-time generator: creates vcard1.php … vcard42.php
 * Run: php backend/templates/generate-vcard-files.php
 */
$registry = require __DIR__ . '/_theme-registry.php';
$dir = __DIR__;

foreach ($registry as $slug => $meta) {
    $name = $meta['name'] ?? $slug;
    $num = (int) str_replace('vcard', '', $slug);
    $content = <<<PHP
<?php
/**
 * Tapify vCard Template: {$name} ({$slug})
 *
 * Dedicated template file — routed from vcard.php by template_id.
 * @see templates/README.md
 */
\$TAPIFY_TEMPLATE_SLUG = '{$slug}';
require __DIR__ . '/_bootstrap.php';

PHP;
    $path = "{$dir}/{$slug}.php";
    file_put_contents($path, $content);
    echo "Created {$slug}.php\n";
}

echo "Done. " . count($registry) . " template entry files.\n";
