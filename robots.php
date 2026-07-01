<?php
/**
 * TAPIFY - Dynamic robots.txt
 * Allows crawling of public mini-sites and points crawlers at the sitemap.
 * Served on any host (business subdomains + app.tapify.co.in) via the front
 * controller (index.php / router.php).
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/seo.php';

header('Content-Type: text/plain; charset=utf-8');

$sitemapUrl = (defined('SITE_URL') ? rtrim(SITE_URL, '/') : '') . '/sitemap.xml';
echo tapify_build_robots_txt($sitemapUrl);
