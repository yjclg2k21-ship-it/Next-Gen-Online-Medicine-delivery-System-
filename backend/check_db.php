<?php
require 'app/Core/Database.php';
try {
    $db = \App\Core\Database::getInstance()->getConnection();
    $q = $db->query("SHOW TABLES LIKE 'return_requests'");
    $tables = $q->fetchAll();
    if (empty($tables)) {
        echo "TABLE_MISSING: return_requests\n";
        // Attempt to create it
        $sql = "CREATE TABLE IF NOT EXISTS `return_requests` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `order_id` int(11) NOT NULL,
            `user_id` int(11) NOT NULL,
            `reason` text NOT NULL,
            `status` enum('pending','approved','rejected','completed') DEFAULT 'pending',
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
            `deleted_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $db->exec($sql);
        echo "TABLE_CREATED: return_requests\n";
    } else {
        echo "TABLE_EXISTS: return_requests\n";
    }

    // Check orders table status column
    $q = $db->query("DESCRIBE orders");
    $cols = $q->fetchAll();
    foreach ($cols as $col) {
        if ($col['Field'] === 'status') {
            echo "ORDERS_STATUS_TYPE: " . $col['Type'] . "\n";
            // If it's an enum, we might need to add return statuses
            if (strpos($col['Type'], 'enum') !== false) {
                if (strpos($col['Type'], 'return_requested') === false) {
                    echo "UPDATING_ENUM_STATUS...\n";
                    $db->exec("ALTER TABLE orders MODIFY COLUMN status ENUM('pending','confirmed','processing','dispatched','delivered','cancelled','return_requested','returned') DEFAULT 'pending'");
                    echo "ENUM_UPDATED\n";
                }
            }
        }
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
