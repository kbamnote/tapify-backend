<?php
/**
 * TAPIFY - Update Profile
 * POST /backend/api/profile/update.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$input = getInput();
$name = sanitize($input['name'] ?? '');
$email = sanitize($input['email'] ?? '');
$phone = sanitize($input['phone'] ?? '');

if (empty($name)) sendError('Name is required');
if (empty($email)) sendError('Email is required');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) sendError('Invalid email format');
if (strlen($name) > 150) sendError('Name too long');
if (strlen($name) < 2) sendError('Name too short');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    // Check email uniqueness
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1");
    $stmt->execute([$email, $userId]);
    if ($stmt->fetch()) sendError('Email already used by another account');

    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
    $stmt->execute([$name, $email, $phone, $userId]);

    sendSuccess('Profile updated successfully');
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
