<?php
require 'C:/xampp/htdocs/Next Gen Online Medicine Delivery System/backend/app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();

$db->exec("UPDATE medicines SET category_id = FLOOR(1 + (RAND() * 17)) WHERE category_id IS NULL OR category_id = 0");
echo "Assigned random category IDs to medicines.\n";
