<?php
require_once __DIR__ . '/../app/Core/Database.php';
$db = App\Core\Database::getInstance()->getConnection();
$stmt = $db->query("DESCRIBE orders");
print_r($stmt->fetchAll(PDO::FETCH_COLUMN));
