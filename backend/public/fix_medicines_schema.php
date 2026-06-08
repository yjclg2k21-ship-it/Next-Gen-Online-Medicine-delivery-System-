<?php
require 'C:/xampp/htdocs/Next Gen Online Medicine Delivery System/backend/app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();

try {
    $db->exec("ALTER TABLE medicines ADD COLUMN expiry_date DATE NULL AFTER stock");
    echo "expiry_date column added to medicines table.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
