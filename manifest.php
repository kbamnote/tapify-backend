<?php
/**
 * Web App Manifest — enables "Add to Home Screen" on Chrome/Android.
 *
 * Dynamically generated per site so the site name and icons (from the
 * published document) are always correct.  Served as application/json.
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/builder/lib/SiteRepo.php';

$slug = tapify_subdomain_slug($_SERVER['HTTP_HOST'] ?? '');
if ($slug === '') {
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No site subdomain'], JSON_UNESCAPED_SLASHES);
    exit;
}

try {
    $site = SiteRepo::findBySlug($slug);
    if (!$site) {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Site not found'], JSON_UNESCAPED_SLASHES);
        exit;
    }

    $published = SiteRepo::getPublished($site);
    $doc = is_array($published) ? ($published['doc'] ?? null) : null;
} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Server error'], JSON_UNESCAPED_SLASHES);
    exit;
}

$name     = 'Website';
$favicon  = '';
$themeCol = '#2563EB';
$bgCol    = '#FFFFFF';

if (is_array($doc)) {
    $name    = $doc['site']['name'] ?? 'Website';
    $favicon = $doc['site']['favicon'] ?? '';
    if (!empty($doc['theme']['color']['primary'])) $themeCol = $doc['theme']['color']['primary'];
    if (!empty($doc['theme']['color']['bg']))       $bgCol    = $doc['theme']['color']['bg'];
}

// Resolve "media:123" to a full URL for the icon.
if (preg_match('/^media:(\d+)$/', $favicon, $m)) {
    $base = defined('SITE_URL') ? SITE_URL : 'https://app.tapify.co.in';
    $favicon = $base . '/api/sites/media.php?id=' . $m[1];
}

// If the favicon is a URL, prepend scheme if missing.
if ($favicon !== '' && !preg_match('#^https?://#i', $favicon) && !preg_match('#^/#', $favicon)) {
    $favicon = ''; // unknown ref — skip icons
}

$manifest = [
    'name'             => $name,
    'short_name'       => mb_substr($name, 0, 12, 'UTF-8'),
    'start_url'        => '/',
    'scope'            => '/',
    'display'          => 'standalone',
    'orientation'      => 'portrait-primary',
    'background_color' => $bgCol,
    'theme_color'      => $themeCol,
    'categories'       => ['business'],
];

if ($favicon !== '') {
    $manifest['icons'] = [
        ['src' => $favicon, 'sizes' => '192x192', 'type' => 'image/png'],
        ['src' => $favicon, 'sizes' => '512x512', 'type' => 'image/png'],
    ];
}

// Some crawlers (Twitter, Facebook) check the manifest for app info.
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=3600');
echo json_encode($manifest, JSON_UNESCAPED_SLASHES);
