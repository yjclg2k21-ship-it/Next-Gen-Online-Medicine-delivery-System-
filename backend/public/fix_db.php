<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Starting database fix...\n";
    
    // Create revoked_tokens table
    $sql = "CREATE TABLE IF NOT EXISTS revoked_tokens (
        id INT AUTO_INCREMENT PRIMARY KEY,
        token_hash VARCHAR(255) NOT NULL UNIQUE,
        expires_at TIMESTAMP NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $db->exec($sql);
    echo "Table 'revoked_tokens' created or already exists.\n";
    
    // Check if users table exists and has data
    $stmt = $db->query("SHOW TABLES LIKE 'users'");
    if ($stmt->fetch()) {
        $count = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
        echo "Table 'users' exists with $count records.\n";
    } else {
        echo "WARNING: Table 'users' does not exist!\n";
    }

    echo "Database fix completed.\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
