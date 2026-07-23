<?php
/**
 * GET /api/sites/reviews.php?site=<slug>[&item=<item-slug>]   (PUBLIC)
 *
 * The approved reviews shown on a product/service detail page. Public because
 * the published site renders them to anonymous visitors.
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../builder/lib/SiteRepo.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'GET') sendError('Only GET allowed', 405);

$slug = strtolower(trim((string)($_GET['site'] ?? '')));
$item = trim((string)($_GET['item'] ?? ''));
if ($slug === '') sendError('site is required');

try {
    $site = SiteRepo::findBySlug($slug);
    if (!$site) sendSuccess('OK', []);   // unknown site -> just no reviews

    $db = getDB();
    if ($item !== '') {
        $st = $db->prepare(
            "SELECT name, rating, comment, created_at
               FROM site_reviews
              WHERE site_id = ? AND item_slug = ? AND approved = 1
              ORDER BY created_at DESC LIMIT 50"
        );
        $st->execute([(int)$site['id'], $item]);
    } else {
        $st = $db->prepare(
            "SELECT name, rating, comment, created_at
               FROM site_reviews
              WHERE site_id = ? AND approved = 1
              ORDER BY created_at DESC LIMIT 50"
        );
        $st->execute([(int)$site['id']]);
    }

    $rows = array_map(function ($r) {
        return [
            'name'    => $r['name'],
            'rating'  => (int)$r['rating'],
            'comment' => $r['comment'],
            'date'    => $r['created_at'],
        ];
    }, $st->fetchAll(PDO::FETCH_ASSOC));

    sendSuccess('OK', $rows);
} catch (Exception $e) {
    error_log('reviews: ' . $e->getMessage());
    sendSuccess('OK', []);
}
