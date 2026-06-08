<?php
require_once 'app/Core/Database.php';
$db = \App\Core\Database::getInstance()->getConnection();

try {
    $db->exec("DROP TABLE IF EXISTS backups");
    $db->exec("CREATE TABLE backups (
        id INT AUTO_INCREMENT PRIMARY KEY,
        filename VARCHAR(255) NOT NULL,
        size VARCHAR(50) NOT NULL,
        type ENUM('daily', 'weekly', 'manual') DEFAULT 'manual',
        status ENUM('success', 'failed', 'in_progress') DEFAULT 'success',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Success: 'backups' table recreated with correct schema.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
