<?php
/**
 * TAPIFY - Main Router (Front Controller)
 */

require_once __DIR__ . '/config/database.php';

// Parse URL
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$alias = trim($uri, '/');

// Wildcard subdomain support: <slug>.tapify.co.in with no path -> treat the
// subdomain as the alias. Path-based URLs (app.tapify.co.in/<slug>) are unchanged.
if ($alias === '' || $alias === 'index.php') {
    $subSlug = tapify_subdomain_slug($_SERVER['HTTP_HOST'] ?? '');
    if ($subSlug !== '') {
        $alias = $subSlug;
    }
}

// If root, show API status
if (empty($alias) || $alias === 'index.php') {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Tapify Backend API is running',
        'version' => '1.0.0'
    ]);
    exit;
}

// WhatsApp Cloud API webhook (GET verify / POST receive). Handled before any DB
// lookup so it never collides with vCard/store slugs and can't break them.
if ($alias === 'webhook') {
    require __DIR__ . '/webhook.php';
    exit;
}

// SEO endpoints — served on every host (incl. business subdomains). Handled
// before the DB slug lookup so they never collide with a vCard/store alias.
if ($alias === 'sitemap.xml') {
    require __DIR__ . '/sitemap.php';
    exit;
}
if ($alias === 'robots.txt') {
    require __DIR__ . '/robots.php';
    exit;
}

// Ignore static files
if (preg_match('/\.(?:png|jpg|jpeg|gif|css|js|ico)$/', $alias)) {
    return false;
}

// Ignore API folder (let Nginx/Apache handle it or handle it here)
if (strpos($alias, 'api/') === 0) {
    return false;
}

try {
    $pdo = getDB();

    // 0. Builder website (JSON document, rendered here in PHP like a vCard).
    //    The subdomain identifies the owner, so a published builder site owns the
    //    whole host (root + every sub-page). Non-builder subdomains fall through.
    $builderSlug = tapify_subdomain_slug($_SERVER['HTTP_HOST'] ?? '');
    if ($builderSlug !== '') {
        require_once __DIR__ . '/builder/render/SiteRenderer.php';
        if (SiteRenderer::hasPublishedSite($builderSlug)) {
            $pagePath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
            SiteRenderer::renderBySlug($builderSlug, $pagePath);
            exit;
        }
    }

    // 1. Check if it's a vCard
    $stmt = $pdo->prepare("SELECT id FROM vcards WHERE url_alias = ? AND status = 1 LIMIT 1");
    $stmt->execute([$alias]);
    if ($stmt->fetch()) {
        $_GET['alias'] = $alias;
        require __DIR__ . '/vcard.php';
        exit;
    }

    // 2. Check if it's a Store
    $stmt = $pdo->prepare("SELECT id FROM whatsapp_stores WHERE url_alias = ? AND status = 1 LIMIT 1");
    $stmt->execute([$alias]);
    if ($stmt->fetch()) {
        $_GET['alias'] = $alias;
        require __DIR__ . '/store.php';
        exit;
    }

    // 3. Check if it's a Dynamic QR Short Link
    if (strpos($alias, 'qr/') === 0) {
        $shortUrl = substr($alias, 3); // remove 'qr/'
        $stmt = $pdo->prepare("SELECT * FROM dynamic_qrs WHERE short_url = ? LIMIT 1");
        $stmt->execute([$shortUrl]);
        $qr = $stmt->fetch();
        
        if ($qr) {
            // Check status
            if ($qr['status'] == 0) {
                http_response_code(403);
                die('<!DOCTYPE html><html><head><title>Inactive QR</title><style>body{font-family:sans-serif;text-align:center;padding:50px;background:#f8f9fa}h1{color:#ef4444;font-size:2rem}</style></head><body><h1>This QR code is inactive or disabled.</h1></body></html>');
            }
            
            // Log scan
            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $device = (strpos(strtolower($userAgent), 'mobile') !== false || strpos(strtolower($userAgent), 'android') !== false || strpos(strtolower($userAgent), 'iphone') !== false) ? 'Mobile' : 'Desktop';
            
            $browser = 'Unknown';
            if (strpos($userAgent, 'Edg') !== false) $browser = 'Edge';
            elseif (strpos($userAgent, 'Chrome') !== false) $browser = 'Chrome';
            elseif (strpos($userAgent, 'Safari') !== false) $browser = 'Safari';
            elseif (strpos($userAgent, 'Firefox') !== false) $browser = 'Firefox';

            try {
                $logStmt = $pdo->prepare("INSERT INTO dynamic_qr_scans (qr_id, ip_address, device_type, browser, scanned_at) VALUES (?, ?, ?, ?, NOW())");
                $logStmt->execute([$qr['id'], $ip, $device, $browser]);
            } catch (Exception $e) {}

            // Redirect
            $dest = $qr['destination_url'];
            if (!preg_match("~^(?:f|ht)tps?://~i", $dest)) {
                $dest = "http://" . $dest;
            }
            header("Location: " . $dest);
            exit;
        }
    }

    // 4. Fallback to 404
    http_response_code(404);
    die('<!DOCTYPE html><html><head><title>404 Not Found</title><style>body{font-family:sans-serif;text-align:center;padding:50px;background:#f8f9fa}h1{color:#8338ec;font-size:4rem}a{color:#8338ec;text-decoration:none;font-weight:bold}</style></head><body><h1>404</h1><p>The page "'.htmlspecialchars($alias).'" was not found.</p><a href="/">Go Home</a></body></html>');

} catch (Exception $e) {
    http_response_code(500);
    die('Error: ' . $e->getMessage());
}
