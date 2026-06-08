<?php
require 'C:/xampp/htdocs/Next Gen Online Medicine Delivery System/backend/app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();

try {
    $db->exec("ALTER TABLE addresses ADD COLUMN latitude DECIMAL(10, 8) NULL AFTER pincode;");
    echo "Added latitude. ";
} catch(Exception $e) {
    echo "Latitude error (may already exist). ";
}

try {
    $db->exec("ALTER TABLE addresses ADD COLUMN longitude DECIMAL(11, 8) NULL AFTER latitude;");
    echo "Added longitude. ";
} catch(Exception $e) {
    echo "Longitude error (may already exist). ";
}
