<?php
/**
 * TAPIFY — website brief from the WhatsApp bot.
 *
 *   POST /api/public/site-from-chat.php
 *   Header: X-Tapify-Bot-Key: <VISIBILITY_BOT_KEY>
 *
 *   { phone, email?, business?, type, services, audience }
 *      → { brief_id, url? }
 *
 * WHY A BRIEF AND NOT A FINISHED SITE ROW.
 * The site itself is built by the website-builder pipeline, not here and not
 * by the vCard templates. This endpoint is the single, authenticated front
 * door for chat-originated briefs: validate, persist, then call
 * dispatch_to_builder() below — that one function is where generation hooks
 * in when the builder side consumes these rows. Everything before it must not
 * know how a site gets built; everything after it never learns WhatsApp.
 *
 * AUTH. Same server-to-server key as visibility-score.php: without it anyone
 * could stuff briefs into the pipeline from a browser tab.
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

ini_set('display_errors', '0');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST')    { sendError('POST only', 405); }

$expected = getenv('VISIBILITY_BOT_KEY') ?: '';
$given    = $_SERVER['HTTP_X_TAPIFY_BOT_KEY'] ?? '';
if ($expected === '') {
    sendError('Site intake is not configured on this server.', 503);
}
if (!hash_equals($expected, (string)$given)) {
    sendError('Not authorised.', 401);
}

$input     = getInput();
$email     = strtolower(trim((string)($input['email'] ?? '')));
$phone     = trim((string)($input['phone'] ?? ''));
$business  = trim((string)($input['business'] ?? ''));
$type      = trim((string)($input['type'] ?? ''));
$services  = trim((string)($input['services'] ?? ''));
$audience  = trim((string)($input['audience'] ?? ''));

// The three chat answers are the payload; without them there is nothing to build.
if ($type === '' || $services === '' || $audience === '') {
    sendError('type, services and audience are required.', 422);
}

try {
    $db = getDB();
    ensureTable($db);

    // Attach the brief to an account when we can — the bot collects the email
    // earlier in the very same conversation, so most briefs will match.
    $userId = null;
    if ($email !== '') {
        $st = $db->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $st->execute([$email]);
        $userId = $st->fetchColumn() ?: null;
    }

    $st = $db->prepare(
        'INSERT INTO site_chat_briefs
            (user_id, phone, email, business, business_type, services, audience, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $st->execute([
        $userId,
        $phone !== '' ? $phone : null,
        $email !== '' ? $email : null,
        $business !== '' ? $business : null,
        $type,
        $services,
        $audience,
        'new',
    ]);
    $briefId = (int)$db->lastInsertId();

    $brief = [
        'id' => $briefId, 'user_id' => $userId, 'phone' => $phone, 'email' => $email,
        'business' => $business, 'type' => $type, 'services' => $services, 'audience' => $audience,
    ];
    dispatch_to_builder($db, $brief);

    sendSuccess('Brief received', ['brief_id' => $briefId]);

} catch (Exception $e) {
    error_log('[SITE-CHAT] intake failed: ' . $e->getMessage());
    sendError('Could not store the brief right now.', 500);
}

/* ------------------------------------------------------------- helpers */

function ensureTable(PDO $db): void
{
    try {
        $db->exec(
            "CREATE TABLE IF NOT EXISTS site_chat_briefs (
                id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                user_id       INT UNSIGNED NULL,
                phone         VARCHAR(32)  NULL,
                email         VARCHAR(190) NULL,
                business      VARCHAR(190) NULL,
                business_type VARCHAR(120) NOT NULL,
                services      VARCHAR(500) NOT NULL,
                audience      VARCHAR(250) NOT NULL,
                status        VARCHAR(24)  NOT NULL DEFAULT 'new',
                created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_status (status),
                INDEX idx_user (user_id)
             ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
    } catch (Exception $e) {
        // Same rule as every table setup in this codebase: log and carry on.
        error_log('[SITE-CHAT] table setup failed: ' . $e->getMessage());
    }
}

/**
 * THE HOOKUP POINT into the website builder.
 *
 * Today it only marks the row queued — nothing pretends a site was generated
 * when it was not. The builder side takes over by replacing this body (or
 * consuming rows WHERE status = 'new'), at which point the WhatsApp customer
 * gets real generation with zero changes on the bot side.
 */
function dispatch_to_builder(PDO $db, array $brief): void
{
    try {
        $st = $db->prepare("UPDATE site_chat_briefs SET status = 'queued' WHERE id = ?");
        $st->execute([$brief['id']]);
    } catch (Exception $e) {
        error_log('[SITE-CHAT] dispatch mark failed: ' . $e->getMessage());
    }
}
