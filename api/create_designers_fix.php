<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

try {
    $pdo = getDB();
    $password = '123456';
    $hashed = hashPassword($password);

    $designers = [
        ['name' => 'Designer One', 'email' => 'designer1@tapify.com'],
        ['name' => 'Designer Two', 'email' => 'designer2@tapify.com'],
        ['name' => 'Designer Three', 'email' => 'designer3@tapify.com'],
    ];

    $added = [];

    foreach ($designers as $d) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$d['email']]);
        if ($row = $stmt->fetch()) {
            // Update existing user's password and role
            $stmt = $pdo->prepare("UPDATE users SET password = ?, role = 'admin', status = 1 WHERE id = ?");
            $stmt->execute([$hashed, $row['id']]);
            $added[] = $d['email'] . ' (Updated)';
        } else {
            // Insert new user
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone, role, email_verified, status) VALUES (?, ?, ?, '0000000000', 'admin', 1, 1)");
            $stmt->execute([$d['name'], $d['email'], $hashed]);
            $added[] = $d['email'] . ' (Created)';
        }
    }

    echo json_encode([
        'success' => true, 
        'message' => 'Designers successfully created/updated!', 
        'accounts' => $added, 
        'password_for_all' => '123456'
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
