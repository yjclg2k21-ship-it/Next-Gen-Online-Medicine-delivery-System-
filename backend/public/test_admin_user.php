<?php
require 'C:/xampp/htdocs/Next Gen Online Medicine Delivery System/backend/app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();
$stmt = $db->query("SELECT id, name, email, role FROM users WHERE role = 'admin'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
