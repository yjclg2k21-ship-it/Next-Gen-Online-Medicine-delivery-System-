<?php
$host = '127.0.0.1';
$db   = 'medicine_delivery';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

$pdo = new PDO($dsn, $user, $pass, $options);

$sql = "
CREATE TABLE IF NOT EXISTS blogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content TEXT NOT NULL,
    cover_image VARCHAR(255) NULL,
    author_name VARCHAR(100) DEFAULT 'MediMitra Clinical Team',
    status ENUM('draft', 'published') DEFAULT 'published',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert dummy data if empty
INSERT INTO blogs (title, slug, content, cover_image) 
SELECT 'Managing Hypertension with Clinical Precision', 'managing-hypertension', '<p>Hypertension, commonly known as high blood pressure, is a condition...</p>', ''
WHERE NOT EXISTS (SELECT 1 FROM blogs LIMIT 1);

INSERT INTO blogs (title, slug, content, cover_image) 
SELECT 'Understanding Antibiotic Resistance', 'antibiotic-resistance', '<p>Antibiotic resistance occurs when bacteria change in response to the use of these medicines...</p>', ''
WHERE NOT EXISTS (SELECT 1 FROM blogs WHERE slug = 'antibiotic-resistance');
";

try {
    $pdo->exec($sql);
    echo "Blogs table created and seeded successfully.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
