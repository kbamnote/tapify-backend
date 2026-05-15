<?php
/**
 * TAPIFY - Toggle Read Status
 * POST /backend/api/inquiries/mark-read.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$input = getInput();
$id = (int)($input['id'] ?? 0);
$isRead = isset($input['is_read']) ? (int)(bool)$input['is_read'] : null;

if (!$id) sendError('Inquiry ID required');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    // Verify ownership through vcard
    $stmt = $pdo->prepare("
        SELECT i.id FROM vcard_inquiries i
        JOIN vcards v ON v.id = i.vcard_id
        WHERE i.id = ? AND v.user_id = ? LIMIT 1
    ");
    $stmt->execute([$id, $userId]);
    if (!$stmt->fetch()) sendError('Access denied', 403);

    if ($isRead === null) {
        // Toggle
        $stmt = $pdo->prepare("UPDATE vcard_inquiries SET is_read = NOT is_read WHERE id = ?");
        $stmt->execute([$id]);
    } else {
        $stmt = $pdo->prepare("UPDATE vcard_inquiries SET is_read = ? WHERE id = ?");
        $stmt->execute([$isRead, $id]);
    }

    sendSuccess('Status updated');
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
