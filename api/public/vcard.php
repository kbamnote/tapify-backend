<?php
/**
 * TAPIFY - Public vCard API
 * GET /backend/api/public/vcard.php?alias=tapify
 *
 * Returns all data for public vCard display
 * No authentication required - this is PUBLIC!
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

$alias = trim($_GET['alias'] ?? '');
if (empty($alias)) sendError('vCard alias required', 400);

try {
    $pdo = getDB();

    // Load vCard by URL alias (only active ones)
    $stmt = $pdo->prepare("SELECT * FROM vcards WHERE url_alias = ? AND status = 1 LIMIT 1");
    $stmt->execute([$alias]);
    $vcard = $stmt->fetch();

    if (!$vcard) {
        sendError('vCard not found', 404);
    }

    // Increment view count (non-blocking, fire and forget)
    try {
        $pdo->prepare("UPDATE vcards SET view_count = view_count + 1 WHERE id = ?")
            ->execute([$vcard['id']]);
    } catch (Exception $e) {
        // Don't break if counter fails
    }

    $vcardId = $vcard['id'];

    // Load all related data
    // Business Hours
    $stmt = $pdo->prepare("SELECT * FROM vcard_business_hours WHERE vcard_id = ? ORDER BY FIELD(day_name, 'MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY')");
    $stmt->execute([$vcardId]);
    $vcard['business_hours'] = $stmt->fetchAll();

    // Services
    $stmt = $pdo->prepare("SELECT * FROM vcard_services WHERE vcard_id = ? ORDER BY display_order, id");
    $stmt->execute([$vcardId]);
    $vcard['services'] = $stmt->fetchAll();

    // Products (with full image URL)
    $stmt = $pdo->prepare("SELECT * FROM vcard_products WHERE vcard_id = ? ORDER BY display_order, id");
    $stmt->execute([$vcardId]);
    $products = $stmt->fetchAll();
    foreach ($products as &$p) {
        $p['price'] = $p['price'] !== null ? (float)$p['price'] : null;
        if ($p['image']) $p['image_url'] = imgUrl($p['image']);
    }
    $vcard['products'] = $products;

    // Social Links
    $stmt = $pdo->prepare("SELECT * FROM vcard_social_links WHERE vcard_id = ? ORDER BY display_order, id");
    $stmt->execute([$vcardId]);
    $vcard['social_links'] = $stmt->fetchAll();

    // Custom Links - check if table exists (Phase 3C)
    try {
        $stmt = $pdo->prepare("SELECT * FROM vcard_custom_links WHERE vcard_id = ? ORDER BY display_order, id");
        $stmt->execute([$vcardId]);
        $vcard['custom_links'] = $stmt->fetchAll();
    } catch (Exception $e) {
        $vcard['custom_links'] = [];
    }

    // Blogs
    try {
        $stmt = $pdo->prepare("SELECT * FROM vcard_blogs WHERE vcard_id = ? ORDER BY display_order, id DESC");
        $stmt->execute([$vcardId]);
        $blogs = $stmt->fetchAll();
        foreach ($blogs as &$b) {
            if ($b['image']) $b['image_url'] = imgUrl($b['image']);
        }
        $vcard['blogs'] = $blogs;
    } catch (Exception $e) {
        $vcard['blogs'] = [];
    }

    // Testimonials
    try {
        $stmt = $pdo->prepare("SELECT * FROM vcard_testimonials WHERE vcard_id = ? ORDER BY display_order, id");
        $stmt->execute([$vcardId]);
        $testimonials = $stmt->fetchAll();
        foreach ($testimonials as &$t) {
            if ($t['image']) $t['image_url'] = imgUrl($t['image']);
        }
        $vcard['testimonials'] = $testimonials;
    } catch (Exception $e) {
        $vcard['testimonials'] = [];
    }

    // Galleries with images
    try {
        $stmt = $pdo->prepare("SELECT * FROM vcard_galleries WHERE vcard_id = ? ORDER BY display_order, id");
        $stmt->execute([$vcardId]);
        $galleries = $stmt->fetchAll();
        foreach ($galleries as &$g) {
            $imgStmt = $pdo->prepare("SELECT * FROM vcard_gallery_images WHERE gallery_id = ? ORDER BY display_order, id");
            $imgStmt->execute([$g['id']]);
            $images = $imgStmt->fetchAll();
            foreach ($images as &$img) {
                $img['public_url'] = imgUrl($img['image_url']);
            }
            $g['images'] = $images;
        }
        $vcard['galleries'] = $galleries;
    } catch (Exception $e) {
        $vcard['galleries'] = [];
    }

    // Convert images to full URLs
    if ($vcard['cover_image']) $vcard['cover_image_url'] = imgUrl($vcard['cover_image']);
    if ($vcard['profile_image']) $vcard['profile_image_url'] = imgUrl($vcard['profile_image']);
    if ($vcard['favicon_image']) $vcard['favicon_image_url'] = imgUrl($vcard['favicon_image']);

    // Convert booleans
    $boolFields = ['display_inquiry_form', 'display_qr_section', 'display_download_qr', 'display_add_contact',
                    'display_whatsapp_share', 'display_language_selector', 'hide_sticky_bar',
                    'show_contact', 'show_services', 'show_galleries', 'show_products',
                    'show_testimonials', 'show_blogs', 'show_business_hours', 'show_appointments',
                    'show_map', 'show_banner', 'show_instagram', 'show_iframes', 'show_newsletter'];
    foreach ($boolFields as $field) {
        if (isset($vcard[$field])) {
            $vcard[$field] = (bool)$vcard[$field];
        }
    }

    // Remove sensitive fields
    unset($vcard['password']);
    unset($vcard['user_id']);
    unset($vcard['created_at']);
    unset($vcard['updated_at']);

    sendSuccess('vCard loaded', ['vcard' => $vcard]);

} catch (Exception $e) {
    sendError('Failed to load: ' . $e->getMessage(), 500);
}
