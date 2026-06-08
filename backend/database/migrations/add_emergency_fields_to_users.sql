-- Migration: Add emergency delivery partner fields to users table
-- Requirements: 1.2 (Nearest eligible partner selection), 1.4 (Partner exclusivity during emergency)
-- Description: Adds columns for emergency order locking, idle tracking, and responsiveness status
--              for delivery partners. Also adds is_online column required for availability filtering.

-- Add is_online column (required for delivery partner availability tracking and index)
ALTER TABLE `users`
    ADD COLUMN `is_online` TINYINT(1) DEFAULT 0 AFTER `deleted_at`;

-- Add emergency delivery partner fields
ALTER TABLE `users`
    ADD COLUMN `emergency_locked_order_id` INT NULL AFTER `is_online`,
    ADD COLUMN `last_idle_since` DATETIME NULL AFTER `emergency_locked_order_id`,
    ADD COLUMN `is_non_responsive` TINYINT(1) DEFAULT 0 AFTER `last_idle_since`;

-- Add composite index for efficient delivery partner availability queries
-- Used by PriorityEngine to find available delivery partners:
-- role='delivery', is_online=1, emergency_locked_order_id IS NULL
ALTER TABLE `users`
    ADD INDEX `idx_delivery_available` (`role`, `is_online`, `emergency_locked_order_id`);
