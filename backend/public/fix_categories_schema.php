<?php
require 'C:/xampp/htdocs/Next Gen Online Medicine Delivery System/backend/app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();
try {
    $db->exec("ALTER TABLE categories ADD COLUMN description TEXT DEFAULT NULL");
    $db->exec("ALTER TABLE categories ADD COLUMN parent_id INT DEFAULT NULL");
    echo "Columns added successfully.";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
