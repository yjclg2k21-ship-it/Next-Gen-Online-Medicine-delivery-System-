-- Migration: Enhance Prescription Workflow
-- Features: Expiry check, auto-link medicines, reuse prevention
-- Date: 2026-05-28

-- 1. Add expiry_date and usage tracking to prescriptions table
ALTER TABLE `prescriptions` 
    ADD COLUMN `expiry_date` DATE NULL AFTER `doctor_name`,
    ADD COLUMN `issued_date` DATE NULL AFTER `doctor_name`,
    ADD COLUMN `times_used` INT DEFAULT 0 AFTER `status`,
    ADD COLUMN `max_uses` INT DEFAULT 1 AFTER `times_used`,
    ADD COLUMN `last_used_at` TIMESTAMP NULL AFTER `max_uses`,
    ADD COLUMN `reviewed_by` INT NULL AFTER `comment`,
    ADD COLUMN `reviewed_at` TIMESTAMP NULL AFTER `reviewed_by`,
    ADD COLUMN `vendor_id` INT NULL AFTER `user_id`;

-- 2. Create prescription_medicines table (links detected medicines to a prescription)
CREATE TABLE IF NOT EXISTS `prescription_medicines` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `prescription_id` INT NOT NULL,
    `medicine_id` INT NULL,
    `medicine_name` VARCHAR(255) NOT NULL,
    `dosage` VARCHAR(100) NULL,
    `quantity` INT DEFAULT 1,
    `added_to_cart` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`medicine_id`) REFERENCES `medicines`(`id`) ON DELETE SET NULL,
    INDEX `idx_rx_medicines` (`prescription_id`, `medicine_id`)
) ENGINE=InnoDB;

-- 3. Create prescription_usage_log (tracks each time a prescription is used for an order)
CREATE TABLE IF NOT EXISTS `prescription_usage_log` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `prescription_id` INT NOT NULL,
    `order_id` INT NOT NULL,
    `used_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_rx_order` (`prescription_id`, `order_id`)
) ENGINE=InnoDB;

-- 4. Add prescription_id to cart_items for linking
ALTER TABLE `cart_items`
    ADD COLUMN `prescription_id` INT NULL,
    ADD COLUMN `prescription_medicine_id` INT NULL;
