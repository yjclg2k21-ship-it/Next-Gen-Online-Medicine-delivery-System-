<?php
require 'backend/app/bootstrap.php';
$db = App\Core\Database::getInstance()->getConnection();
$stmt = $db->query("SELECT id, email, role FROM users");
echo "Users:\n";
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    print_r($row);
}
echo "\nOrders:\n";
$stmt = $db->query("SELECT id FROM orders");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['id'] . "\n";
}
