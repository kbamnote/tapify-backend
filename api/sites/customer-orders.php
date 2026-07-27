<?php
/**
 * POST /api/sites/customer-orders.php   (PUBLIC)   { site, token }
 *
 * A signed-in customer's own order history on a published builder site. The
 * token identifies the customer (from site_customers); orders are matched by
 * that customer's email. Never exposes anyone else's orders.
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../builder/lib/SiteRepo.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$in    = getInput();
$slug  = strtolower(trim((string)($in['site'] ?? '')));
$token = trim((string)($in['token'] ?? ''));
if ($slug === '') sendError('This form is not configured correctly.');
if ($token === '') sendError('Not signed in', 401);

try {
    $site = SiteRepo::findBySlug($slug);
    if (!$site) sendError('This website is not available.', 404);
    $siteId = (int)$site['id'];
    $db = getDB();

    if (!$db->query("SHOW TABLES LIKE 'site_customers'")->fetchColumn()) sendError('Not signed in', 401);

    $cs = $db->prepare("SELECT email FROM site_customers WHERE site_id = ? AND token = ? LIMIT 1");
    $cs->execute([$siteId, $token]);
    $email = $cs->fetchColumn();
    if (!$email) sendError('Session expired. Please sign in again.', 401);

    if (!$db->query("SHOW TABLES LIKE 'site_orders'")->fetchColumn()) {
        sendSuccess('OK', ['orders' => []]);
    }

    $tail = "FROM site_orders WHERE site_id = ? AND customer_email = ? ORDER BY created_at DESC LIMIT 200";
    try {
        $st = $db->prepare("SELECT id, item_title, item_slug, item_image, price, mrp, option_label, option_value, quantity, status, note, created_at $tail");
        $st->execute([$siteId, $email]);
        $orders = $st->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $eCol) {
        // item_image column not migrated yet.
        $st = $db->prepare("SELECT id, item_title, item_slug, price, mrp, option_label, option_value, quantity, status, note, created_at $tail");
        $st->execute([$siteId, $email]);
        $orders = $st->fetchAll(PDO::FETCH_ASSOC);
    }
    // Fill in any missing product images by matching the order to the site's
    // CURRENT published content (so images show even for orders placed before the
    // image was captured at checkout, or if the product had none stored on it).
    $missing = false;
    foreach ($orders as $o) { if (empty($o['item_image'])) { $missing = true; break; } }
    if ($missing) {
        $pub = SiteRepo::getPublished($site);
        $doc = is_array($pub) ? ($pub['doc'] ?? null) : null;
        if (is_array($doc)) {
            $bySlug = [];
            $byTitle = [];
            collectProductImages($doc, $bySlug, $byTitle);
            foreach ($orders as &$o) {
                if (!empty($o['item_image'])) continue;
                $slugKey  = trim((string)($o['item_slug'] ?? ''));
                $titleKey = trim((string)($o['item_title'] ?? ''));
                $ref = ($slugKey !== '' && isset($bySlug[$slugKey])) ? $bySlug[$slugKey]
                     : (($titleKey !== '' && isset($byTitle[$titleKey])) ? $byTitle[$titleKey] : '');
                $o['item_image'] = resolveMediaRef($ref);
            }
            unset($o);
        }
    }

    sendSuccess('OK', ['orders' => $orders]);
} catch (Exception $e) {
    error_log('customer-orders: ' . $e->getMessage());
    sendError('Could not load your orders.', 500);
}

/** Resolve a stored media value ("media:<id>" or URL/path) to a servable URL. */
function resolveMediaRef($ref): string
{
    if (!is_string($ref) || $ref === '') return '';
    if (preg_match('#^(https?://|/)#i', $ref)) return $ref;
    if (preg_match('/^media:(\d+)$/', $ref, $m)) {
        return (defined('SITE_URL') ? SITE_URL : 'https://app.tapify.co.in') . '/api/sites/media.php?id=' . $m[1];
    }
    return '';
}

/** Walk the doc for any product/service item (has a title + image) and index it
 *  by a title-slug and by title, so orders can be matched back to their image. */
function collectProductImages($node, array &$bySlug, array &$byTitle): void
{
    if (!is_array($node)) return;
    $title = $node['title'] ?? null;
    $image = $node['image'] ?? null;
    if (is_string($title) && $title !== '' && is_string($image) && $image !== '') {
        $t = trim($title);
        $byTitle[$t] = $image;
        // Mirror SiteRenderer::itemSlug — an explicit slug wins, else the title,
        // normalised to a-z0-9 + hyphens.
        $raw = (isset($node['slug']) && is_string($node['slug']) && trim($node['slug']) !== '') ? $node['slug'] : $t;
        $slug = trim(preg_replace('/[^a-z0-9]+/', '-', strtolower(trim($raw))), '-');
        if ($slug !== '') $bySlug[$slug] = $image;
    }
    foreach ($node as $v) { if (is_array($v)) collectProductImages($v, $bySlug, $byTitle); }
}
