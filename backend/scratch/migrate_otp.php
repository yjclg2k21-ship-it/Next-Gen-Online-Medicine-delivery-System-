<?php
require_once __DIR__ . '/../app/bootstrap.php';
use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    $db->exec("ALTER TABLE orders ADD COLUMN IF NOT EXISTS delivery_otp VARCHAR(10) DEFAULT NULL");
    echo "Migration Success: delivery_otp column synchronized.\n";
} catch (Exception $e) {
    echo "Migration Failed: " . $e->getMessage() . "\n";
}
