<?php
require __DIR__ . '/backend/app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();

try {
    $db->exec("ALTER TABLE `orders` ADD COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL;");
    echo "Added deleted_at column to orders table.\n";
} catch (Exception $e) {
    echo "Warning: " . $e->getMessage() . "\n";
}
