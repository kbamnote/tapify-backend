<?php
/**
 * TAPIFY - Delete Inquiry
 * POST /backend/api/inquiries/delete.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$input = getInput();
$id = (int)($input['id'] ?? 0);
if (!$id) sendError('Inquiry ID required');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    // Verify ownership
    $stmt = $pdo->prepare("
        SELECT i.id FROM vcard_inquiries i
        JOIN vcards v ON v.id = i.vcard_id
        WHERE i.id = ? AND v.user_id = ? LIMIT 1
    ");
    $stmt->execute([$id, $userId]);
    if (!$stmt->fetch()) sendError('Access denied', 403);

    $stmt = $pdo->prepare("DELETE FROM vcard_inquiries WHERE id = ?");
    $stmt->execute([$id]);

    sendSuccess('Inquiry deleted');
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
