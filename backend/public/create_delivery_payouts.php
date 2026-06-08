<?php
require 'C:/xampp/htdocs/Next Gen Online Medicine Delivery System/backend/app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();

try {
    $db->exec("
        CREATE TABLE IF NOT EXISTS `delivery_payouts` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `rider_id` int(11) NOT NULL,
            `amount` decimal(10,2) NOT NULL,
            `status` enum('pending','processed','failed') DEFAULT 'pending',
            `processed_at` timestamp NULL DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`id`),
            FOREIGN KEY (`rider_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ");
    echo "delivery_payouts table created successfully.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
