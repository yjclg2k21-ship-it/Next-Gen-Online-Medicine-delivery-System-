<?php
require 'C:/xampp/htdocs/Next Gen Online Medicine Delivery System/backend/app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();
$stmt = $db->query("SELECT id, name, email, role, status FROM users ORDER BY role, id");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($users);
