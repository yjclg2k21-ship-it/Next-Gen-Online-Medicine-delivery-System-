<?php
require 'C:/xampp/htdocs/Next Gen Online Medicine Delivery System/backend/app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();
$stmt = $db->query("SELECT * FROM delivery_methods");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
