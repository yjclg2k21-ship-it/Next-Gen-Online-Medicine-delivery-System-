<?php
require 'C:/xampp/htdocs/Next Gen Online Medicine Delivery System/backend/app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();
$stmt = $db->query("SELECT id, name, salt FROM medicines LIMIT 10");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
