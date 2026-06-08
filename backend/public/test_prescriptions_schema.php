<?php
require 'C:/xampp/htdocs/Next Gen Online Medicine Delivery System/backend/app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();
$stmt = $db->query("DESCRIBE prescriptions");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
