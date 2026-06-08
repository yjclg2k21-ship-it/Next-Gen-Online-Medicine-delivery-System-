<?php
require 'backend/app/bootstrap.php';
$db = App\Core\Database::getInstance()->getConnection();

// Seed Categories
$db->exec("INSERT IGNORE INTO categories (id, name, slug) VALUES 
(1, 'Antibiotics', 'antibiotics'),
(2, 'Chronic Care', 'chronic'),
(3, 'OTC Products', 'otc')");

// Seed Medicines
$db->exec("INSERT IGNORE INTO medicines (id, vendor_id, category_id, name, slug, salt, price, stock, approval_status) VALUES 
(1, 2, 1, 'Amoxicillin 500mg', 'amoxicillin-500', 'Amoxicillin', 450.00, 100, 'approved'),
(2, 2, 2, 'Metformin 850mg', 'metformin-850', 'Metformin', 120.00, 200, 'approved'),
(3, 2, 3, 'Paracetamol 650mg', 'paracetamol-650', 'Paracetamol', 45.00, 500, 'approved')");

// Seed an Order for user 3
$db->exec("INSERT IGNORE INTO orders (id, user_id, vendor_id, delivery_method_id, total_amount, grand_total, status, payment_status, created_at) VALUES 
(1, 3, 2, 1, 450.00, 500.00, 'confirmed', 'paid', NOW())");

$db->exec("INSERT IGNORE INTO order_items (order_id, medicine_id, quantity, price, subtotal) VALUES 
(1, 1, 1, 450.00, 450.00)");

$db->exec("INSERT IGNORE INTO order_status_logs (order_id, status, created_at) VALUES 
(1, 'placed', NOW()),
(1, 'confirmed', NOW())");

echo "Seeding completed successfully.\n";
