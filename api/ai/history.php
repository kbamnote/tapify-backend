<?php
/**
 * GET /api/ai/history.php?feature=<key>&saved=1&limit=30
 * Returns the current user's AI generation history (most recent first).
 */
require_once __DIR__ . '/../../config/database.php';
ini_set('display_errors', '0'); // keep PHP errors out of the JSON response
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/ai/autoload.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError('Method not allowed', 405);
}

requireAuth();
$userId = getCurrentUserId();

$feature   = isset($_GET['feature']) ? trim($_GET['feature']) : null;
$savedOnly = isset($_GET['saved']) && $_GET['saved'] == '1';
$limit     = isset($_GET['limit']) ? (int) $_GET['limit'] : 30;

try {
    $history = new AiHistory(getDB());
    $items   = $history->listForUser($userId, $feature, $savedOnly, $limit);
    sendSuccess('History fetched', ['items' => $items]);
} catch (Exception $e) {
    AiLogger::error('history.endpoint_failed', ['error' => $e->getMessage()]);
    sendError('Could not load history.', 500);
}
