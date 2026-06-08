-- ============================================================
-- Migration: Fix Column Mismatches
-- Date: 2025-01-01
-- Description: Add missing columns referenced in PHP controllers
--              but absent from the database schema.
-- Note: If re-running, "Duplicate column" errors can be safely ignored.
-- ============================================================

-- ============================================================
-- 1. delivery_profiles — add missing columns
-- ============================================================
ALTER TABLE delivery_profiles ADD COLUMN zone VARCHAR(100) NULL;
ALTER TABLE delivery_profiles ADD COLUMN aadhaar_number VARCHAR(20) NULL;
ALTER TABLE delivery_profiles ADD COLUMN pan_number VARCHAR(20) NULL;
ALTER TABLE delivery_profiles ADD COLUMN bank_name VARCHAR(100) NULL;
ALTER TABLE delivery_profiles ADD COLUMN account_number VARCHAR(50) NULL;
ALTER TABLE delivery_profiles ADD COLUMN ifsc_code VARCHAR(20) NULL;
ALTER TABLE delivery_profiles ADD COLUMN current_address TEXT NULL;
ALTER TABLE delivery_profiles ADD COLUMN permanent_address TEXT NULL;

-- ============================================================
-- 2. vendor_profiles — add missing columns
-- ============================================================
ALTER TABLE vendor_profiles ADD COLUMN pharmacist_reg_number VARCHAR(100) NULL;
ALTER TABLE vendor_profiles ADD COLUMN pan_card_number VARCHAR(20) NULL;
ALTER TABLE vendor_profiles ADD COLUMN aadhaar_number VARCHAR(20) NULL;
ALTER TABLE vendor_profiles ADD COLUMN gst_number VARCHAR(20) NULL;
ALTER TABLE vendor_profiles ADD COLUMN owner_name VARCHAR(100) NULL;
ALTER TABLE vendor_profiles ADD COLUMN bank_name VARCHAR(100) NULL;
ALTER TABLE vendor_profiles ADD COLUMN account_number VARCHAR(50) NULL;
ALTER TABLE vendor_profiles ADD COLUMN ifsc_code VARCHAR(20) NULL;

-- ============================================================
-- 3. orders — add missing delivered_at column
-- ============================================================
ALTER TABLE orders ADD COLUMN delivered_at TIMESTAMP NULL;

-- ============================================================
-- 4. categories — add missing parent_id and sub_categories columns
-- ============================================================
ALTER TABLE categories ADD COLUMN parent_id INT NULL;
ALTER TABLE categories ADD COLUMN sub_categories JSON NULL;
ALTER TABLE categories ADD INDEX idx_categories_parent (parent_id);
ALTER TABLE categories ADD CONSTRAINT fk_categories_parent
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL;

-- ============================================================
-- 5. addresses — add missing is_default column
-- ============================================================
ALTER TABLE addresses ADD COLUMN is_default TINYINT(1) DEFAULT 0;

-- ============================================================
-- 6. settings — rename key_name to key (code consistently uses `key`)
-- ============================================================
ALTER TABLE settings CHANGE `key_name` `key` VARCHAR(100) NOT NULL;
