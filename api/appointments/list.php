<?php
/**
 * TAPIFY - List Appointments API
 * GET /backend/api/appointments/list.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAuth();

try {
    $pdo = getDB();
    $userId = getCurrentUserId();
    $role = $_SESSION['user_role'] ?? 'user';

    // If admin, they can see all. If user, only their own.
    if ($role === 'admin') {
        $stmt = $pdo->prepare("
            SELECT a.*, v.vcard_name 
            FROM appointments a
            LEFT JOIN vcards v ON v.id = a.vcard_id
            ORDER BY a.created_at DESC
        ");
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare("
            SELECT a.*, v.vcard_name 
            FROM appointments a
            LEFT JOIN vcards v ON v.id = a.vcard_id
            WHERE a.user_id = ?
            ORDER BY a.created_at DESC
        ");
        $stmt->execute([$userId]);
    }

    $appointments = $stmt->fetchAll();

    sendSuccess('Appointments retrieved', ['appointments' => $appointments]);

} catch (Exception $e) {
    sendError('Failed to load appointments: ' . $e->getMessage(), 500);
}
