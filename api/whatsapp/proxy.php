<?php
/**
 * Customer → CRM WhatsApp bridge.
 *
 *   GET  /api/whatsapp/proxy.php?action=status
 *   GET  /api/whatsapp/proxy.php?action=conversations
 *   GET  /api/whatsapp/proxy.php?action=thread&phone=9198...
 *   GET  /api/whatsapp/proxy.php?action=templates
 *   POST /api/whatsapp/proxy.php?action=send      { phone, name?, templateName?, params?, body? }
 *
 * The inbox, threads and sending all live in the Node CRM. Rather than exposing
 * that service to customer browsers (CORS, a second login, a public surface), it
 * stays private and this endpoint forwards on the customer's behalf:
 *
 *   - the customer is authenticated HERE, by the normal PHP session
 *   - their users.id is asserted to the CRM via X-Tapify-User
 *   - CRM_SERVICE_KEY proves the caller is this backend
 *
 * ACTIONS ARE WHITELISTED. Forwarding an arbitrary caller-supplied path would
 * turn this into an open proxy into the CRM, reachable by any logged-in
 * customer — so the map below is the only thing that can be reached, and the
 * customer's id is never taken from the request.
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if (!isLoggedIn()) sendError('Not logged in', 401);

if (CRM_SERVICE_KEY === '' || CRM_SERVICE_URL === '') {
    sendError('WhatsApp is not available yet. Please contact support.', 503);
}

/** action => [http method, CRM path, allowed query params] */
const ACTIONS = [
    'status'        => ['GET',  '/api/partner/whatsapp/status',        []],
    'conversations' => ['GET',  '/api/partner/whatsapp/conversations',  []],
    'thread'        => ['GET',  '/api/partner/whatsapp/thread/{phone}', ['phone']],
    'templates'     => ['GET',  '/api/partner/whatsapp/templates',      []],
    'send'          => ['POST', '/api/partner/whatsapp/send',           []],
    'bot'           => ['GET',  '/api/partner/whatsapp/bot',            []],
    'bot-save'      => ['POST', '/api/partner/whatsapp/bot',            []],
    // Broadcast returns 202 immediately and runs in the background — it must
    // not be made synchronous, or the 15s timeout below would abandon the
    // caller mid-send with no way to tell what actually went out.
    'broadcast'     => ['POST', '/api/partner/whatsapp/broadcast',      []],
    'broadcasts'    => ['GET',  '/api/partner/whatsapp/broadcasts',     []],
];

$action = trim((string)($_GET['action'] ?? ''));
if (!isset(ACTIONS[$action])) sendError('Unknown action', 400);

[$method, $path, $allowedQuery] = ACTIONS[$action];
if ($_SERVER['REQUEST_METHOD'] !== $method) sendError('Method not allowed for this action', 405);

// Path parameters are substituted, never concatenated from raw input, so a
// value like "../../users" cannot escape the whitelisted route.
if (in_array('phone', $allowedQuery, true)) {
    $phone = preg_replace('/\D/', '', (string)($_GET['phone'] ?? ''));
    if ($phone === '') sendError('phone is required', 400);
    $path = str_replace('{phone}', rawurlencode($phone), $path);
}

$url  = rtrim(CRM_SERVICE_URL, '/') . $path;
$body = $method === 'POST' ? json_encode(getInput() ?: []) : null;

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST  => $method,
    CURLOPT_TIMEOUT        => 15,
    CURLOPT_CONNECTTIMEOUT => 5,
    CURLOPT_HTTPHEADER     => array_filter([
        'Content-Type: application/json',
        'Accept: application/json',
        'X-Service-Key: ' . CRM_SERVICE_KEY,
        'X-Tapify-User: ' . (int)getCurrentUserId(),
    ]),
]);
if ($body !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

$raw    = curl_exec($ch);
$status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err    = curl_error($ch);
curl_close($ch);

if ($err !== '') {
    error_log('whatsapp proxy: ' . $err);
    sendError('Could not reach the WhatsApp service. Please try again.', 502);
}

// Pass the CRM's JSON straight through, preserving its status code so the UI can
// act on 409 not_connected / 409 window_closed rather than a generic failure.
http_response_code($status ?: 502);
header('Content-Type: application/json');
echo $raw !== false && $raw !== '' ? $raw : json_encode(['success' => false, 'message' => 'Empty response']);
