-- ============================================================
-- MEDIMITRA UNIFIED SCHEMA (CLINICAL PARITY VERSION)
-- ============================================================

CREATE DATABASE IF NOT EXISTS `medicine_delivery`;
USE `medicine_delivery`;

-- 1. RBAC & PERMISSIONS
CREATE TABLE IF NOT EXISTS `roles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) UNIQUE NOT NULL,
    `display_name` VARCHAR(100),
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `permissions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) UNIQUE NOT NULL,
    `group` VARCHAR(50),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `role_permissions` (
    `role_id` INT,
    `permission_id` INT,
    PRIMARY KEY (`role_id`, `permission_id`),
    FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
);

-- 2. IDENTITY (USERS & LOGISTICS)
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'vendor', 'user', 'delivery') DEFAULT 'user',
    `phone` VARCHAR(20),
    `wallet_balance` DECIMAL(10,2) DEFAULT 0.00,
    `status` ENUM('active', 'inactive', 'suspended', 'pending', 'blocked') DEFAULT 'active',
    `fcm_token` VARCHAR(255) NULL,
    `is_verified` TINYINT(1) DEFAULT 0,
    `latitude` DECIMAL(10, 8) NULL,
    `longitude` DECIMAL(11, 8) NULL,
    `is_online` BOOLEAN DEFAULT 0,
    `last_idle_since` TIMESTAMP NULL,
    `is_non_responsive` BOOLEAN DEFAULT 0,
    `emergency_locked_order_id` INT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL
);

CREATE TABLE IF NOT EXISTS `addresses` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `label` VARCHAR(50) DEFAULT 'Home',
    `address_line1` TEXT NOT NULL,
    `address_line2` TEXT,
    `city` VARCHAR(50),
    `state` VARCHAR(50),
    `pincode` VARCHAR(10),
    `latitude` DECIMAL(10, 8) NULL,
    `longitude` DECIMAL(11, 8) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `user_profiles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `dob` DATE,
    `gender` ENUM('male', 'female', 'other'),
    `blood_group` VARCHAR(5),
    `emergency_contact` VARCHAR(20),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `vendor_profiles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `pharmacy_name` VARCHAR(255),
    `license_number` VARCHAR(100),
    `store_image` VARCHAR(255),
    `opening_time` TIME,
    `closing_time` TIME,
    `address` TEXT,
    `latitude` DECIMAL(10, 8) NULL,
    `longitude` DECIMAL(11, 8) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `delivery_profiles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `vehicle_type` ENUM('cycle', 'bike', 'scooter', 'car'),
    `vehicle_number` VARCHAR(50),
    `license_number` VARCHAR(50),
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `payout_method` VARCHAR(50) DEFAULT 'UPI',
    `payout_details` TEXT NULL,
    `cash_in_hand` DECIMAL(10,2) DEFAULT 0.00,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- 3. CATALOG & INVENTORY
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) UNIQUE NOT NULL,
    `image` VARCHAR(255),
    `status` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `brands` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `type` ENUM('Generic', 'MNC', 'Ayurvedic', 'Domestic', 'Local') DEFAULT 'Generic',
    `slug` VARCHAR(100) UNIQUE NOT NULL,
    `status` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `medicines` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `vendor_id` INT NOT NULL,
    `category_id` INT NULL,
    `brand_id` INT NULL,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) UNIQUE NOT NULL,
    `salt` VARCHAR(255),
    `price` DECIMAL(10,2) NOT NULL,
    `mrp` DECIMAL(10,2),
    `stock` INT DEFAULT 0,
    `expiry_date` DATE NULL,
    `image` VARCHAR(255),
    `requires_prescription` TINYINT(1) DEFAULT 0,
    `approval_status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    `vetting_reason` TEXT,
    `vetting_note` TEXT, -- Detailed clinical note for vendor
    `vetted_at` TIMESTAMP NULL,
    `vetted_by` INT NULL,
    `status` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    FOREIGN KEY (`vendor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`brand_id`) REFERENCES `brands`(`id`) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS `inventory_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `medicine_id` INT NOT NULL,
    `vendor_id` INT NOT NULL,
    `change_qty` INT NOT NULL,
    `reason` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`medicine_id`) REFERENCES `medicines`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`vendor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- 4. LOGISTICS & OPERATIONS
CREATE TABLE IF NOT EXISTS `delivery_methods` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL,
    `display_name` VARCHAR(100),
    `price` DECIMAL(10,2) DEFAULT 0.00,
    `estimated_time` VARCHAR(100),
    `priority` INT DEFAULT 1,
    `sla_hours` INT DEFAULT 24
);

CREATE TABLE IF NOT EXISTS `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `vendor_id` INT NOT NULL,
    `delivery_partner_id` INT NULL, -- Assigned delivery agent
    `delivery_method_id` INT NULL,
    `address_id` INT NULL,
    `prescription_id` INT NULL,
    `total_amount` DECIMAL(10,2) NOT NULL,
    `delivery_fee` DECIMAL(10,2) DEFAULT 0.00,
    `grand_total` DECIMAL(10,2) NOT NULL,
    `is_emergency` TINYINT(1) DEFAULT 0, -- Urgent delivery tier
    `sla_deadline` TIMESTAMP NULL,
    `status` ENUM('pending', 'confirmed', 'processing', 'dispatched', 'delivered', 'cancelled', 'returned') DEFAULT 'pending',
    `payment_status` ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    `payment_method` ENUM('card', 'upi', 'cod', 'wallet') DEFAULT 'card',
    `idempotency_key` VARCHAR(255) NULL,
    `assigned_at` TIMESTAMP NULL,
    `sla_breach` TINYINT(1) DEFAULT 0,
    `emergency_acknowledged_at` TIMESTAMP NULL,
    `emergency_ready_at` TIMESTAMP NULL,
    `emergency_picked_up_at` TIMESTAMP NULL,
    `emergency_delivered_at` TIMESTAMP NULL,
    `emergency_locked` BOOLEAN DEFAULT 0,
    `notes` TEXT,
    `delivery_otp` VARCHAR(10) NULL,
    `pickup_otp` VARCHAR(10) NULL,
    `proof_image` VARCHAR(255) NULL,
    `failure_reason` TEXT NULL,
    `failure_at` TIMESTAMP NULL,
    `rejection_reason` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
    FOREIGN KEY (`vendor_id`) REFERENCES `users`(`id`),
    FOREIGN KEY (`delivery_partner_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`delivery_method_id`) REFERENCES `delivery_methods`(`id`)
);

CREATE TABLE IF NOT EXISTS `order_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `medicine_id` INT NOT NULL,
    `quantity` INT NOT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `subtotal` DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `order_status_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `status` VARCHAR(50) NOT NULL,
    `comment` TEXT,
    `changed_by` INT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
);

-- 5. COMPLIANCE & RECOGNITION
CREATE TABLE IF NOT EXISTS `prescriptions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `doctor_name` VARCHAR(100),
    `image_path` VARCHAR(255) NOT NULL,
    `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    `comment` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `return_requests` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `order_id` INT NOT NULL,
    `rider_id` INT NULL,
    `reason` TEXT,
    `status` ENUM('pending', 'approved', 'rejected', 'assigned_for_pickup', 'picked_up', 'completed') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`rider_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
);

-- 5.1 Prescription Reviews (Parity with Prescription::getReviews)
CREATE TABLE IF NOT EXISTS `prescription_reviews` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `prescription_id` INT NOT NULL,
    `vendor_id` INT NOT NULL,
    `status` ENUM('approved', 'rejected') NOT NULL,
    `comment` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (`prescription_id`),
    INDEX (`vendor_id`),
    FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`vendor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `kyc_documents` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `document_type` ENUM('aadhar', 'license', 'pharmacy_license', 'gst') NOT NULL,
    `document_number` VARCHAR(50) NOT NULL,
    `document_image` VARCHAR(255) NOT NULL,
    `status` ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    `rejection_reason` TEXT,
    `verified_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `audit_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `action` VARCHAR(100) NOT NULL,
    `target_model` VARCHAR(100),
    `target_id` INT,
    `payload` JSON,
    `metadata` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 6. FINANCIALS & PAYOUTS
CREATE TABLE IF NOT EXISTS `vendor_payouts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `vendor_id` INT NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `status` ENUM('pending', 'processed', 'failed') DEFAULT 'pending',
    `reference_id` VARCHAR(100),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`vendor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `vendor_commissions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `vendor_id` INT NOT NULL,
    `order_id` INT NOT NULL,
    `commission_amount` DECIMAL(10,2) NOT NULL,
    `status` ENUM('pending', 'settled') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`vendor_id`) REFERENCES `users`(`id`),
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`)
);

CREATE TABLE IF NOT EXISTS `wallet_transactions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `type` ENUM('credit', 'debit') NOT NULL,
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `payments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `rider_id` INT NULL,
    `user_id` INT NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `method` ENUM('card', 'upi', 'cod', 'wallet') NOT NULL,
    `transaction_id` VARCHAR(100),
    `status` ENUM('pending', 'success', 'failed') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
);

CREATE TABLE IF NOT EXISTS `refunds` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `payment_id` INT NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `reason` TEXT,
    `status` ENUM('pending', 'processed', 'failed') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`payment_id`) REFERENCES `payments`(`id`)
);

-- 7. COMMUNICATIONS & TICKETING
CREATE TABLE IF NOT EXISTS `notifications` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `is_read` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `support_tickets` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `status` ENUM('open', 'in_progress', 'resolved', 'closed') DEFAULT 'open',
    `priority` ENUM('low', 'medium', 'high') DEFAULT 'medium',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `support_ticket_messages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ticket_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `message` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
);

-- 8. MARKETING (BANNERS & NEWSLETTER)
CREATE TABLE IF NOT EXISTS `banners` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255),
    `image_url` VARCHAR(255) NOT NULL,
    `link` VARCHAR(255),
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `newsletter_subscribers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `status` ENUM('active', 'unsubscribed') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `newsletter_campaigns` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `subject` VARCHAR(255) NOT NULL,
    `body` TEXT NOT NULL,
    `sent_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 9. PHARMACY OPS & EXTENSIONS
CREATE TABLE IF NOT EXISTS `refill_reminders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `medicine_id` INT NOT NULL,
    `last_order_date` DATE NOT NULL,
    `duration_days` INT NOT NULL,
    `next_reminder_date` DATE NOT NULL,
    `status` ENUM('active', 'inactive', 'ordered') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`medicine_id`) REFERENCES `medicines`(`id`) ON DELETE CASCADE
);

-- REMOVED: health_vault table (feature removed - scope creep)
-- CREATE TABLE IF NOT EXISTS `health_vault` (
--     `id` INT AUTO_INCREMENT PRIMARY KEY,
--     `user_id` INT NOT NULL,
--     `title` VARCHAR(255) NOT NULL,
--     `file_path` VARCHAR(255) NOT NULL,
--     `type` ENUM('prescription', 'report', 'other') DEFAULT 'prescription',
--     `doctor_name` VARCHAR(255),
--     `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
-- );

-- 10. AI & CLINICAL LOGS
CREATE TABLE IF NOT EXISTS `pos_sales` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `vendor_id` INT NOT NULL,
    `customer_name` VARCHAR(100),
    `customer_phone` VARCHAR(20),
    `seller_name` VARCHAR(100) NULL,
    `total_amount` DECIMAL(10,2) NOT NULL,
    `payment_method` ENUM('cash', 'card', 'upi') DEFAULT 'cash',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`vendor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `pos_sale_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `pos_sale_id` INT NOT NULL,
    `medicine_id` INT NOT NULL,
    `quantity` INT NOT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `subtotal` DECIMAL(10,2) NOT NULL,
    INDEX (`pos_sale_id`),
    INDEX (`medicine_id`),
    FOREIGN KEY (`pos_sale_id`) REFERENCES `pos_sales`(`id`) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS `wishlists` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `medicine_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (`user_id`, `medicine_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`medicine_id`) REFERENCES `medicines`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `cart_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `medicine_id` INT NOT NULL,
    `quantity` INT DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`medicine_id`) REFERENCES `medicines`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `medicine_substitutes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `medicine_id` INT NOT NULL,
    `substitute_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`medicine_id`) REFERENCES `medicines`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`substitute_id`) REFERENCES `medicines`(`id`) ON DELETE CASCADE
);

-- 10. REVIEWS & FEEDBACK
CREATE TABLE IF NOT EXISTS `reviews` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `order_id` INT NULL,
    `medicine_id` INT NOT NULL,
    `rating` TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    `comment` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
    FOREIGN KEY (`medicine_id`) REFERENCES `medicines`(`id`)
);

-- 11. SYSTEM & JOBS
CREATE TABLE IF NOT EXISTS `worker_jobs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `queue` VARCHAR(50) DEFAULT 'default',
    `payload` JSON NOT NULL,
    `attempts` TINYINT DEFAULT 0,
    `reserved_at` TIMESTAMP NULL,
    `available_at` TIMESTAMP NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `failed_jobs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `connection` TEXT NOT NULL,
    `queue` TEXT NOT NULL,
    `payload` LONGTEXT NOT NULL,
    `exception` LONGTEXT NOT NULL,
    `failed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `key_name` VARCHAR(100) UNIQUE NOT NULL,
    `value` TEXT,
    `description` TEXT,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `backups` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `filename` VARCHAR(255) NOT NULL,
    `size_mb` DECIMAL(10,2),
    `path` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `password_resets` (
    `email` VARCHAR(100) NOT NULL,
    `token` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (`email`),
    INDEX (`token`)
);

-- 12. CLINICAL STREAMS & ANALYTICS
CREATE TABLE IF NOT EXISTS `health_metrics` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `metric_type` VARCHAR(50) NOT NULL, -- blood_pressure, glucose, heart_rate, weight
    `value_1` DECIMAL(10,2) NOT NULL,
    `value_2` DECIMAL(10,2) NULL, -- Optional (e.g., diastolic BP)
    `unit` VARCHAR(20),
    `reading_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `clinical_recommendations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `medicine_id` INT NULL,
    `category_id` INT NULL,
    `score` DECIMAL(5,2) DEFAULT 0.00, -- Confidence score
    `reason` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`medicine_id`) REFERENCES `medicines`(`id`) ON DELETE CASCADE
);

-- 13. AI SAFETY & CLINICAL ALERTS
CREATE TABLE IF NOT EXISTS `drug_interactions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `medicine_a_id` INT NOT NULL,
    `medicine_b_id` INT NOT NULL,
    `severity` ENUM('low', 'moderate', 'severe') NOT NULL,
    `warning_text` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`medicine_a_id`) REFERENCES `medicines`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`medicine_b_id`) REFERENCES `medicines`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `safety_alerts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `order_id` INT NULL,
    `alert_type` ENUM('interaction', 'dosage', 'allergy') NOT NULL,
    `severity` ENUM('low', 'medium', 'high', 'critical') NOT NULL,
    `message` TEXT NOT NULL,
    `is_acknowledged` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE SET NULL
);

-- 14. LOGISTICS & SUBSCRIPTION ENHANCEMENTS
CREATE TABLE IF NOT EXISTS `delivery_payouts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `rider_id` INT NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `status` ENUM('pending', 'processed', 'failed') DEFAULT 'pending',
    `requested_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `processed_at` TIMESTAMP NULL,
    FOREIGN KEY (`rider_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `newsletter_segments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `query_logic` TEXT, -- Simplified segment logic storage
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `delivery_reviews` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `order_id` INT NOT NULL UNIQUE,
    `rider_id` INT NOT NULL,
    `rating` TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    `comment` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`rider_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- Security: Token Blacklist for persistent logouts

CREATE TABLE IF NOT EXISTS revoked_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    token_hash VARCHAR(64) NOT NULL,
    revoked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    INDEX (token_hash)
) ENGINE=InnoDB;

-- Security: Token Blacklist for persistent logouts
CREATE TABLE IF NOT EXISTS revoked_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    token_hash VARCHAR(64) NOT NULL,
    revoked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    INDEX (token_hash)
) ENGINE=InnoDB;

-- Logistics: Real-time agent tracking
CREATE TABLE IF NOT EXISTS agent_locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    agent_id INT NOT NULL,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (agent_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- MISSING CORE NODES (RECONCILIATION)
-- ============================================================

CREATE TABLE IF NOT EXISTS prescriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type ENUM('image', 'pdf') NOT NULL,
    doctor_name VARCHAR(255),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS kyc_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    document_type VARCHAR(50) NOT NULL,
    document_number VARCHAR(100) NOT NULL,
    document_image VARCHAR(255),
    status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    rejection_reason TEXT,
    verified_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    target_type VARCHAR(50),
    target_id INT,
    metadata JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS vendor_commissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendor_id INT NOT NULL,
    order_id INT NOT NULL,
    commission_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'settled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vendor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS vendor_payouts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendor_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processed', 'failed') DEFAULT 'pending',
    requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_at TIMESTAMP NULL,
    FOREIGN KEY (vendor_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS return_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    user_id INT NOT NULL,
    reason TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS refill_reminders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    medicine_id INT NOT NULL,
    last_order_date DATE NOT NULL,
    duration_days INT NOT NULL,
    next_reminder_date DATE NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (medicine_id) REFERENCES medicines(id) ON DELETE CASCADE
) ENGINE=InnoDB;
