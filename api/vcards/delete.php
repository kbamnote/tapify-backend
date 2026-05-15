<?php
/**
 * TAPIFY - Delete vCard API
 * POST /backend/api/vcards/delete.php
 * Body: { id }
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Only POST method allowed', 405);
}

$input = getInput();
$vcardId = (int)($input['id'] ?? 0);

if (!$vcardId) {
    sendError('vCard ID is required');
}

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    // Verify ownership (Admins can delete any card)
    if (isAdmin()) {
        $stmt = $pdo->prepare("SELECT id, vcard_name FROM vcards WHERE id = ? LIMIT 1");
        $stmt->execute([$vcardId]);
    } else {
        $stmt = $pdo->prepare("SELECT id, vcard_name FROM vcards WHERE id = ? AND user_id = ? LIMIT 1");
        $stmt->execute([$vcardId, $userId]);
    }
    $vcard = $stmt->fetch();

    if (!$vcard) {
        sendError('vCard not found or access denied', 404);
    }

    // Delete (CASCADE will delete related records)
    if (isAdmin()) {
        $stmt = $pdo->prepare("DELETE FROM vcards WHERE id = ?");
        $stmt->execute([$vcardId]);
    } else {
        $stmt = $pdo->prepare("DELETE FROM vcards WHERE id = ? AND user_id = ?");
        $stmt->execute([$vcardId, $userId]);
    }

    sendSuccess('"' . $vcard['vcard_name'] . '" deleted successfully');

} catch (Exception $e) {
    sendError('Failed to delete vCard: ' . $e->getMessage(), 500);
}
