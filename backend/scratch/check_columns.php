<?php
require_once __DIR__ . '/../app/Core/Database.php';

try {
    $db = App\Core\Database::getInstance()->getConnection();
    $tables = ['orders', 'prescriptions', 'medicines', 'users'];
    foreach ($tables as $table) {
        echo "Table: $table\n";
        $stmt = $db->query("DESCRIBE $table");
        $cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
        print_r($cols);
        echo "Has deleted_at: " . (in_array('deleted_at', $cols) ? "YES" : "NO") . "\n\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
