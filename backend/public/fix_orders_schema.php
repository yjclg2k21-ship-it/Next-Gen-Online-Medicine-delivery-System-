<?php
require 'C:/xampp/htdocs/Next Gen Online Medicine Delivery System/backend/app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();
try {
    $db->exec("ALTER TABLE orders ADD COLUMN reassignment_count INT DEFAULT 0");
    echo "Column reassignment_count added successfully.";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
