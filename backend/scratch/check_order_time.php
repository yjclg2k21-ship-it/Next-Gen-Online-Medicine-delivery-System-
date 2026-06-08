<?php
require_once 'app/Core/Database.php';
$db = \App\Core\Database::getInstance()->getConnection();
$q = $db->query("SELECT created_at FROM orders WHERE id = 1");
$row = $q->fetch(PDO::FETCH_ASSOC);
echo "Order Created At: " . $row['created_at'] . "\n";
echo "Current Time: " . date('Y-m-d H:i:s') . "\n";
