<?php
require 'C:/xampp/htdocs/Next Gen Online Medicine Delivery System/backend/app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();

$db->exec("UPDATE medicines SET brand_id = FLOOR(1 + (RAND() * 4)) WHERE brand_id IS NULL OR brand_id = 0");
echo "Assigned random brand IDs to medicines.\n";
