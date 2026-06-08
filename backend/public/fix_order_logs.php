<?php
require 'C:/xampp/htdocs/Next Gen Online Medicine Delivery System/backend/app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();
try {
    $db->exec("ALTER TABLE order_status_logs ADD COLUMN changed_by INT(11) DEFAULT NULL AFTER comment;");
    echo "Added changed_by successfully. ";
} catch(Exception $e) {
    echo "Error adding changed_by: " . $e->getMessage();
}
