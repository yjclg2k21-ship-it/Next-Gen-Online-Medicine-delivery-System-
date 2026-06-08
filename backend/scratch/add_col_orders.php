<?php
require_once __DIR__ . '/../app/Core/Database.php';
$db = App\Core\Database::getInstance()->getConnection();
try {
    $db->exec("ALTER TABLE orders ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL AFTER updated_at");
    echo "Column deleted_at added to orders table.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
