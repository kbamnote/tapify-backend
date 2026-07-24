<?php
/**
 * POST /api/sites/order-submit.php   (PUBLIC)
 *
 * Receives an order placed from a PUBLISHED builder site's product/service
 * detail page (Buy Now).
 *
 * The sender is an anonymous visitor, so everything is treated as hostile:
 *   - the site must exist and be published
 *   - name + phone are required and length-capped
 *   - a per-IP hourly rate limit keeps out casual bots
 *
 * Answers JSON (the detail page posts with fetch).
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../builder/lib/SiteRepo.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$in   = getInput();
$slug = strtolower(trim((string)($in['site'] ?? '')));
$name = trim((string)($in['name'] ?? ''));
$phone = trim((string)($in['phone'] ?? ''));

if ($slug === '')  sendError('This shop is not configured correctly.');
if ($name === '')  sendError('Please enter your name.');
if ($phone === '') sendError('Please enter your contact number.');

try {
    $site = SiteRepo::findBySlug($slug);
    if (!$site || ($site['status'] ?? '') === 'disabled') sendError('This website is not available.', 404);
    $published = SiteRepo::getPublished($site);
    if (!$published) sendError('This website is not published yet.', 400);

    $db = getDB();

    // --- rate limit: per IP, per site, per hour ---
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    if ($ip !== '') {
        $st = $db->prepare(
            "SELECT COUNT(*) FROM site_orders
             WHERE site_id = ? AND ip_address = ? AND created_at > (NOW() - INTERVAL 1 HOUR)"
        );
        $st->execute([(int)$site['id'], $ip]);
        if ((int)$st->fetchColumn() >= 15) sendError('Too many orders from this device. Please try again later.', 429);
    }

    $cut = function ($v, $n) { return mb_substr(trim((string)$v), 0, $n); };

    // If a signed-in customer placed the order, tie it to their account email
    // (authoritative) so it shows in their order history — even if they typed a
    // different email in the form.
    $email = trim((string)($in['email'] ?? ''));
    $ctoken = trim((string)($in['customer_token'] ?? ''));
    if ($ctoken !== '' && $db->query("SHOW TABLES LIKE 'site_customers'")->fetchColumn()) {
        $cs = $db->prepare("SELECT email FROM site_customers WHERE site_id = ? AND token = ? LIMIT 1");
        $cs->execute([(int)$site['id'], $ctoken]);
        $ce = $cs->fetchColumn();
        if ($ce) $email = $ce;
    }

    $st = $db->prepare(
        "INSERT INTO site_orders
           (site_id, item_title, item_slug, price, mrp, option_label, option_value,
            quantity, customer_name, customer_phone, customer_email, note, ip_address)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)"
    );
    $st->execute([
        (int)$site['id'],
        $cut($in['item'] ?? '', 200),
        $cut($in['item_slug'] ?? '', 120),
        $cut($in['price'] ?? '', 40),
        $cut($in['mrp'] ?? '', 40),
        $cut($in['option_label'] ?? '', 60),
        $cut($in['option_value'] ?? '', 120),
        max(1, (int)($in['quantity'] ?? 1)),
        $cut($name, 120),
        $cut($phone, 40),
        $cut($email, 190),
        $cut($in['note'] ?? '', 2000),
        $ip,
    ]);

    $orderId = (int)$db->lastInsertId();

    // === WhatsApp (silent failure) — order confirmation + business alert ===
    // NOTE: these two templates ('order_confirmation', 'new_order_alert') must be
    // created + approved on the WhatsApp Business account. Until then the send
    // just logs and returns false — the order is unaffected.
    try {
        require_once __DIR__ . '/../../includes/whatsapp-helper.php';
        $item = trim((string)($in['item'] ?? 'your order')) ?: 'your order';

        // 1) confirmation to the customer (if they left a phone)
        if ($phone !== '') {
            sendWhatsAppTemplate($phone, 'order_confirmation', [$name, '#' . $orderId, $item]);
        }
        // 2) new-order alert to the site's own business number
        $biz = $published['doc']['business'] ?? [];
        $bizPhone = trim((string)($biz['whatsapp'] ?? $biz['phone'] ?? ''));
        if ($bizPhone !== '') {
            sendWhatsAppTemplate($bizPhone, 'new_order_alert', [$name, ($phone !== '' ? $phone : 'Not provided'), $item]);
        }
    } catch (Exception $e) {
        error_log('site order WhatsApp failed: ' . $e->getMessage());
    }

    sendSuccess('Order placed', ['id' => $orderId]);
} catch (Exception $e) {
    error_log('order-submit: ' . $e->getMessage());
    sendError('Could not place the order right now.', 500);
}
