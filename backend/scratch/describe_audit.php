<?php
require_once __DIR__ . '/../app/Core/Database.php';
$db = App\Core\Database::getInstance()->getConnection();
$stmt = $db->query("DESCRIBE audit_logs");
print_r($stmt->fetchAll(PDO::FETCH_COLUMN));
