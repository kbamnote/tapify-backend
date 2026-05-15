<?php
/**
 * TAPIFY - Get vCard details API
 * GET /backend/api/vcards/get.php?id=123
 * Returns single vCard with all fields
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAuth();

$vcardId = (int)($_GET['id'] ?? 0);
if (!$vcardId) {
    sendError('vCard ID is required');
}

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    // Get vCard (only if belongs to current user)
    $stmt = $pdo->prepare("SELECT * FROM vcards WHERE id = ? AND user_id = ? LIMIT 1");
    $stmt->execute([$vcardId, $userId]);
    $vcard = $stmt->fetch();

    if (!$vcard) {
        sendError('vCard not found or access denied', 404);
    }

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

    // Convert booleans
    $boolFields = ['display_inquiry_form', 'display_qr_section', 'display_download_qr', 'display_add_contact',
                    'display_whatsapp_share', 'display_language_selector', 'hide_sticky_bar',
                    'qr_use_config', 'banner_show', 'remove_branding',
                    'show_contact', 'show_services', 'show_galleries', 'show_products',
                    'show_testimonials', 'show_blogs', 'show_business_hours', 'show_appointments',
                    'show_map', 'show_banner', 'show_instagram', 'show_iframes', 'show_newsletter', 'status'];

    foreach ($boolFields as $field) {
        if (isset($vcard[$field])) {
            $vcard[$field] = (bool)$vcard[$field];
        }
    }

    // Generate preview URL
    $vcard['preview_url'] = SITE_URL . '/' . $vcard['url_alias'];

    sendSuccess('vCard loaded', ['vcard' => $vcard]);

} catch (Exception $e) {
    sendError('Failed to load vCard: ' . $e->getMessage(), 500);
}
