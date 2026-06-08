-- Migration: Create emergency_assignment_log table
-- Feature: Emergency Order Priority System
-- Requirements: 1.6 (Assignment metadata recording), 1.8 (Timeout-based reassignment tracking)
-- Description: Logs all emergency order assignment events including initial assignments,
--              reassignments, and retries with partner distance and SLA deadline metadata.

CREATE TABLE IF NOT EXISTS `emergency_assignment_log` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `partner_id` INT NOT NULL,
    `assignment_timestamp` DATETIME NOT NULL,
    `rider_distance_km` DECIMAL(6,2) NOT NULL,
    `sla_deadline` DATETIME NOT NULL,
    `vendor_lat` DECIMAL(10,7) NULL,
    `vendor_lng` DECIMAL(10,7) NULL,
    `partner_lat` DECIMAL(10,7) NULL,
    `partner_lng` DECIMAL(10,7) NULL,
    `assignment_type` ENUM('initial', 'reassignment', 'retry') DEFAULT 'initial',
    `status` ENUM('assigned', 'accepted', 'timeout', 'revoked') DEFAULT 'assigned',
    `accepted_at` DATETIME NULL,
    `revoked_at` DATETIME NULL,
    `revoke_reason` VARCHAR(255) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_order` (`order_id`),
    INDEX `idx_partner` (`partner_id`),
    INDEX `idx_status` (`status`),
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`partner_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
