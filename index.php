<?php
/**
 * TAPIFY - Main Router (Front Controller)
 */

require_once __DIR__ . '/config/database.php';

// Parse URL
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$alias = trim($uri, '/');

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

    // 3. Fallback to 404
    http_response_code(404);
    die('<!DOCTYPE html><html><head><title>404 Not Found</title><style>body{font-family:sans-serif;text-align:center;padding:50px;background:#f8f9fa}h1{color:#8338ec;font-size:4rem}a{color:#8338ec;text-decoration:none;font-weight:bold}</style></head><body><h1>404</h1><p>The page "'.htmlspecialchars($alias).'" was not found.</p><a href="/">Go Home</a></body></html>');

} catch (Exception $e) {
    http_response_code(500);
    die('Error: ' . $e->getMessage());
}
