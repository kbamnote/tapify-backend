<?php
/**
 * TAPIFY - Public tap tracker.
 *
 * Records the taps on a customer's card, website or store that never reach the
 * server on their own: Call, WhatsApp, Email, Save Contact, Directions, Share,
 * a social icon, an outside link. Everything else (inquiries, appointments,
 * orders) is already recorded where it is handled.
 *
 * Called by the snippet injected into every public page, through
 * navigator.sendBeacon — so it must accept a text/plain body and answer with
 * nothing at all. Nobody is waiting for this response and no caller ever reads
 * it: a bad request is a silent 204 like any other.
 *
 * The owner is looked up from the asset, never taken from the request.
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/engagement/Engagement.php';

http_response_code(204);
header('Content-Type: text/plain');
header('Cache-Control: no-store');

// The beacon is sent from the public card/site, which may sit on a custom
// domain or a subdomain, so the browser treats it as cross-origin.
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin !== '') {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Vary: Origin');
}
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    exit;
}
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    exit;
}

/** Asset type => [table, id column, owner column]. */
const TAP_ASSETS = [
    'card'  => ['vcards', 'id', 'user_id'],
    'site'  => ['sites', 'id', 'user_id'],
    'store' => ['whatsapp_stores', 'id', 'user_id'],
];

const TAP_EVENTS = [
    'tap_call', 'tap_whatsapp', 'tap_email', 'tap_save_contact', 'tap_directions',
    'tap_share', 'tap_social', 'tap_link', 'tap_qr_download', 'tap_book', 'tap_order', 'tap_pay',
];

try {
    $raw = file_get_contents('php://input');
    $body = is_string($raw) && $raw !== '' ? json_decode($raw, true) : null;
    if (!is_array($body)) {
        exit;
    }

    $type = (string)($body['t'] ?? '');
    $assetId = (int)($body['id'] ?? 0);
    $event = (string)($body['e'] ?? '');
    if (!isset(TAP_ASSETS[$type]) || $assetId <= 0 || !in_array($event, TAP_EVENTS, true)) {
        exit;
    }

    [$table, $idCol, $ownerCol] = TAP_ASSETS[$type];
    $pdo = getDB();
    $st = $pdo->prepare("SELECT $ownerCol FROM $table WHERE $idCol = ? LIMIT 1");
    $st->execute([$assetId]);
    $userId = (int)$st->fetchColumn();
    if ($userId <= 0) {
        exit;
    }

    // `s` is the source the page itself was opened with (nfc / qr / …), passed
    // on so a tap is attributed the same way as the view that led to it.
    $source = (string)($body['s'] ?? '');
    Engagement::record($userId, $type, $assetId, $event, [
        'label'  => (string)($body['l'] ?? ''),
        'dedupe' => false,
    ] + (in_array($source, Engagement::SOURCES, true) ? ['source' => $source] : []));
} catch (Throwable $e) {
    error_log('tap.php: ' . $e->getMessage());
}
