<?php
require 'C:/xampp/htdocs/Next Gen Online Medicine Delivery System/backend/app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();

try {
    $stmt = $db->prepare("INSERT INTO medicines (name, slug, salt, vendor_id, category_id, brand_id, price, mrp, status, approval_status, stock, requires_prescription, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        'Lisinopril 20mg',
        'lisinopril-20mg',
        'Lisinopril',
        2, // vendor_id
        1, // category_id (Assuming 1 exists)
        1, // brand_id
        30.00,
        35.00,
        1,
        'approved',
        100,
        1,
        ''
    ]);
    echo "Lisinopril added to DB successfully.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
