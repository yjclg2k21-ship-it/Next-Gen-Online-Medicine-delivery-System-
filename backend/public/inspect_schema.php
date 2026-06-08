<?php
require 'backend/app/bootstrap.php';
$db = App\Core\Database::getInstance()->getConnection();

$tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "Tables found: " . implode(", ", $tables) . "\n";

foreach ($tables as $table) {
    $count = $db->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
    echo "\n--- Table: $table ($count rows) ---\n";
    try {
        $stmt = $db->query("DESCRIBE `$table` ");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo implode(", ", $columns) . "\n";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
