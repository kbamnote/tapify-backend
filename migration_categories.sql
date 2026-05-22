-- Create `categories` table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create `category_content` table
CREATE TABLE IF NOT EXISTS category_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    type ENUM('text', 'image', 'mixed') NOT NULL,
    text_content TEXT,
    image_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);
