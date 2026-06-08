<?php
require_once 'app/Core/Database.php';
$db = \App\Core\Database::getInstance()->getConnection();

$sql = "CREATE TABLE IF NOT EXISTS backups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    size VARCHAR(50) NOT NULL,
    type ENUM('daily', 'weekly', 'manual') DEFAULT 'manual',
    status ENUM('success', 'failed', 'in_progress') DEFAULT 'success',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$db->exec($sql);
echo "Backups table ensured.\n";

// Ensure storage directory
$dir = __DIR__ . '/../storage/backups';
if (!file_exists($dir)) {
    mkdir($dir, 0777, true);
    echo "Storage directory created.\n";
}
