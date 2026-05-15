<?php
/**
 * TAPIFY - Admin: List Users API
 * GET /backend/api/admin/users/list.php
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/functions.php';

requireAdmin(); // Strictly restricted to Admins

try {
    $pdo = getDB();
    
    $stmt = $pdo->prepare("
        SELECT 
            u.id, u.name, u.email, u.phone, u.role, u.status, u.created_at, u.last_login,
            (SELECT COUNT(*) FROM vcards WHERE user_id = u.id) as vcards_count
        FROM users u
        ORDER BY u.created_at DESC
    ");
    $stmt->execute();
    $users = $stmt->fetchAll();

    sendSuccess('Users retrieved', ['users' => $users]);

} catch (Exception $e) {
    sendError('Failed to load users: ' . $e->getMessage(), 500);
}
