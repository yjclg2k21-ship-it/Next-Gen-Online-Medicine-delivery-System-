-- Migration: Create emergency_escalations table
-- Description: Stores escalation records for emergency orders when SLA thresholds are breached
-- Requirements: 3.3 (Vendor preparation deadline escalation), 6.2 (SLA monitoring escalation), 6.4 (Breach audit logging)

CREATE TABLE IF NOT EXISTS `emergency_escalations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `escalation_level` ENUM('warning', 'critical', 'breach', 'manual_intervention') NOT NULL,
    `reason` VARCHAR(500) NOT NULL,
    `triggered_at` DATETIME NOT NULL,
    `resolved_at` DATETIME NULL,
    `resolved_by` INT NULL,
    `partner_id` INT NULL,
    `vendor_id` INT NULL,
    `remaining_minutes` DECIMAL(5,1) NULL,
    `action_taken` VARCHAR(255) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_order` (`order_id`),
    INDEX `idx_level` (`escalation_level`),
    INDEX `idx_unresolved` (`resolved_at`),
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
