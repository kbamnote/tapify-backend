<?php
/**
 * POST /api/ai/save.php
 * Body: { history_id*, saved? (default true) }
 * Bookmarks / un-bookmarks a generated result the user wants to keep.
 */
require_once __DIR__ . '/../../config/database.php';
ini_set('display_errors', '0'); // keep PHP errors out of the JSON response
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/ai/autoload.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method not allowed', 405);
}

requireAuth();
$userId = getCurrentUserId();

$input     = getInput();
$historyId = (int) ($input['history_id'] ?? 0);
$saved     = array_key_exists('saved', $input) ? !empty($input['saved']) : true;

if ($historyId <= 0) {
    sendError('history_id is required', 422);
}

try {
    $history = new AiHistory(getDB());
    $ok      = $history->setSaved($userId, $historyId, $saved);
    if (!$ok) {
        sendError('Result not found.', 404);
    }
    sendSuccess($saved ? 'Saved' : 'Removed from saved', ['history_id' => $historyId, 'is_saved' => $saved]);
} catch (Exception $e) {
    AiLogger::error('save.endpoint_failed', ['error' => $e->getMessage()]);
    sendError('Could not update saved state.', 500);
}
