<?php
/**
 * TAPIFY — public visibility score, for the WhatsApp bot.
 *
 *   POST /api/public/visibility-score.php
 *   Header: X-Tapify-Bot-Key: <VISIBILITY_BOT_KEY>
 *
 *   { "query": "Galaxy Car Decor Nagpur" }   → { candidates:[{place_id,name,address}] }
 *   { "place_id": "ChIJ..." }                → { place:{...}, score:{...} }
 *
 * WHY THIS LIVES HERE AND NOT IN THE CRM.
 * The CRM bot is a Node service; the scoring conventions (bands, groups, item
 * shape, wording) already live in PHP beside MarketingScore. Writing a second
 * implementation in Node would drift, and the day the two disagree the bot
 * quotes a customer a number the app contradicts. One implementation, called
 * over HTTP.
 *
 * SPEND. Places is billed per request, and this is about to be advertised
 * nationally. Two guards, both mandatory:
 *   - every lookup is cached by place_id for CACHE_DAYS
 *   - a hard daily call ceiling (VISIBILITY_DAILY_CAP), after which the bot is
 *     told to back off rather than the bill being allowed to run
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/google/PlacesClient.php';
require_once __DIR__ . '/../../includes/google/VisibilityScore.php';
require_once __DIR__ . '/../../includes/google/VisibilityLeads.php';

// Not display_errors: a PHP notice in front of the body would corrupt the JSON
// the bot parses, and the bot would report "could not check" for a working call.
ini_set('display_errors', '0');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST')    { sendError('POST only', 405); }

const CACHE_DAYS = 7;

/* ------------------------------------------------------------------ auth */
// Server-to-server only. Without this any visitor could run up the Places bill
// from a browser, which is the whole reason the key never reaches the client.
$expected = getenv('VISIBILITY_BOT_KEY') ?: '';
$given    = $_SERVER['HTTP_X_TAPIFY_BOT_KEY'] ?? '';
if ($expected === '') {
    sendError('Scoring is not configured on this server.', 503);
}
if (!hash_equals($expected, (string)$given)) {
    sendError('Not authorised.', 401);
}

$places = new PlacesClient();
if (!$places->isConfigured()) {
    sendError('Google lookup is not configured on this server.', 503);
}

$input    = getInput();
$query    = trim((string)($input['query'] ?? ''));
$placeId  = trim((string)($input['place_id'] ?? ''));
$db       = getDB();

ensureTables($db);

/* ------------------------------------------------------- mode 1: search */
if ($placeId === '') {
    if (mb_strlen($query) < 3) {
        sendError('Send a business name and city, at least 3 characters.', 422);
    }
    // A pasted Maps link is not a searchable phrase. Resolve it to the business
    // name first — the bot explicitly invites people to paste one.
    $resolved = $places->resolveMapsLink($query);
    if ($resolved !== null) $query = $resolved;

    if (!PlacesClient::spendAllowed($db)) {
        sendError('Busy right now — try again in a little while.', 429);
    }
    PlacesClient::countCall($db);

    $candidates = $places->searchText($query, 3);
    if ($candidates === null) {
        // The lookup itself failed. Saying "no match" here would blame the
        // customer's business for our outage, and bury the outage.
        sendError('Could not reach Google just now. Please try again shortly.', 502);
    }
    sendSuccess($candidates ? 'Matches' : 'No matches',
                ['candidates' => $candidates, 'query' => $query]);
}

/* -------------------------------------------------- mode 2: score a place */
// The bot passes the WhatsApp number it is talking to. That is what makes the
// lead attributable later, when the same number appears at registration — no
// install-referrer library, no code for the customer to type in.
$leadPhone = trim((string)($input['phone'] ?? ''));

$cached = readCache($db, $placeId);
if ($cached !== null) {
    // Still a lead even when the score came from cache — the person asked.
    if ($leadPhone !== '' && !empty($cached['place']) && !empty($cached['score'])) {
        VisibilityLeads::record($db, $leadPhone, $cached['place'], $cached['score']);
    }
    // Rows cached before the Marketing Score existed carry no `marketing`
    // block. Rebuild it from the facts already stored instead of paying
    // Google again. description_len was never cached, so that item drops and
    // `possible` shrinks — the same rule as every other absent signal.
    if (empty($cached['marketing']) && !empty($cached['place']['facts'])) {
        $f = $cached['place']['facts'];
        $cached['marketing'] = VisibilityScore::marketingPreview([
            'reviews_total' => $f['reviews'] ?? null,
            'rating'        => $f['rating'] ?? null,
            'photos_total'  => $f['photos'] ?? null,
            'photos_capped' => $f['photos_capped'] ?? null,
            'has_hours'     => $f['has_hours'] ?? null,
            'has_website'   => $f['has_website'] ?? null,
            'has_category'  => ($f['category'] ?? null) ? 1 : 0,
        ]);
    }
    $cached['cached'] = true;
    sendSuccess('Visibility score', $cached);
}

if (!PlacesClient::spendAllowed($db)) {
    sendError('Busy right now — try again in a little while.', 429);
}
PlacesClient::countCall($db);

$details = $places->details($placeId);
if (!$details) {
    sendError('Could not read that listing from Google. Try again in a moment.', 502);
}

$score = VisibilityScore::compute($details);
$place = [
    'place_id' => $details['place_id'],
    'name'     => $details['name'],
    'address'  => $details['address'],
    'maps_url' => $details['maps_url'],
    'status'   => $details['status'],
    // The raw figures, so the bot can quote them back. A score nobody
    // recognises is a score nobody believes — "18 reviews, 4.2 stars" is what
    // makes the number credible to the person reading it.
    'facts'    => [
        'reviews'     => $details['reviews_total'],
        'rating'      => $details['rating'],
        'photos'      => $details['photos_total'],
        'photos_capped' => $details['photos_capped'],
        'has_hours'   => $details['has_hours'],
        'has_website' => $details['has_website'],
        'category'    => $details['category'],
    ],
];

$payload = ['place' => $place, 'score' => $score];
// The bot quotes THIS number as the Marketing Score. Computed off the same
// $details compute() already consumed — zero extra Places calls.
$payload['marketing'] = VisibilityScore::marketingPreview($details);
writeCache($db, $placeId, $payload);
if ($leadPhone !== '') {
    VisibilityLeads::record($db, $leadPhone, $place, $score);
}
$payload['cached'] = false;
sendSuccess('Visibility score', $payload);


/* ------------------------------------------------------------- helpers */

function ensureTables(PDO $db): void
{
    try {
        $db->exec(
            "CREATE TABLE IF NOT EXISTS place_score_cache (
               place_id   VARCHAR(255) NOT NULL PRIMARY KEY,
               payload    MEDIUMTEXT   NOT NULL,
               created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
               INDEX idx_created (created_at)
             ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
        PlacesClient::ensureUsageTable($db);
    } catch (Exception $e) {
        // A missing cache table must not stop a lookup working — it only means
        // we pay Google again. Log and carry on.
        error_log('[VIS] table setup failed: ' . $e->getMessage());
    }
}

function readCache(PDO $db, string $placeId)
{
    try {
        $st = $db->prepare(
            "SELECT payload FROM place_score_cache
              WHERE place_id = ? AND created_at >= (NOW() - INTERVAL " . CACHE_DAYS . " DAY)"
        );
        $st->execute([$placeId]);
        $row = $st->fetchColumn();
        if (!$row) return null;
        $d = json_decode($row, true);
        return is_array($d) ? $d : null;
    } catch (Exception $e) {
        return null;
    }
}

function writeCache(PDO $db, string $placeId, array $payload): void
{
    try {
        $st = $db->prepare(
            "INSERT INTO place_score_cache (place_id, payload) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE payload = VALUES(payload), created_at = CURRENT_TIMESTAMP"
        );
        $st->execute([$placeId, json_encode($payload)]);
    } catch (Exception $e) {
        error_log('[VIS] cache write failed: ' . $e->getMessage());
    }
}

