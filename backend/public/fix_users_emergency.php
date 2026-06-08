<?php
require 'C:/xampp/htdocs/Next Gen Online Medicine Delivery System/backend/app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();

try {
    $db->exec("ALTER TABLE users 
        ADD COLUMN latitude DECIMAL(10, 8) NULL AFTER is_verified,
        ADD COLUMN longitude DECIMAL(11, 8) NULL AFTER latitude,
        ADD COLUMN is_online BOOLEAN DEFAULT 0 AFTER longitude,
        ADD COLUMN last_idle_since TIMESTAMP NULL AFTER is_online,
        ADD COLUMN is_non_responsive BOOLEAN DEFAULT 0 AFTER last_idle_since,
        ADD COLUMN emergency_locked_order_id INT NULL AFTER is_non_responsive");
    echo "Columns added to users successfully.";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
