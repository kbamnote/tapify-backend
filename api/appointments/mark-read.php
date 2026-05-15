<?php
/**
 * TAPIFY - Mark Appointment Read
 * POST /backend/api/appointments/mark-read.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$input = getInput();
$id = (int)($input['id'] ?? 0);
$isRead = isset($input['is_read']) ? (int)(bool)$input['is_read'] : 1;

if (!$id) sendError('Appointment ID required');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    $stmt = $pdo->prepare("
        SELECT a.id FROM vcard_appointments a
        JOIN vcards v ON v.id = a.vcard_id
        WHERE a.id = ? AND v.user_id = ? LIMIT 1
    ");
    $stmt->execute([$id, $userId]);
    if (!$stmt->fetch()) sendError('Access denied', 403);

    $stmt = $pdo->prepare("UPDATE vcard_appointments SET is_read = ? WHERE id = ?");
    $stmt->execute([$isRead, $id]);

    sendSuccess('Updated');
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
