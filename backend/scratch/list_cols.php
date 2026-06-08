<?php
require_once __DIR__ . '/../app/Core/Database.php';
$db = App\Core\Database::getInstance()->getConnection();
foreach (['vendor_profiles', 'delivery_profiles'] as $table) {
    echo "--- Table: $table ---\n";
    $stmt = $db->query("DESCRIBE $table");
    foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        echo $row['Field'] . " (" . $row['Type'] . ")\n";
    }
    echo "\n";
}
