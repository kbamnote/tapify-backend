<?php
/**
 * TAPIFY - Delete Appointment
 * POST /backend/api/appointments/delete.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();
blockStaffDelete(); // card-editor accounts may not delete

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$input = getInput();
$id = (int)($input['id'] ?? 0);
if (!$id) sendError('Appointment ID required');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    // A booking made on a website-builder site lives in its own table and is
    // owned through `sites`, not `vcards`.
    $isSite = (($input['source'] ?? '') === 'site');
    $table  = $isSite ? 'site_appointments' : 'vcard_appointments';

    if (isAdmin()) {
        $stmt = $pdo->prepare("SELECT id FROM $table WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
    } elseif ($isSite) {
        $stmt = $pdo->prepare("
            SELECT sa.id FROM site_appointments sa
            JOIN sites s ON s.id = sa.site_id
            WHERE sa.id = ? AND s.user_id = ? LIMIT 1
        ");
        $stmt->execute([$id, $userId]);
    } else {
        $stmt = $pdo->prepare("
            SELECT a.id FROM vcard_appointments a
            JOIN vcards v ON v.id = a.vcard_id
            WHERE a.id = ? AND v.user_id = ? LIMIT 1
        ");
        $stmt->execute([$id, $userId]);
    }
    if (!$stmt->fetch()) sendError('Access denied', 403);

    $stmt = $pdo->prepare("DELETE FROM $table WHERE id = ?");
    $stmt->execute([$id]);

    sendSuccess('Appointment deleted');
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
