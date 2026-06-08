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

-- 2. DEMO USERS (Password: password123)
-- Hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
INSERT IGNORE INTO `users` (`id`, `name`, `email`, `password`, `role`, `phone`, `status`) VALUES
(1, 'Admin Super', 'admin@medimitra.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '9000000001', 'active'),
(2, 'City Medicals', 'vendor@medimitra.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'vendor', '9000000002', 'active'),
(3, 'Patient John', 'user@medimitra.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', '9000000003', 'active'),
(4, 'Suresh Delivery', 'delivery@medimitra.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'delivery', '9000000004', 'active');

-- 3. ADDRESSES (Phaltan Taluka for all core users)
INSERT IGNORE INTO `addresses` (`user_id`, `label`, `address_line1`, `city`, `state`, `pincode`, `is_default`) VALUES
(1, 'Office', 'Main Market, Phaltan Taluka', 'Phaltan', 'Maharashtra', '415523', 1),
(2, 'Pharmacy', 'City Center, Phaltan Taluka', 'Phaltan', 'Maharashtra', '415523', 1),
(3, 'Home', 'Near Bus Stand, Phaltan Taluka', 'Phaltan', 'Maharashtra', '415523', 1),
(4, 'Hub', 'Logistics Node, Phaltan Taluka', 'Phaltan', 'Maharashtra', '415523', 1);

-- 4. CATEGORIES
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

-- 6. DELIVERY METHODS
INSERT IGNORE INTO `delivery_methods` (`id`, `name`, `display_name`, `price`, `estimated_time`, `priority`, `sla_hours`) VALUES
(1, 'standard', 'Standard Delivery', 30.00, '2-3 Days', 1, 72),
(2, 'express', 'Express Delivery', 80.00, 'Same Day', 2, 24),
(3, 'emergency', 'Emergency Delivery', 150.00, '2 Hours', 3, 2);

-- 7. MEDICINES
INSERT IGNORE INTO `medicines` (`id`, `vendor_id`, `category_id`, `brand_id`, `name`, `slug`, `salt`, `price`, `mrp`, `stock`, `approval_status`, `image`) VALUES
(1, 2, 1, 1, 'Paracetamol 500mg', 'paracetamol-500mg', 'Paracetamol', 40.00, 50.00, 100, 'approved', '/assets/images/medicines/medicines/paracetamol-500mg.jpg'),
(2, 2, 1, 2, 'Aspirin 150mg', 'aspirin-150mg', 'Acetylsalicylic Acid', 25.00, 35.00, 200, 'approved', '/assets/images/medicines/medicines/aspirin-150mg.jpg'),
(3, 2, 1, 3, 'Cetirizine 10mg', 'cetirizine-10mg', 'Cetirizine', 30.00, 45.00, 300, 'approved', '/assets/images/medicines/tablet.png'),
(4, 2, 2, 2, 'Cough Syrup EX', 'cough-syrup-ex', 'Dextromethorphan', 110.00, 140.00, 50, 'approved', '/assets/images/medicines/medicines/cough-syrup-ex.jpg'),
(5, 2, 2, 1, 'Antacid Oral Gel', 'antacid-gel', 'Magnesium Hydroxide', 120.00, 150.00, 60, 'approved', '/assets/images/medicines/medicines/antacid-gel.jpg'),
(6, 2, 3, 2, 'Pediatric Vitamin Drops', 'vitamin-drops', 'Multivitamin', 180.00, 220.00, 35, 'approved', '/assets/images/medicines/medicines/pediatric-vitamin-drops.jpg'),
(7, 2, 3, 1, 'Baby Gripe Water', 'gripe-water', 'Herbal Extracts', 95.00, 120.00, 50, 'approved', '/assets/images/medicines/syrup.png');

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
