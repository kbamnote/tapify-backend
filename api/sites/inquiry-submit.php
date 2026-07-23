<?php
/**
 * POST /api/sites/inquiry-submit.php   (PUBLIC)
 *
 * An enquiry sent from a PUBLISHED builder site's Contact section (the built-in
 * enquiry form). Stored in site_inquiries and shown on the dashboard's
 * "Website Inquiries" page.
 *
 * Anonymous visitor input: the site must be published, fields are capped, and
 * there is a per-IP hourly rate limit.
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../builder/lib/SiteRepo.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$in      = getInput();
$slug    = strtolower(trim((string)($in['site'] ?? '')));
$name    = trim((string)($in['name'] ?? ''));
$phone   = trim((string)($in['phone'] ?? ''));
$message = trim((string)($in['message'] ?? ''));

if ($slug === '')    sendError('This form is not configured correctly.');
if ($name === '')    sendError('Please enter your name.');
if ($phone === '')   sendError('Please enter your phone number.');
if ($message === '') sendError('Please write your message.');

try {
    $site = SiteRepo::findBySlug($slug);
    if (!$site || ($site['status'] ?? '') === 'disabled') sendError('This website is not available.', 404);
    if (!SiteRepo::getPublished($site)) sendError('This website is not published yet.', 400);

    $db = getDB();

    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    if ($ip !== '') {
        $st = $db->prepare(
            "SELECT COUNT(*) FROM site_inquiries
              WHERE site_id = ? AND ip_address = ? AND created_at > (NOW() - INTERVAL 1 HOUR)"
        );
        $st->execute([(int)$site['id'], $ip]);
        if ((int)$st->fetchColumn() >= 10) sendError('Too many messages from this device. Please try again later.', 429);
    }

    $st = $db->prepare(
        "INSERT INTO site_inquiries (site_id, name, email, phone, subject, message, page_url, ip_address)
         VALUES (?,?,?,?,?,?,?,?)"
    );
    $st->execute([
        (int)$site['id'],
        mb_substr($name, 0, 150),
        mb_substr(trim((string)($in['email'] ?? '')), 0, 190),
        mb_substr($phone, 0, 40),
        mb_substr(trim((string)($in['subject'] ?? '')), 0, 200),
        mb_substr($message, 0, 5000),
        mb_substr((string)($_SERVER['HTTP_REFERER'] ?? ''), 0, 600),
        $ip,
    ]);

    sendSuccess('Enquiry sent');
} catch (Exception $e) {
    error_log('site inquiry-submit: ' . $e->getMessage());
    sendError('Could not send your message right now.', 500);
}
