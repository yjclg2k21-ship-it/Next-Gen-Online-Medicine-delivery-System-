<?php
require 'C:/xampp/htdocs/Next Gen Online Medicine Delivery System/backend/app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();
$db->exec("ALTER TABLE brands ADD COLUMN type ENUM('Generic', 'MNC', 'Ayurvedic', 'Domestic', 'Local') DEFAULT 'Generic' AFTER name;");
echo "Column added successfully";
