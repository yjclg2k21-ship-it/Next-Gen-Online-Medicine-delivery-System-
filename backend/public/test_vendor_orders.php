<?php
require 'C:/xampp/htdocs/Next Gen Online Medicine Delivery System/backend/app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();
$stmt = $db->query("SELECT id, vendor_id, grand_total, status, created_at FROM orders WHERE vendor_id = 2");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
