<?php
require_once __DIR__ . '/../app/Core/Database.php';
$db = App\Core\Database::getInstance()->getConnection();
$tables = ['return_requests', 'addresses'];
foreach ($tables as $table) {
    echo "Table: $table\n";
    try {
        $stmt = $db->query("DESCRIBE $table");
        $cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "Has deleted_at: " . (in_array('deleted_at', $cols) ? "YES" : "NO") . "\n";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
