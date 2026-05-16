<?php
/**
 * TAPIFY - Public vCard View API
 * GET /backend/api/vcards/view.php?alias=dentist
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

// No requireAuth() here because this is public

$alias = $_GET['alias'] ?? '';
if (!$alias) {
    sendError('Alias is required');
}

try {
    $pdo = getDB();

    // Get vCard by alias
    $stmt = $pdo->prepare("SELECT * FROM vcards WHERE url_alias = ? AND status = 1 LIMIT 1");
    $stmt->execute([$alias]);
    $vcard = $stmt->fetch();

    if (!$vcard) {
        sendError('vCard not found', 404);
    }

    $vcardId = $vcard['id'];

    // Update view count
    $pdo->prepare("UPDATE vcards SET view_count = view_count + 1 WHERE id = ?")->execute([$vcardId]);

    // Get business hours
    $stmt = $pdo->prepare("SELECT * FROM vcard_business_hours WHERE vcard_id = ? ORDER BY FIELD(day_name, 'MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY')");
    $stmt->execute([$vcardId]);
    $vcard['business_hours'] = $stmt->fetchAll();

    // Get services
    $stmt = $pdo->prepare("SELECT * FROM vcard_services WHERE vcard_id = ? ORDER BY display_order, id");
    $stmt->execute([$vcardId]);
    $vcard['services'] = $stmt->fetchAll();

    // Get products
    $stmt = $pdo->prepare("SELECT * FROM vcard_products WHERE vcard_id = ? ORDER BY display_order, id");
    $stmt->execute([$vcardId]);
    $vcard['products'] = $stmt->fetchAll();

    // Get social links
    $stmt = $pdo->prepare("SELECT * FROM vcard_social_links WHERE vcard_id = ? ORDER BY display_order, id");
    $stmt->execute([$vcardId]);
    $vcard['social_links'] = $stmt->fetchAll();

    // Get testimonials
    $vcard['testimonials'] = [];
    try {
        $stmt = $pdo->prepare("SELECT * FROM vcard_testimonials WHERE vcard_id = ? ORDER BY id DESC");
        $stmt->execute([$vcardId]);
        $vcard['testimonials'] = $stmt->fetchAll();
    } catch (Exception $e) {}

    // Get blogs
    $vcard['blogs'] = [];
    try {
        $stmt = $pdo->prepare("SELECT * FROM vcard_blogs WHERE vcard_id = ? ORDER BY id DESC");
        $stmt->execute([$vcardId]);
        $vcard['blogs'] = $stmt->fetchAll();
    } catch (Exception $e) {}

    // Get custom links
    $vcard['custom_links'] = [];
    try {
        $stmt = $pdo->prepare("SELECT * FROM vcard_custom_links WHERE vcard_id = ? ORDER BY id");
        $stmt->execute([$vcardId]);
        $vcard['custom_links'] = $stmt->fetchAll();
    } catch (Exception $e) {}

    // Get galleries
    $vcard['galleries'] = [];
    try {
        $stmt = $pdo->prepare("SELECT * FROM vcard_galleries WHERE vcard_id = ? ORDER BY id");
        $stmt->execute([$vcardId]);
        $vcard['galleries'] = $stmt->fetchAll();
    } catch (Exception $e) {}

    sendSuccess('vCard loaded', ['vcard' => $vcard]);

} catch (Exception $e) {
    sendError('Failed to load vCard: ' . $e->getMessage(), 500);
}
