<?php
/**
 * TAPIFY - Template Loader
 * Loads vCard data and routes to correct template based on template_id
 */

require_once __DIR__ . '/config/database.php';

$alias = trim($_GET['alias'] ?? '');
if (empty($alias)) {
    header('Location: /');
    exit;
}

try {
    $pdo = getDB();

    $stmt = $pdo->prepare("SELECT * FROM vcards WHERE url_alias = ? AND status = 1 LIMIT 1");
    $stmt->execute([$alias]);
    $vcard = $stmt->fetch();

    if (!$vcard) {
        http_response_code(404);
        die('<!DOCTYPE html><html><head><title>vCard Not Found</title><style>body{font-family:sans-serif;text-align:center;padding:50px;background:linear-gradient(135deg,#f5f7fa,#c3cfe2);min-height:100vh;margin:0;display:flex;align-items:center;justify-content:center}.box{background:white;padding:50px;border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,0.1);max-width:400px}h1{color:#8338ec;font-size:5rem;margin:0}p{color:#6b7280;margin:15px 0 25px}a{background:linear-gradient(135deg,#8338ec,#a855f7);color:white;padding:12px 30px;border-radius:50px;text-decoration:none;font-weight:600}</style></head><body><div class="box"><h1>404</h1><p>vCard not found.</p><a href="/">Go Home</a></div></body></html>');
    }

    $vcardId = $vcard['id'];

    $pdo->prepare("UPDATE vcards SET view_count = view_count + 1 WHERE id = ?")->execute([$vcardId]);

    $stmt = $pdo->prepare("SELECT * FROM vcard_business_hours WHERE vcard_id = ? ORDER BY FIELD(day_name, 'MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY')");
    $stmt->execute([$vcardId]);
    $businessHours = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT * FROM vcard_services WHERE vcard_id = ? ORDER BY display_order, id");
    $stmt->execute([$vcardId]);
    $services = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT * FROM vcard_products WHERE vcard_id = ? ORDER BY display_order, id");
    $stmt->execute([$vcardId]);
    $products = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT * FROM vcard_social_links WHERE vcard_id = ? ORDER BY display_order, id");
    $stmt->execute([$vcardId]);
    $socialLinks = $stmt->fetchAll();

    $customLinks = $blogs = $testimonials = $galleries = [];
    try {
        $stmt = $pdo->prepare("SELECT * FROM vcard_custom_links WHERE vcard_id = ? ORDER BY display_order, id");
        $stmt->execute([$vcardId]);
        $customLinks = $stmt->fetchAll();
    } catch (Exception $e) {}

    try {
        $stmt = $pdo->prepare("SELECT * FROM vcard_blogs WHERE vcard_id = ? ORDER BY display_order, id DESC");
        $stmt->execute([$vcardId]);
        $blogs = $stmt->fetchAll();
    } catch (Exception $e) {}

    try {
        $stmt = $pdo->prepare("SELECT * FROM vcard_testimonials WHERE vcard_id = ? ORDER BY display_order, id");
        $stmt->execute([$vcardId]);
        $testimonials = $stmt->fetchAll();
    } catch (Exception $e) {}

    try {
        $stmt = $pdo->prepare("SELECT * FROM vcard_galleries WHERE vcard_id = ? ORDER BY display_order, id");
        $stmt->execute([$vcardId]);
        $galleries = $stmt->fetchAll();
        foreach ($galleries as &$g) {
            $imgStmt = $pdo->prepare("SELECT * FROM vcard_gallery_images WHERE gallery_id = ? ORDER BY display_order, id");
            $imgStmt->execute([$g['id']]);
            $g['images'] = $imgStmt->fetchAll();
        }
    } catch (Exception $e) {}

} catch (Exception $e) {
    die('Error: ' . htmlspecialchars($e->getMessage()));
}

function imgUrl($path, $default = '') {
    return $path ? '/' . $path : $default;
}

$fullName = trim(($vcard['first_name'] ?? '') . ' ' . ($vcard['last_name'] ?? ''));
if (empty($fullName)) $fullName = $vcard['vcard_name'];

// === TEMPLATE ROUTING ===
$templateId = $vcard['template_id'] ?? 'vcard1';

// Base map for specific popular ones
$templateMap = [
    'vcard1' => 'default',
    'vcard16' => 'lawyer',
    'vcard17' => 'doctor',
    'vcard18' => 'restaurant',
    'vcard19' => 'real-estate',
];

// If not in specific map, distribute all 42 IDs across available designs
if (!isset($templateMap[$templateId])) {
    $numId = (int)str_replace('vcard', '', $templateId);
    if ($numId > 0) {
        $designs = ['default', 'lawyer', 'doctor', 'restaurant', 'real-estate'];
        $templateName = $designs[$numId % count($designs)];
    } else {
        $templateName = 'default';
    }
} else {
    $templateName = $templateMap[$templateId];
}

$templatePath = __DIR__ . "/templates/$templateName.php";

if (!file_exists($templatePath)) {
    $templatePath = __DIR__ . '/templates/default.php';
    $templateName = 'default';
}

header('Content-Type: text/html; charset=utf-8');
include $templatePath;
