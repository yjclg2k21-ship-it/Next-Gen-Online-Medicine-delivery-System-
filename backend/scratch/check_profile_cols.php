<?php
require_once __DIR__ . '/../app/Core/Database.php';
$db = App\Core\Database::getInstance()->getConnection();
$tables = ['vendor_profiles', 'delivery_profiles'];
foreach ($tables as $table) {
    echo "Table: $table\n";
    $stmt = $db->query("DESCRIBE $table");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
}
