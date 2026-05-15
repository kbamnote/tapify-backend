<?php
/**
 * TAPIFY - Mark All Inquiries as Read
 * POST /backend/api/inquiries/mark-all-read.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    $stmt = $pdo->prepare("
        UPDATE vcard_inquiries i
        JOIN vcards v ON v.id = i.vcard_id
        SET i.is_read = 1
        WHERE v.user_id = ? AND i.is_read = 0
    ");
    $stmt->execute([$userId]);

    sendSuccess('All marked as read', ['updated' => $stmt->rowCount()]);
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
