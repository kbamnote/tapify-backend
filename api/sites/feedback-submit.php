<?php
/**
 * POST /api/sites/feedback-submit.php   (PUBLIC)
 *
 * Feedback submitted from a published builder site's Feedback section.
 *   { site, name, email?, rating (1-5), message }
 *
 * Anonymous visitor input: the site must be published, fields are capped, and a
 * per-IP hourly rate limit keeps out casual spam. Answers JSON.
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../builder/lib/SiteRepo.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$in      = getInput();
$slug    = strtolower(trim((string)($in['site'] ?? '')));
$name    = trim((string)($in['name'] ?? ''));
$email   = trim((string)($in['email'] ?? ''));
$message = trim((string)($in['message'] ?? ''));
$rating  = (int)($in['rating'] ?? 5);
if ($rating < 1 || $rating > 5) $rating = 5;

if ($slug === '')    sendError('This form is not configured correctly.');
if ($name === '')    $name = 'Anonymous';   // survey grid submits without a name
if ($message === '') sendError('Please write your feedback.');

try {
    $site = SiteRepo::findBySlug($slug);
    if (!$site || ($site['status'] ?? '') === 'disabled') sendError('This website is not available.', 404);
    $published = SiteRepo::getPublished($site);
    if (!$published) sendError('This website is not published yet.', 400);

    $db = getDB();

    // Create the table on first use so this works before the migration is run.
    if (!$db->query("SHOW TABLES LIKE 'site_feedback'")->fetchColumn()) {
        $db->exec("CREATE TABLE site_feedback (
            id INT AUTO_INCREMENT PRIMARY KEY, site_id INT NOT NULL,
            name VARCHAR(150) NOT NULL DEFAULT '', email VARCHAR(190) NOT NULL DEFAULT '',
            phone VARCHAR(40) NOT NULL DEFAULT '',
            rating TINYINT NOT NULL DEFAULT 5, message TEXT NOT NULL,
            page_url VARCHAR(600) NOT NULL DEFAULT '', is_read TINYINT(1) NOT NULL DEFAULT 0,
            ip_address VARCHAR(45) NOT NULL DEFAULT '',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            KEY idx_site_created (site_id, created_at), KEY idx_site_read (site_id, is_read)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    }

    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    if ($ip !== '') {
        $st = $db->prepare("SELECT COUNT(*) FROM site_feedback WHERE site_id = ? AND ip_address = ? AND created_at > (NOW() - INTERVAL 1 HOUR)");
        $st->execute([(int)$site['id'], $ip]);
        if ((int)$st->fetchColumn() >= 8) sendError('Too much feedback from this device. Please try again later.', 429);
    }

    $pageUrl = mb_substr((string)($_SERVER['HTTP_REFERER'] ?? ''), 0, 600);
    $phone   = mb_substr(trim((string)($in['phone'] ?? '')), 0, 40);
    $baseVals = [(int)$site['id'], mb_substr($name, 0, 150), mb_substr($email, 0, 190)];
    $tailVals = [$rating, mb_substr($message, 0, 2000), $pageUrl, $ip];
    try {
        $st = $db->prepare("INSERT INTO site_feedback (site_id, name, email, phone, rating, message, page_url, ip_address)
                            VALUES (?,?,?,?,?,?,?,?)");
        $st->execute(array_merge($baseVals, [$phone], $tailVals));
    } catch (Exception $eCol) {
        // phone column not migrated yet.
        $st = $db->prepare("INSERT INTO site_feedback (site_id, name, email, rating, message, page_url, ip_address)
                            VALUES (?,?,?,?,?,?,?)");
        $st->execute(array_merge($baseVals, $tailVals));
    }

    // WhatsApp alert to the site's own business number (reuses the approved
    // new_inquiry_alert template: name, contact, message).
    try {
        require_once __DIR__ . '/../../includes/whatsapp-helper.php';
        $biz = $published['doc']['business'] ?? [];
        $bizPhone = trim((string)($biz['whatsapp'] ?? $biz['phone'] ?? ''));
        if ($bizPhone !== '') {
            $msg = 'Rating ' . $rating . '/5 — ' . preg_replace('/\s+/', ' ', $message);
            if (mb_strlen($msg) > 300) $msg = mb_substr($msg, 0, 297) . '...';
            $contact = $phone !== '' ? $phone : ($email !== '' ? $email : 'Feedback');
            sendWhatsAppTemplate($bizPhone, 'new_inquiry_alert', [$name, $contact, $msg]);
        }
    } catch (Exception $e) {
        error_log('feedback WhatsApp failed: ' . $e->getMessage());
    }

    sendSuccess('Thank you for your feedback!', ['id' => (int)$db->lastInsertId()]);
} catch (Exception $e) {
    error_log('feedback-submit: ' . $e->getMessage());
    sendError('Could not send your feedback right now.', 500);
}
