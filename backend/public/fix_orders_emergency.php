<?php
require 'C:/xampp/htdocs/Next Gen Online Medicine Delivery System/backend/app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();

try {
    $db->exec("ALTER TABLE orders 
        ADD COLUMN sla_deadline TIMESTAMP NULL AFTER is_emergency,
        ADD COLUMN emergency_acknowledged_at TIMESTAMP NULL AFTER sla_breach,
        ADD COLUMN emergency_ready_at TIMESTAMP NULL AFTER emergency_acknowledged_at,
        ADD COLUMN emergency_picked_up_at TIMESTAMP NULL AFTER emergency_ready_at,
        ADD COLUMN emergency_delivered_at TIMESTAMP NULL AFTER emergency_picked_up_at,
        ADD COLUMN emergency_locked BOOLEAN DEFAULT 0 AFTER emergency_delivered_at");
    echo "Columns added to orders successfully.";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
