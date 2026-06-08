<?php
require_once __DIR__ . '/../app/Core/Database.php';

try {
    $db = App\Core\Database::getInstance()->getConnection();
    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tables as $table) {
        $stmt = $db->query("DESCRIBE $table");
        $cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('deleted_at', $cols)) {
            echo "Table $table is missing deleted_at\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
