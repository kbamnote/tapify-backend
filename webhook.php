<?php
/**
 * TAPIFY - WhatsApp Cloud API webhook
 * --------------------------------------------------------------------------
 * GET  /webhook  -> Meta verification handshake (hub.mode / hub.verify_token /
 *                   hub.challenge). Echoes the challenge back when the token
 *                   matches WHATSAPP_VERIFY_TOKEN.
 * POST /webhook  -> Receives WhatsApp webhook events, logs the raw payload,
 *                   and returns HTTP 200 (Meta only needs a fast 200 OK).
 *
 * Routed from index.php / router.php on  $alias === 'webhook'.  Self-contained:
 * it does not touch the database or any existing route.
 */

require_once __DIR__ . '/config/database.php';

/**
 * Read a raw query parameter by its EXACT name (handles dotted keys like
 * "hub.mode" that PHP would otherwise mangle into $_GET['hub_mode']).
 */
function wa_query_param(string $name): string {
    $qs = $_SERVER['QUERY_STRING'] ?? '';
    foreach (explode('&', $qs) as $pair) {
        if ($pair === '') continue;
        $kv  = explode('=', $pair, 2);
        $key = urldecode($kv[0]);
        if ($key === $name) {
            return isset($kv[1]) ? urldecode($kv[1]) : '';
        }
    }
    return '';
}

// Verify token from config/env (no hardcoded secret).
$verifyToken = defined('WHATSAPP_VERIFY_TOKEN') && WHATSAPP_VERIFY_TOKEN !== ''
    ? WHATSAPP_VERIFY_TOKEN
    : (getenv('WHATSAPP_VERIFY_TOKEN') ?: '');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// ---------------------------------------------------------------------------
// 1) GET — Meta verification handshake
// ---------------------------------------------------------------------------
if ($method === 'GET') {
    $mode      = wa_query_param('hub.mode');
    $token     = wa_query_param('hub.verify_token');
    $challenge = wa_query_param('hub.challenge');

    header('Content-Type: text/plain; charset=utf-8');

    if ($mode === 'subscribe' && $verifyToken !== '' && hash_equals($verifyToken, $token)) {
        http_response_code(200);
        echo $challenge;                 // Meta expects the raw challenge echoed back
    } else {
        http_response_code(403);
        echo 'Forbidden';
    }
    exit;
}

// ---------------------------------------------------------------------------
// 2) POST — receive events, log, ack 200
// ---------------------------------------------------------------------------
if ($method === 'POST') {
    $raw = file_get_contents('php://input');

    // Primary log -> platform log stream (visible in Railway "Deploy Logs").
    error_log('[whatsapp-webhook] ' . $raw);

    // Best-effort persistent copy in the system temp dir (NOT under the web
    // root, so the payload — which can contain phone numbers — is not served).
    @file_put_contents(
        rtrim(sys_get_temp_dir(), '/\\') . DIRECTORY_SEPARATOR . 'whatsapp-webhook.log',
        '[' . date('c') . '] ' . $raw . "\n",
        FILE_APPEND | LOCK_EX
    );

    // (Optional hardening, not enabled here: verify the X-Hub-Signature-256
    //  header against an app secret before trusting the payload.)

    http_response_code(200);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'EVENT_RECEIVED';
    exit;
}

// ---------------------------------------------------------------------------
// Any other method
// ---------------------------------------------------------------------------
http_response_code(405);
header('Allow: GET, POST');
header('Content-Type: text/plain; charset=utf-8');
echo 'Method Not Allowed';
exit;
