<?php
/**
 * TAPIFY — leads the WhatsApp bot should chase, and the record that it did.
 *
 *   POST /api/public/visibility-followups.php
 *   Header: X-Tapify-Bot-Key: <VISIBILITY_BOT_KEY>
 *
 *   { "action": "due", "limit": 50 }
 *       → { leads:[{id,phone,place_id,business_name,score,band,followups_sent,
 *                   new_score,new_band,changed}] }
 *         Each lead is RE-SCORED here rather than in the CRM, so the message
 *         can say what actually moved. Re-scoring is what costs money, which is
 *         why `limit` is capped and the daily ceiling still applies.
 *
 *   { "action": "sent", "id": 12, "score": 61 }
 *       → marks one follow-up as delivered
 *
 *   { "action": "stats" }
 *       → { leads, installed, connected, avg_score }
 *
 * WHY THE RE-SCORE HAPPENS HERE. The whole point of the follow-up is "your
 * score moved". Asking the CRM to compare numbers would mean teaching it the
 * scoring rules, which is the duplication this design exists to avoid.
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/google/PlacesClient.php';
require_once __DIR__ . '/../../includes/google/VisibilityScore.php';
require_once __DIR__ . '/../../includes/google/VisibilityLeads.php';

ini_set('display_errors', '0');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST')    { sendError('POST only', 405); }

$expected = getenv('VISIBILITY_BOT_KEY') ?: '';
if ($expected === '')                                     { sendError('Not configured.', 503); }
if (!hash_equals($expected, (string)($_SERVER['HTTP_X_TAPIFY_BOT_KEY'] ?? ''))) {
    sendError('Not authorised.', 401);
}

$input  = getInput();
$action = trim((string)($input['action'] ?? 'due'));
$db     = getDB();

if ($action === 'stats') {
    $stats = VisibilityLeads::stats($db);
    $stats['places_calls_today'] = PlacesClient::callsToday($db);
    $stats['daily_cap'] = (int)(getenv('VISIBILITY_DAILY_CAP') ?: 2000);
    sendSuccess('Lead stats', $stats);
}

if ($action === 'sent') {
    $id = (int)($input['id'] ?? 0);
    if ($id <= 0) sendError('id is required.', 422);
    VisibilityLeads::markFollowedUp($db, $id, isset($input['score']) ? (int)$input['score'] : null);
    sendSuccess('Recorded', ['id' => $id]);
}

if ($action !== 'due') sendError('Unknown action.', 422);

$limit = (int)($input['limit'] ?? 25);
$leads = VisibilityLeads::dueForFollowUp($db, $limit);
if (!$leads) sendSuccess('Nothing due', ['leads' => []]);

$places = new PlacesClient();
PlacesClient::ensureUsageTable($db);
$out = [];
foreach ($leads as $lead) {
    $row = [
        'id'             => (int)$lead['id'],
        'phone'          => $lead['phone'],
        'place_id'       => $lead['place_id'],
        'business_name'  => $lead['business_name'],
        'score'          => $lead['score'] !== null ? (int)$lead['score'] : null,
        'band'           => $lead['band'],
        'followups_sent' => (int)$lead['followups_sent'],
        'new_score'      => null,
        'new_band'       => null,
        'changed'        => 0,
    ];

    // Re-scoring spends money, so it goes through the SAME daily ceiling as the
    // bot's own lookups. Without this the follow-up loop would run past the cap
    // unattended — the one path nobody is watching when it does.
    if ($places->isConfigured() && PlacesClient::spendAllowed($db)) {
        PlacesClient::countCall($db);
        // A stale figure would be worse than none: telling someone their score
        // "moved" when it did not is the fastest way to be ignored.
        $details = $places->details($lead['place_id']);
        if ($details) {
            $fresh = VisibilityScore::compute($details);
            $row['new_score'] = $fresh['score'];
            $row['new_band']  = $fresh['band'];
            $row['changed']   = $row['score'] !== null ? ($fresh['score'] - $row['score']) : 0;
        }
    }
    $out[] = $row;
}

sendSuccess('Leads due', ['leads' => $out]);
