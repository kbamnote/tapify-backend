<?php
/**
 * TAPIFY - Dynamic sitemap.xml
 * Lists every active vCard and WhatsApp Store at its canonical (subdomain) URL.
 * Served on any host via the front controller (index.php / router.php).
 *
 * Submit https://app.tapify.co.in/sitemap.xml in Google Search Console under a
 * DOMAIN property for tapify.co.in (a domain property covers all subdomains, so
 * the cross-subdomain <loc> URLs are accepted).
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/seo.php';

header('Content-Type: application/xml; charset=utf-8');
header('X-Robots-Tag: noindex'); // the sitemap file itself shouldn't be indexed

$entries = [];
try {
    $pdo = getDB();

    $stmt = $pdo->query("SELECT url_alias, updated_at FROM vcards WHERE status = 1 ORDER BY id");
    foreach ($stmt as $row) {
        $alias = trim((string)($row['url_alias'] ?? ''));
        if ($alias === '') continue;
        $entries[] = [
            'loc'     => public_card_url($alias),
            'lastmod' => tapify_seo_w3c_date($row['updated_at'] ?? ''),
        ];
    }

    $stmt = $pdo->query("SELECT url_alias, updated_at FROM whatsapp_stores WHERE status = 1 ORDER BY id");
    foreach ($stmt as $row) {
        $alias = trim((string)($row['url_alias'] ?? ''));
        if ($alias === '') continue;
        $entries[] = [
            'loc'     => public_card_url($alias),
            'lastmod' => tapify_seo_w3c_date($row['updated_at'] ?? ''),
        ];
    }
} catch (Throwable $e) {
    // Return a valid (possibly empty) urlset rather than a 500 so crawlers don't
    // record the sitemap as broken.
}

echo tapify_build_sitemap_xml($entries);
