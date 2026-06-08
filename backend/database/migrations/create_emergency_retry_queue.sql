-- Migration: Create emergency_retry_queue table
-- Feature: Emergency Order Priority System
-- Requirement: 1.7 - Priority retry queue for unassigned emergency orders
-- Description: Stores emergency orders that could not be assigned to a delivery partner,
--              enabling automatic retry every 60 seconds for a maximum of 5 attempts.

CREATE TABLE IF NOT EXISTS `emergency_retry_queue` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `attempt_count` INT DEFAULT 0,
    `max_attempts` INT DEFAULT 5,
    `last_attempt_at` DATETIME NULL,
    `next_attempt_at` DATETIME NULL,
    `status` ENUM('pending', 'assigned', 'exhausted') DEFAULT 'pending',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_order_id` (`order_id`),
    INDEX `idx_pending` (`status`, `next_attempt_at`),
    CONSTRAINT `fk_retry_queue_order` FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
