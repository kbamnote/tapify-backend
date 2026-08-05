<?php
/**
 * WhatsApp Embedded Signup — server side.
 *
 *   GET  /api/whatsapp/connect.php                -> { app_id, config_id, graph_version }
 *   POST /api/whatsapp/connect.php                { code, waba_id, phone_number_id }
 *   POST /api/whatsapp/connect.php?disconnect=1
 *
 * The browser runs Meta's Embedded Signup popup (Facebook JS SDK), which returns
 * an authorization CODE plus the customer's waba_id / phone_number_id. Only this
 * backend holds FACEBOOK_WA_APP_SECRET (the WhatsApp Meta app's secret), so the
 * code→token exchange happens here, never in the browser. The resulting token is
 * handed to the CRM over the service bridge and stored there encrypted; it is
 * never returned to the client and never logged.
 *
 * Requires Tech Provider status and App Review for
 * whatsapp_business_management + whatsapp_business_messaging.
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if (!isLoggedIn()) sendError('Not logged in', 401);

$graphVersion = defined('FACEBOOK_GRAPH_VERSION') && FACEBOOK_GRAPH_VERSION !== ''
    ? FACEBOOK_GRAPH_VERSION : 'v20.0';

/* ---------------------------------------------- what the browser needs ---- */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    sendSuccess('OK', [
        'app_id'        => defined('FACEBOOK_WA_APP_ID') ? FACEBOOK_WA_APP_ID : '',
        'config_id'     => defined('FACEBOOK_WA_CONFIG_ID') ? FACEBOOK_WA_CONFIG_ID : '',
        'graph_version' => $graphVersion,
        // The UI hides the Connect button unless everything is configured,
        // rather than opening a popup that is certain to fail.
        'ready'         => (defined('FACEBOOK_WA_APP_ID') && FACEBOOK_WA_APP_ID !== '')
                        && (defined('FACEBOOK_WA_APP_SECRET') && FACEBOOK_WA_APP_SECRET !== '')
                        && (defined('FACEBOOK_WA_CONFIG_ID') && FACEBOOK_WA_CONFIG_ID !== '')
                        && CRM_SERVICE_KEY !== '',
    ]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Method not allowed', 405);
if (CRM_SERVICE_KEY === '') sendError('WhatsApp is not available yet. Please contact support.', 503);

/** Server-to-server call into the CRM, asserting the logged-in customer. */
function crmPost($path, array $body)
{
    $ch = curl_init(rtrim(CRM_SERVICE_URL, '/') . $path);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($body),
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Accept: application/json',
            'X-Service-Key: ' . CRM_SERVICE_KEY,
            'X-Tapify-User: ' . (int)getCurrentUserId(),
        ],
    ]);
    $raw = curl_exec($ch);
    $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);
    if ($err !== '') {
        error_log('whatsapp connect: CRM unreachable: ' . $err);
        sendError('Could not reach the WhatsApp service. Please try again.', 502);
    }
    return [$status, json_decode($raw, true) ?: []];
}

/* ------------------------------------------------------------ disconnect -- */
if (!empty($_GET['disconnect'])) {
    [$status, $data] = crmPost('/api/partner/whatsapp/disconnect', []);
    if ($status >= 400) sendError($data['message'] ?? 'Could not disconnect.', $status);
    sendSuccess('WhatsApp disconnected', $data);
}

/* --------------------------------------------------------------- connect -- */
$in            = getInput();
$code          = trim((string)($in['code'] ?? ''));
$wabaId        = preg_replace('/\D/', '', (string)($in['waba_id'] ?? ''));
$phoneNumberId = preg_replace('/\D/', '', (string)($in['phone_number_id'] ?? ''));

if ($code === '')          sendError('Missing authorization code from the WhatsApp popup.');
if ($wabaId === '')        sendError('Missing WhatsApp Business Account id.');
if ($phoneNumberId === '') sendError('Missing WhatsApp phone number id.');

if (!defined('FACEBOOK_WA_APP_ID') || FACEBOOK_WA_APP_ID === '' || !defined('FACEBOOK_WA_APP_SECRET') || FACEBOOK_WA_APP_SECRET === '') {
    sendError('WhatsApp onboarding is not configured.', 503);
}

// --- code -> business access token -----------------------------------------
// Embedded Signup returns a code exchanged WITHOUT a redirect_uri (unlike the
// classic OAuth redirect flow used for Pages).
$tokenUrl = 'https://graph.facebook.com/' . $graphVersion . '/oauth/access_token?' . http_build_query([
    'client_id'     => FACEBOOK_WA_APP_ID,
    'client_secret' => FACEBOOK_WA_APP_SECRET,
    'code'          => $code,
]);

$ch = curl_init($tokenUrl);
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 20, CURLOPT_CONNECTTIMEOUT => 5]);
$raw = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

if ($err !== '') {
    error_log('whatsapp connect: token exchange failed: ' . $err);
    sendError('Could not complete the WhatsApp connection. Please try again.', 502);
}

$tok = json_decode($raw, true) ?: [];
$accessToken = $tok['access_token'] ?? '';
if ($accessToken === '') {
    // Log Meta's reason, but never the token or the code.
    error_log('whatsapp connect: no access_token — ' . ($tok['error']['message'] ?? 'unknown'));
    sendError($tok['error']['message'] ?? 'WhatsApp did not return an access token.', 502);
}

// --- hand off to the CRM ----------------------------------------------------
[$status, $data] = crmPost('/api/partner/whatsapp/connect', [
    'wabaId'        => $wabaId,
    'phoneNumberId' => $phoneNumberId,
    'accessToken'   => $accessToken,
]);

if ($status >= 400) {
    sendError($data['message'] ?? $data['error'] ?? 'Could not finish connecting WhatsApp.', $status);
}

sendSuccess('WhatsApp connected', $data);
