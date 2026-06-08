<?php
require_once __DIR__ . '/../app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();

$tables = ['orders', 'users', 'return_requests', 'prescriptions', 'medicines', 'addresses'];
foreach ($tables as $table) {
    try {
        $stmt = $db->query("SHOW COLUMNS FROM `{$table}` LIKE 'deleted_at'");
        $col = $stmt->fetchAll();
        echo $table . ": " . (count($col) > 0 ? "HAS deleted_at" : "MISSING deleted_at") . "\n";
    } catch (\Exception $e) {
        echo $table . ": ERROR - " . $e->getMessage() . "\n";
    }
}
