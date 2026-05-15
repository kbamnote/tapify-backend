<?php
/**
 * TAPIFY - Update vCard API
 * POST /backend/api/vcards/update.php
 * Body: { id, ...fields to update }
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Only POST method allowed', 405);
}

$input = getInput();
$vcardId = (int)($input['id'] ?? 0);

if (!$vcardId) {
    sendError('vCard ID is required');
}

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    // Verify ownership (Admins can update any card)
    if (isAdmin()) {
        $stmt = $pdo->prepare("SELECT id, url_alias FROM vcards WHERE id = ? LIMIT 1");
        $stmt->execute([$vcardId]);
    } else {
        $stmt = $pdo->prepare("SELECT id, url_alias FROM vcards WHERE id = ? AND user_id = ? LIMIT 1");
        $stmt->execute([$vcardId, $userId]);
    }
    $existing = $stmt->fetch();

    if (!$existing) {
        sendError('vCard not found or access denied', 404);
    }

    // Whitelist of allowed fields to update
    $allowedFields = [
        'url_alias', 'vcard_name', 'occupation', 'description', 'cover_type', 'cover_color',
        'template_id', 'first_name', 'last_name', 'email', 'phone', 'phone_country_code',
        'alternate_email', 'alternate_phone', 'location', 'location_url', 'location_type',
        'dob', 'company', 'job_title', 'made_by', 'made_by_url', 'default_language',
        'display_inquiry_form', 'display_qr_section', 'display_download_qr', 'display_add_contact',
        'display_whatsapp_share', 'display_language_selector', 'hide_sticky_bar', 'qr_download_size',
        'primary_color', 'secondary_color', 'bg_color', 'cards_bg_color', 'button_text_color',
        'label_text_color', 'description_text_color', 'social_icon_color', 'button_style', 'sticky_position',
        'qr_color', 'qr_bg_color', 'qr_style', 'qr_eye_style', 'qr_use_config',
        'banner_title', 'banner_url', 'banner_description', 'banner_button_text', 'banner_show',
        'custom_css', 'custom_js', 'remove_branding', 'font_family', 'font_size',
        'seo_site_title', 'seo_home_title', 'seo_meta_keyword', 'seo_meta_description', 'google_analytics',
        'privacy_policy', 'terms_conditions',
        'show_contact', 'show_services', 'show_galleries', 'show_products', 'show_testimonials',
        'show_blogs', 'show_business_hours', 'show_appointments', 'show_map', 'show_banner',
        'show_instagram', 'show_iframes', 'show_newsletter', 'status'
    ];

    $updateFields = [];
    $params = ['id' => $vcardId];

    // Special handling for url_alias (must be unique)
    if (isset($input['url_alias'])) {
        $newAlias = generateSlug($input['url_alias']);
        if ($newAlias !== $existing['url_alias']) {
            if (!isUrlAliasUnique($newAlias, $vcardId)) {
                sendError('This URL is already taken. Please choose another.', 409);
            }
        }
        $input['url_alias'] = $newAlias;
    }

    // Build update query
    foreach ($allowedFields as $field) {
        if (array_key_exists($field, $input)) {
            $updateFields[] = "`$field` = :$field";
            // Boolean handling
            if (in_array($field, ['display_inquiry_form', 'display_qr_section', 'display_download_qr',
                'display_add_contact', 'display_whatsapp_share', 'display_language_selector',
                'hide_sticky_bar', 'qr_use_config', 'banner_show', 'remove_branding',
                'show_contact', 'show_services', 'show_galleries', 'show_products', 'show_testimonials',
                'show_blogs', 'show_business_hours', 'show_appointments', 'show_map', 'show_banner',
                'show_instagram', 'show_iframes', 'show_newsletter', 'status'])) {
                $params[$field] = $input[$field] ? 1 : 0;
            } else {
                $params[$field] = $input[$field];
            }
        }
    }

    if (empty($updateFields)) {
        sendError('No fields to update');
    }

    $sql = "UPDATE vcards SET " . implode(', ', $updateFields) . " WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // Update business hours if provided
    if (isset($input['business_hours']) && is_array($input['business_hours'])) {
        $pdo->prepare("DELETE FROM vcard_business_hours WHERE vcard_id = ?")->execute([$vcardId]);
        $bhStmt = $pdo->prepare("INSERT INTO vcard_business_hours (vcard_id, day_name, is_open, open_time, close_time) VALUES (?, ?, ?, ?, ?)");
        foreach ($input['business_hours'] as $bh) {
            $bhStmt->execute([
                $vcardId,
                $bh['day_name'],
                $bh['is_open'] ? 1 : 0,
                $bh['open_time'] ?? '10:00 AM',
                $bh['close_time'] ?? '06:00 PM'
            ]);
        }
    }

    // Update social links if provided
    if (isset($input['social_links']) && is_array($input['social_links'])) {
        $pdo->prepare("DELETE FROM vcard_social_links WHERE vcard_id = ?")->execute([$vcardId]);
        $slStmt = $pdo->prepare("INSERT INTO vcard_social_links (vcard_id, platform, url, display_order) VALUES (?, ?, ?, ?)");
        foreach ($input['social_links'] as $i => $sl) {
            if (!empty($sl['url'])) {
                $slStmt->execute([$vcardId, $sl['platform'], $sl['url'], $i]);
            }
        }
    }

    sendSuccess('vCard updated successfully!', ['vcard_id' => $vcardId]);

} catch (Exception $e) {
    sendError('Failed to update vCard: ' . $e->getMessage(), 500);
}
