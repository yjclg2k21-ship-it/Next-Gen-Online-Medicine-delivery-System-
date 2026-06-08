<?php
require_once __DIR__ . '/../app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();

$db->exec("CREATE TABLE IF NOT EXISTS `revoked_tokens` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `token_hash` VARCHAR(64) NOT NULL,
    `revoked_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `expires_at` TIMESTAMP NULL DEFAULT NULL,
    INDEX (`token_hash`)
) ENGINE=InnoDB");

echo "revoked_tokens table created successfully!\n";

// Verify
$s = $db->query("SELECT COUNT(*) FROM `revoked_tokens`");
echo "Verified: " . $s->fetchColumn() . " rows.\n";
