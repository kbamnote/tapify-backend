<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

try {
    $pdo = getDB();

    // Step 1: Ensure 'designer' role exists in ENUM
    $pdo->exec("ALTER TABLE users MODIFY COLUMN role ENUM('admin','user','designer') NOT NULL DEFAULT 'user'");

    // Step 2: Create / update 3 designer accounts
    $password = 'Designer@123';
    $hashed = hashPassword($password);

    $designers = [
        ['name' => 'Designer One',   'email' => 'designer1@tapify.com'],
        ['name' => 'Designer Two',   'email' => 'designer2@tapify.com'],
        ['name' => 'Designer Three', 'email' => 'designer3@tapify.com'],
    ];

    $added = [];

    foreach ($designers as $d) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$d['email']]);
        if ($row = $stmt->fetch()) {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, password = ?, role = 'designer', status = 1 WHERE id = ?");
            $stmt->execute([$d['name'], $hashed, $row['id']]);
            $added[] = $d['email'] . ' (Updated → designer)';
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone, role, email_verified, status) VALUES (?, ?, ?, '', 'designer', 1, 1)");
            $stmt->execute([$d['name'], $d['email'], $hashed]);
            $added[] = $d['email'] . ' (Created as designer)';
        }
    }

    echo json_encode([
        'success'          => true,
        'message'          => 'Designer accounts ready!',
        'accounts'         => $added,
        'password_for_all' => 'Designer@123',
        'panel_url'        => '/designer.html',
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
