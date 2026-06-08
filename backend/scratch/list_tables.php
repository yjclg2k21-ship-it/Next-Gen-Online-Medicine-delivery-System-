<?php
require_once 'app/Core/Database.php';
$db = \App\Core\Database::getInstance()->getConnection();
$q = $db->query("SHOW TABLES");
foreach ($q->fetchAll(PDO::FETCH_COLUMN) as $table) {
    echo $table . "\n";
}
