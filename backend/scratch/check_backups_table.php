<?php
require_once 'app/Core/Database.php';
$db = \App\Core\Database::getInstance()->getConnection();
$q = $db->query("SELECT * FROM backups");
$rows = $q->fetchAll(PDO::FETCH_ASSOC);
echo "Rows: " . count($rows) . "\n";
print_r($rows);
