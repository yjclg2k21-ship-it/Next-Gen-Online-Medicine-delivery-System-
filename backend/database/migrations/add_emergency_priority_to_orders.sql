-- Migration: Add emergency priority columns to orders table
-- Feature: Emergency Order Priority System
-- Requirements: 1.3 (SLA deadline), 1.6 (Assignment metadata)
-- Date: 2026-06-01
-- Description: Adds columns for SLA tracking, partner locking, emergency timestamps,
--              and retry/reassignment counters to support the emergency priority pipeline.

-- Add SLA deadline column (40 minutes from order creation for emergency orders)
ALTER TABLE `orders`
    ADD COLUMN `sla_deadline` DATETIME NULL AFTER `is_emergency`;

-- Add emergency lock flag (prevents partner from receiving other orders during emergency delivery)
ALTER TABLE `orders`
    ADD COLUMN `emergency_locked` TINYINT(1) DEFAULT 0 AFTER `sla_deadline`;

-- Note: `sla_breach` column already exists in the orders table, skipping.

-- Add emergency lifecycle timestamp columns
ALTER TABLE `orders`
    ADD COLUMN `emergency_acknowledged_at` DATETIME NULL AFTER `sla_breach`,
    ADD COLUMN `emergency_ready_at` DATETIME NULL AFTER `emergency_acknowledged_at`,
    ADD COLUMN `emergency_picked_up_at` DATETIME NULL AFTER `emergency_ready_at`,
    ADD COLUMN `emergency_delivered_at` DATETIME NULL AFTER `emergency_picked_up_at`;

-- Add assignment retry and reassignment counters
ALTER TABLE `orders`
    ADD COLUMN `assignment_retry_count` INT DEFAULT 0 AFTER `emergency_delivered_at`,
    ADD COLUMN `reassignment_count` INT DEFAULT 0 AFTER `assignment_retry_count`;

-- Add composite index for querying active emergency orders sorted by SLA deadline
-- Used by: PriorityEngine::evaluateActiveSLAs(), Admin emergency orders endpoint
ALTER TABLE `orders`
    ADD INDEX `idx_emergency_active` (`is_emergency`, `status`, `sla_deadline`);

-- Add composite index for checking partner lock status on emergency orders
-- Used by: PriorityEngine::findNearestEligiblePartner(), partner exclusivity checks
ALTER TABLE `orders`
    ADD INDEX `idx_emergency_partner` (`delivery_partner_id`, `emergency_locked`);
