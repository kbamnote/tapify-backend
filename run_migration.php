<?php
require_once __DIR__ . '/config/database.php';

try {
    $pdo = getDB();

    $sql = "
    -- Create categories table
    CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        image_url VARCHAR(500) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- Create category_content table
    CREATE TABLE IF NOT EXISTS category_content (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category_id INT NOT NULL,
        type ENUM('text', 'image', 'mixed') NOT NULL,
        text_content TEXT,
        image_url VARCHAR(500),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
    );
    ";

    $pdo->exec($sql);
    echo "<h1>Migration Successful!</h1>";
    echo "<p>The 'categories' and 'category_content' tables have been created successfully on your Railway database.</p>";

} catch (Exception $e) {
    echo "<h1>Migration Failed</h1>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
