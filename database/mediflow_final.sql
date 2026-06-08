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
    `status` ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    `is_verified` TINYINT(1) DEFAULT 0,
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
    `is_default` TINYINT(1) DEFAULT 0,
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
    `owner_name` VARCHAR(255),
    `license_number` VARCHAR(100),
    `store_image` VARCHAR(255),
    `opening_time` TIME,
    `closing_time` TIME,
    `address` TEXT,
    `bank_name` VARCHAR(100),
    `account_number` VARCHAR(50),
    `ifsc_code` VARCHAR(20),
    `latitude` DECIMAL(10,8),
    `longitude` DECIMAL(11,8),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `delivery_profiles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `vehicle_type` ENUM('cycle', 'bike', 'scooter', 'car'),
    `vehicle_number` VARCHAR(50),
    `zone` VARCHAR(100),
    `license_number` VARCHAR(50),
    `bank_name` VARCHAR(100),
    `account_number` VARCHAR(50),
    `ifsc_code` VARCHAR(20),
    `current_address` TEXT,
    `permanent_address` TEXT,
    `status` ENUM('active', 'inactive') DEFAULT 'active',
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
    `status` ENUM('pending', 'confirmed', 'processing', 'dispatched', 'delivered', 'cancelled', 'returned') DEFAULT 'pending',
    `payment_status` ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    `payment_method` ENUM('card', 'upi', 'cod', 'wallet') DEFAULT 'card',
    `pickup_otp` VARCHAR(6) NULL,
    `delivery_otp` VARCHAR(6) NULL,
    `idempotency_key` VARCHAR(255) NULL,
    `assigned_at` TIMESTAMP NULL,
    `sla_breach` TINYINT(1) DEFAULT 0,
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
);

-- 5. COMPLIANCE & RECOGNITION
CREATE TABLE IF NOT EXISTS `prescriptions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `order_id` INT NULL,
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
    `reason` TEXT,
    `status` ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
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
-- ============================================================
-- MEDIMITRA DEMO SEEDS (CONSOLIDATED)
-- Data Parity Version: Initialized with RBAC, Catalog & Operations
-- ============================================================

USE `medicine_delivery`;

-- 1. RBAC (Roles)
INSERT IGNORE INTO `roles` (`id`, `name`, `display_name`, `description`) VALUES 
(1, 'admin', 'System Administrator', 'Full access to clinical and personnel nodes.'),
(2, 'vendor', 'Pharmacy Vendor', 'Access to inventory and order fulfillment.'),
(3, 'delivery', 'Logistics Partner', 'Access to dispatch queue and route telemetry.'),
(4, 'user', 'Patient / Customer', 'Access to storefront and personal health records.');

-- 2. PERMISSIONS (Sample)
INSERT IGNORE INTO `permissions` (`id`, `name`, `slug`, `group`) VALUES
(1, 'Manage Users', 'manage-users', 'admin'),
(2, 'Manage Inventory', 'manage-inventory', 'vendor'),
(3, 'Fulfill Orders', 'fulfill-orders', 'vendor'),
(4, 'Dispatch Orders', 'dispatch-orders', 'delivery');

-- 3. DEMO USERS (Password: password123)
-- Hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
INSERT IGNORE INTO `users` (`id`, `name`, `email`, `password`, `role`, `phone`, `status`) VALUES
(1, 'Admin Super', 'admin@medimitra.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '9000000001', 'active'),
(2, 'City Medicals', 'vendor@medimitra.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'vendor', '9000000002', 'active'),
(3, 'Patient John', 'user@medimitra.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', '9000000003', 'active'),
(4, 'Suresh Delivery', 'delivery@medimitra.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'delivery', '9000000004', 'active');

-- 4. CATEGORIES (The 17-Pillar Clinical Tree)
INSERT IGNORE INTO `categories` (`id`, `name`, `slug`, `image`, `status`) VALUES
(1, 'Tablets & Capsules', 'tablets-capsules', '/assets/images/medicines/tablet.png', 1),
(2, 'Syrups & Liquids', 'syrups-liquids', '/assets/images/medicines/syrup.png', 1),
(3, 'Oral Drops', 'oral-drops', '/assets/images/medicines/syrup.png', 1),
(4, 'Topicals (Creams, Gels)', 'topicals', '/assets/images/medicines/ointment.png', 1),
(5, 'Injections & Critical Care', 'injections-critical', '/assets/images/medicines/device.png', 1),
(6, 'Inhalers & Respiratory', 'inhalers-respiratory', '/assets/images/medicines/device.png', 1),
(7, 'Eye Care', 'eye-care', '/assets/images/medicines/ointment.png', 1),
(8, 'Ear & Nasal Products', 'ear-nasal', '/assets/images/medicines/syrup.png', 1),
(9, 'Medical Devices', 'medical-devices', '/assets/images/medicines/device.png', 1),
(10, 'Diagnostic Kits', 'diagnostic-kits', '/assets/images/medicines/device.png', 1),
(11, 'Surgical Supplies', 'surgical-supplies', '/assets/images/medicines/device.png', 1),
(12, 'First Aid', 'first-aid', '/assets/images/medicines/herbal.png', 1),
(13, 'Health Supplements & OTC', 'health-supplements-otc', '/assets/images/medicines/tablet.png', 1),
(14, 'Ayurvedic & Herbal', 'ayurvedic-herbal', '/assets/images/medicines/herbal.png', 1),
(15, 'Homeopathy', 'homeopathy', '/assets/images/medicines/herbal.png', 1),
(16, 'Personal Care & Hygiene', 'personal-care-hygiene', '/assets/images/medicines/ointment.png', 1),
(17, 'Other / Miscellaneous', 'other-miscellaneous', '/assets/images/medicines/tablet.png', 1);

-- 5. BRANDS
INSERT IGNORE INTO `brands` (`id`, `name`, `slug`, `status`) VALUES
(1, 'Cipla', 'cipla', 1),
(2, 'Sun Pharma', 'sun-pharma', 1),
(3, 'Abbott', 'abbott', 1),
(4, 'Mankind', 'mankind', 1);

-- 6. DELIVERY METHODS (Tiered Logic)
INSERT IGNORE INTO `delivery_methods` (`id`, `name`, `display_name`, `price`, `estimated_time`, `priority`, `sla_hours`) VALUES
(1, 'standard', 'Standard Delivery', 30.00, '2-3 Days', 1, 72),
(2, 'express', 'Express Delivery', 80.00, 'Same Day', 2, 24),
(3, 'emergency', 'Emergency Delivery', 150.00, '2 Hours', 3, 2);

-- 7. MEDICINES (Full Catalog - 36 Units for 100% Category Coverage)
INSERT IGNORE INTO `medicines` (`id`, `vendor_id`, `category_id`, `brand_id`, `name`, `slug`, `salt`, `price`, `mrp`, `stock`, `approval_status`, `image`) VALUES
-- 1. Tablets & Capsules
(1, 2, 1, 1, 'Paracetamol 500mg', 'paracetamol-500mg', 'Paracetamol', 40.00, 50.00, 100, 'approved', '/assets/images/medicines/medicines/paracetamol-500mg.jpg'),
(2, 2, 1, 2, 'Aspirin 150mg', 'aspirin-150mg', 'Acetylsalicylic Acid', 25.00, 35.00, 200, 'approved', '/assets/images/medicines/medicines/aspirin-150mg.jpg'),
(3, 2, 1, 3, 'Cetirizine 10mg', 'cetirizine-10mg', 'Cetirizine', 30.00, 45.00, 300, 'approved', '/assets/images/medicines/tablet.png'),
-- 2. Syrups & Liquids
(4, 2, 2, 2, 'Cough Syrup EX', 'cough-syrup-ex', 'Dextromethorphan', 110.00, 140.00, 50, 'approved', '/assets/images/medicines/medicines/cough-syrup-ex.jpg'),
(5, 2, 2, 1, 'Antacid Oral Gel', 'antacid-gel', 'Magnesium Hydroxide', 120.00, 150.00, 60, 'approved', '/assets/images/medicines/medicines/antacid-gel.jpg'),
-- 3. Oral Drops
(6, 2, 3, 2, 'Pediatric Vitamin Drops', 'vitamin-drops', 'Multivitamin', 180.00, 220.00, 35, 'approved', '/assets/images/medicines/medicines/pediatric-vitamin-drops.jpg'),
(7, 2, 3, 1, 'Baby Gripe Water', 'gripe-water', 'Herbal Extracts', 95.00, 120.00, 50, 'approved', '/assets/images/medicines/syrup.png'),
-- 4. Topicals
(8, 2, 4, 3, 'Clotrimazole Cream', 'clotrimazole-cream', 'Antifungal', 85.00, 110.00, 80, 'approved', '/assets/images/medicines/medicines/clotrimazole-cream.jpg'),
(9, 2, 4, 4, 'Pain Relief Gel', 'pain-relief-gel', 'Diclofenac', 90.00, 120.00, 100, 'approved', '/assets/images/medicines/medicines/pain-relief-gel.jpg'),
-- 5. Injections
(10, 2, 5, 2, 'Insulin Glargine', 'insulin-glargine', 'Insulin', 650.00, 800.00, 25, 'approved', '/assets/images/medicines/medicines/insulin-glargine.jpg'),
(11, 2, 5, 4, 'Vitamin B12 Injection', 'b12-injection', 'Methylcobalamin', 150.00, 200.00, 15, 'approved', '/assets/images/medicines/medicines/vitamin-b12-injection.jpg'),
-- 6. Inhalers
(12, 2, 6, 1, 'Asthma Relief Inhaler', 'asthma-inhaler', 'Salbutamol', 320.00, 400.00, 45, 'approved', '/assets/images/medicines/medicines/asthma-relief-inhaler.jpg'),
(13, 2, 6, 2, 'Budesonide Inhaler', 'budesonide-inhaler', 'Budesonide', 450.00, 550.00, 20, 'approved', '/assets/images/medicines/device.png'),
-- 7. Eye Care
(14, 2, 7, 3, 'Lubricating Eye Drops', 'eye-drops', 'Carboxymethylcellulose', 140.00, 180.00, 40, 'approved', '/assets/images/medicines/medicines/lubricating-eye-drops.jpg'),
(15, 2, 7, 1, 'Cooling Eye Relief', 'cooling-eye', 'Naphazoline', 110.00, 140.00, 60, 'approved', '/assets/images/medicines/medicines/lubricating-eye-drops.jpg'),
-- 8. Ear & Nasal
(16, 2, 8, 1, 'Saline Nasal Drops', 'nasal-drops', 'Sodium Chloride', 55.00, 75.00, 90, 'approved', '/assets/images/medicines/medicines/saline-nasal-drops.jpg'),
(17, 2, 8, 2, 'Ear Wax Relief Drops', 'ear-wax-drops', 'Glycerin', 85.00, 110.00, 30, 'approved', '/assets/images/medicines/syrup.png'),
-- 9. Medical Devices
(18, 2, 9, 3, 'Digital Thermometer', 'digital-thermometer', 'Device', 250.00, 300.00, 15, 'approved', '/assets/images/medicines/medicines/digital-thermometer.png'),
(19, 2, 9, 4, 'Blood Pressure Monitor', 'bp-monitor', 'Electronic Device', 2400.00, 3000.00, 10, 'approved', '/assets/images/medicines/device.png'),
-- 10. Diagnostic Kits
(20, 2, 10, 3, 'Blood Glucose Monitor', 'blood-glucose-monitor', 'Diagnostic Kit', 1200.00, 1500.00, 20, 'approved', '/assets/images/medicines/medicines/blood-glucose-kit.png'),
(21, 2, 10, 4, 'Pregnancy Test Kit', 'pregnancy-kit', 'HCG Test', 75.00, 100.00, 300, 'approved', '/assets/images/medicines/device.png'),
-- 11. Surgical Supplies
(22, 2, 11, 1, 'Surgical Gloves (Pair)', 'surgical-gloves', 'Latex', 20.00, 30.00, 500, 'approved', '/assets/images/medicines/medicines/surgical-gloves.png'),
(23, 2, 11, 2, 'Sterile Cotton Roll', 'cotton-roll', 'Cotton', 45.00, 60.00, 100, 'approved', '/assets/images/medicines/ointment.png'),
-- 12. First Aid
(24, 2, 12, 4, 'First Aid Essential Kit', 'first-aid-kit', 'Emergency Care', 450.00, 600.00, 30, 'approved', '/assets/images/medicines/medicines/first-aid-kit.png'),
(25, 2, 12, 1, 'Antiseptic Liquid 500ml', 'antiseptic-liquid', 'Chloroxylenol', 180.00, 220.00, 80, 'approved', '/assets/images/medicines/syrup.png'),
-- 13. Health Supplements
(26, 2, 13, 1, 'Multivitamin Tablets', 'multivitamin-tabs', 'Vitamins & Minerals', 350.00, 450.00, 120, 'approved', '/assets/images/medicines/tablet.png'),
(27, 2, 13, 2, 'Omega-3 Capsules', 'omega3-caps', 'Fish Oil', 550.00, 700.00, 80, 'approved', '/assets/images/medicines/tablet.png'),
-- 14. Ayurvedic
(28, 2, 14, 4, 'Ashwagandha Extract', 'ashwagandha-extract', 'Herbal', 210.00, 250.00, 40, 'approved', '/assets/images/medicines/medicines/ashwagandha-extract.png'),
(29, 2, 14, 3, 'Tulsi Herbal Drops', 'tulsi-drops', 'Holy Basil', 130.00, 160.00, 200, 'approved', '/assets/images/medicines/herbal.png'),
-- 15. Homeopathy
(30, 2, 15, 2, 'Homeopathic Pellets', 'homeopathic-pellets', 'Clinical Formula', 180.00, 220.00, 60, 'approved', '/assets/images/medicines/medicines/homeopathic-pellets.png'),
(31, 2, 15, 1, 'Arnica Pellets', 'arnica-pellets', 'Arnica Montana', 140.00, 180.00, 50, 'approved', '/assets/images/medicines/homeopathic-pellets.png'),
-- 16. Personal Care
(32, 2, 16, 3, 'Advanced Hand Sanitizer', 'hand-sanitizer', 'Ethanol 70%', 95.00, 120.00, 150, 'approved', '/assets/images/medicines/medicines/hand-sanitizer.png'),
(33, 2, 16, 1, 'Medicated Bath Soap', 'medicated-soap', 'Antiseptic', 45.00, 60.00, 200, 'approved', '/assets/images/medicines/ointment.png'),
-- 17. Other
(34, 2, 17, 3, '3-Ply Surgical Mask', 'face-mask', 'Protection', 10.00, 15.00, 1000, 'approved', '/assets/images/medicines/device.png'),
(35, 2, 17, 4, 'Adhesive Medical Tape', 'medical-tape', 'Zinc Oxide', 35.00, 50.00, 100, 'approved', '/assets/images/medicines/ointment.png'),
(36, 2, 1, 4, 'Amoxicillin 500mg', 'amoxicillin-500mg', 'Amoxicillin', 120.00, 160.00, 200, 'approved', '/assets/images/medicines/tablet.png'),
(37, 2, 9, 1, 'Atenolol 50mg', 'atenolol-50mg', 'Atenolol', 65.00, 85.00, 150, 'approved', '/assets/images/medicines/atenolol-50mg.png'),
(38, 2, 15, 2, 'Sertraline 50mg', 'sertraline-50mg', 'Sertraline', 450.00, 550.00, 40, 'approved', '/assets/images/medicines/sertraline-50mg.png');

-- 8. MARKETING (Banners)
INSERT IGNORE INTO `banners` (`title`, `image_url`, `status`) VALUES
('Express Delivery Now Live!', '/assets/images/banners/express.png', 'active'),
('Save 20% on Health Supplements', '/assets/images/banners/sale.png', 'active');

-- 9. SYSTEM SETTINGS
INSERT IGNORE INTO `settings` (`key_name`, `value`) VALUES
('site_name', 'MediMitra'),
('emergency_contact', '+91 90000 00000'),
('free_delivery_threshold', '1000.00'),
('vendor_commission_percent', '10.0');

COMMIT;
