<?php
/**
 * TAPIFY - Register API
 * POST /backend/api/register.php
 * Body: { name, email, password, phone }
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Only POST method allowed', 405);
}

$input = getInput();
$name = sanitize($input['name'] ?? '');
$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';
$phone = sanitize($input['phone'] ?? '');

// Validation
if (empty($name) || empty($email) || empty($password)) {
    sendError('Name, email and password are required');
}

if (!isValidEmail($email)) {
    sendError('Invalid email format');
}

if (strlen($password) < 6) {
    sendError('Password must be at least 6 characters');
}

if (strlen($name) < 2) {
    sendError('Name must be at least 2 characters');
}

try {
    $pdo = getDB();

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        sendError('Email already registered. Please login instead.', 409);
    }

    // Hash password
    $hashedPassword = hashPassword($password);

    // Insert user
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone, role, email_verified, status) VALUES (?, ?, ?, ?, 'user', 0, 1)");
    $stmt->execute([$name, $email, $hashedPassword, $phone]);
    $userId = $pdo->lastInsertId();

    // Create default subscription (Free Plan, 30 days trial)
    $stmt = $pdo->prepare("INSERT INTO subscriptions (user_id, plan_name, vcards_limit, stores_limit, price, subscribed_date, expiry_date, status) VALUES (?, 'Free Trial', 1, 0, 0, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), 'active')");
    $stmt->execute([$userId]);

    // Auto-login after register
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_role'] = 'user';
    $_SESSION['logged_in_at'] = time();

    sendSuccess('Registration successful! Welcome to Tapify.', [
        'user' => [
            'id' => $userId,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'role' => 'user'
        ]
    ]);

} catch (Exception $e) {
    sendError('Registration failed: ' . $e->getMessage(), 500);
}
