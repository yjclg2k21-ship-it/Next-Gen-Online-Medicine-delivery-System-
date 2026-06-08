<?php
require_once 'app/Core/Database.php';
$db = \App\Core\Database::getInstance()->getConnection();

try {
    $db->exec("ALTER TABLE backups ADD COLUMN size VARCHAR(50) AFTER filename");
    echo "Success: 'size' column added to 'backups' table.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
