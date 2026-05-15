<?php
/**
 * TAPIFY - Change Password
 * POST /backend/api/profile/change-password.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$input = getInput();
$currentPassword = $input['current_password'] ?? '';
$newPassword = $input['new_password'] ?? '';
$confirmPassword = $input['confirm_password'] ?? '';

if (empty($currentPassword)) sendError('Current password is required');
if (empty($newPassword)) sendError('New password is required');
if (strlen($newPassword) < 6) sendError('Password must be at least 6 characters');
if ($newPassword !== $confirmPassword) sendError('Passwords do not match');
if ($currentPassword === $newPassword) sendError('New password must be different from current');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (!$user) sendError('User not found', 404);

    if (!password_verify($currentPassword, $user['password'])) {
        sendError('Current password is incorrect');
    }

    $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([$newHash, $userId]);

    sendSuccess('Password changed successfully. Please login again.');
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
