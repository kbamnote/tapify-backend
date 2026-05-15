<?php
/**
 * TAPIFY - Unified Public URL Router
 * Decides whether a URL alias belongs to a vCard or WhatsApp Store
 */

require_once __DIR__ . '/config/database.php';

$alias = trim($_GET['alias'] ?? '');
if (empty($alias)) {
    header('Location: ../frontend/index.html');
    exit;
}

try {
    $pdo = getDB();

    // Check if it's a vCard first
    $stmt = $pdo->prepare("SELECT 'vcard' AS type FROM vcards WHERE url_alias = ? AND status = 1 LIMIT 1");
    $stmt->execute([$alias]);
    $vcardMatch = $stmt->fetch();

    if ($vcardMatch) {
        // It's a vCard - load vcard.php
        $_GET['alias'] = $alias;
        require __DIR__ . '/vcard.php';
        exit;
    }

    // Check if it's a Store
    $stmt = $pdo->prepare("SELECT 'store' AS type FROM whatsapp_stores WHERE url_alias = ? AND status = 1 LIMIT 1");
    $stmt->execute([$alias]);
    $storeMatch = $stmt->fetch();

    if ($storeMatch) {
        // It's a Store - load store.php
        $_GET['alias'] = $alias;
        require __DIR__ . '/store.php';
        exit;
    }

    // Not found
    http_response_code(404);
    header('Content-Type: text/html; charset=utf-8');
    die('<!DOCTYPE html><html><head><title>Page Not Found</title><style>body{font-family:sans-serif;text-align:center;padding:50px;background:linear-gradient(135deg,#f5f7fa,#c3cfe2);min-height:100vh;margin:0;display:flex;align-items:center;justify-content:center}.box{background:white;padding:50px;border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,0.1);max-width:400px}h1{color:#8338ec;font-size:5rem;margin:0}p{color:#6b7280;margin:15px 0 25px}a{background:linear-gradient(135deg,#8338ec,#a855f7);color:white;padding:12px 30px;border-radius:50px;text-decoration:none;font-weight:600}</style></head><body><div class="box"><h1>404</h1><p>The page "' . htmlspecialchars($alias) . '" was not found.</p><a href="/">Go Home</a></div></body></html>');

} catch (Exception $e) {
    http_response_code(500);
    die('Error: ' . htmlspecialchars($e->getMessage()));
}
