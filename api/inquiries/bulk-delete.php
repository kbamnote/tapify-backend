<?php
/**
 * TAPIFY - Bulk Delete Inquiries
 * POST /backend/api/inquiries/bulk-delete.php
 * Body: {ids: [1,2,3]} or {filter: 'read'} to delete all read
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$input = getInput();
$ids = $input['ids'] ?? [];
$filter = $input['filter'] ?? '';

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    $deleted = 0;

    if ($filter === 'read') {
        // Delete all read inquiries for user
        $stmt = $pdo->prepare("
            DELETE i FROM vcard_inquiries i
            JOIN vcards v ON v.id = i.vcard_id
            WHERE v.user_id = ? AND i.is_read = 1
        ");
        $stmt->execute([$userId]);
        $deleted = $stmt->rowCount();
    } elseif (is_array($ids) && count($ids) > 0) {
        $idsClean = array_map('intval', $ids);
        $placeholders = implode(',', array_fill(0, count($idsClean), '?'));
        $params = array_merge($idsClean, [$userId]);

        $stmt = $pdo->prepare("
            DELETE i FROM vcard_inquiries i
            JOIN vcards v ON v.id = i.vcard_id
            WHERE i.id IN ($placeholders) AND v.user_id = ?
        ");
        $stmt->execute($params);
        $deleted = $stmt->rowCount();
    } else {
        sendError('No IDs or filter provided');
    }

    sendSuccess("$deleted inquiries deleted", ['deleted' => $deleted]);
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
