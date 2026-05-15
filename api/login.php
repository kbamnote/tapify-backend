<?php
/**
 * TAPIFY - Login API
 * POST /backend/api/login.php
 * Body: { email, password }
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Only POST method allowed', 405);
}

$input = getInput();
$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';

// Validation
if (empty($email) || empty($password)) {
    sendError('Email and password are required');
}

if (!isValidEmail($email)) {
    sendError('Invalid email format');
}

try {
    $pdo = getDB();

    // Find user
    $stmt = $pdo->prepare("SELECT id, name, email, password, phone, avatar, role, status FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        sendError('Invalid email or password', 401);
    }

    if ($user['status'] != 1) {
        sendError('Your account is disabled. Contact support.', 403);
    }

    // Verify password
    if (!verifyPassword($password, $user['password'])) {
        sendError('Invalid email or password', 401);
    }

    // Update last login
    $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);

    // Create session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['logged_in_at'] = time();

    // Remove password from response
    unset($user['password']);

    sendSuccess('Login successful', [
        'user' => $user,
        'session_id' => session_id()
    ]);

} catch (Exception $e) {
    sendError('Login failed: ' . $e->getMessage(), 500);
}
