<?php
/**
 * POST /api/sites/review-submit.php   (PUBLIC)
 *
 * A visitor leaves a review on a published builder site's product/service page.
 * Same hostile-input posture as order-submit: the site must be published, the
 * fields are capped, and there is a per-IP hourly limit.
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../builder/lib/SiteRepo.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$in      = getInput();
$slug    = strtolower(trim((string)($in['site'] ?? '')));
$name    = trim((string)($in['name'] ?? ''));
$comment = trim((string)($in['comment'] ?? ''));
$rating  = (int)($in['rating'] ?? 5);
if ($rating < 1 || $rating > 5) $rating = 5;

if ($slug === '')    sendError('This page is not configured correctly.');
if ($name === '')    sendError('Please enter your name.');
if ($comment === '') sendError('Please write your review.');

try {
    $site = SiteRepo::findBySlug($slug);
    if (!$site || ($site['status'] ?? '') === 'disabled') sendError('This website is not available.', 404);
    if (!SiteRepo::getPublished($site)) sendError('This website is not published yet.', 400);

    $db = getDB();
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    if ($ip !== '') {
        $st = $db->prepare(
            "SELECT COUNT(*) FROM site_reviews
             WHERE site_id = ? AND ip_address = ? AND created_at > (NOW() - INTERVAL 1 HOUR)"
        );
        $st->execute([(int)$site['id'], $ip]);
        if ((int)$st->fetchColumn() >= 5) sendError('Too many reviews from this device. Please try again later.', 429);
    }

    $st = $db->prepare(
        "INSERT INTO site_reviews (site_id, item_slug, name, rating, comment, ip_address)
         VALUES (?,?,?,?,?,?)"
    );
    $st->execute([
        (int)$site['id'],
        mb_substr(trim((string)($in['item_slug'] ?? '')), 0, 120),
        mb_substr($name, 0, 120),
        $rating,
        mb_substr($comment, 0, 2000),
        $ip,
    ]);

    sendSuccess('Review posted');
} catch (Exception $e) {
    error_log('review-submit: ' . $e->getMessage());
    sendError('Could not post the review right now.', 500);
}
