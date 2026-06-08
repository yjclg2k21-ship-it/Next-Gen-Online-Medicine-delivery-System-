<?php
require_once __DIR__ . '/backend/app/bootstrap.php';
use App\Core\Database;

$db = Database::getInstance()->getConnection();

echo "--- Tables ---\n";
$tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
foreach ($tables as $table) {
    $count = $db->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
    echo "$table: $count rows\n";
}

echo "\n--- Medicines ---\n";
$medicines = $db->query("SELECT id, name, approval_status FROM medicines LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
print_r($medicines);

echo "\n--- Orders ---\n";
$orders = $db->query("SELECT id, user_id, status FROM orders LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
print_r($orders);
