<?php
require 'C:/xampp/htdocs/Next Gen Online Medicine Delivery System/backend/app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();
$stmt = $db->query('SELECT id, name, image FROM medicines LIMIT 5');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
