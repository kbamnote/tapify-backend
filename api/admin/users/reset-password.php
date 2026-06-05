<?php
/**
 * TAPIFY - Admin: Reset a User's Password
 * POST /backend/api/admin/users/reset-password.php
 *
 * Body: { "user_id": <int>, "new_password": "<string>" }
 *
 * Note: passwords are stored as bcrypt hashes and cannot be read back.
 * An admin can only SET a new password, never view the existing one.
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/functions.php';

requireAdmin(); // Strictly restricted to Admins

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$input = getInput();
$userId = $input['user_id'] ?? null;
$newPassword = $input['new_password'] ?? '';

if (empty($userId) || !is_numeric($userId)) sendError('Valid user_id is required');
if (empty($newPassword)) sendError('New password is required');
if (strlen($newPassword) < 6) sendError('Password must be at least 6 characters');

try {
    $pdo = getDB();

    // Make sure the target user exists
    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (!$user) sendError('User not found', 404);

    $newHash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 10]);
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([$newHash, $userId]);

    sendSuccess('Password reset successfully for ' . $user['email']);
} catch (Exception $e) {
    sendError('Failed to reset password: ' . $e->getMessage(), 500);
}
