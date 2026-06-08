<?php
require 'C:/xampp/htdocs/Next Gen Online Medicine Delivery System/backend/app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();

try {
    $db->exec("ALTER TABLE prescriptions 
        ADD COLUMN vendor_id INT NULL AFTER user_id,
        ADD COLUMN reviewed_by INT NULL,
        ADD COLUMN reviewed_at TIMESTAMP NULL,
        ADD COLUMN remarks TEXT NULL,
        ADD COLUMN expiry_date DATE NULL
    ");
    echo "Added vendor_id, reviewed_by, reviewed_at, remarks, expiry_date to prescriptions\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
