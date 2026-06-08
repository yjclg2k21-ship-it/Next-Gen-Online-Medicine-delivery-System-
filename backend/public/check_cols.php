<?php
require_once __DIR__ . '/../app/Core/Database.php';
$db = \App\Core\Database::getInstance()->getConnection();
$stmt = $db->query("SHOW COLUMNS FROM vendor_profiles");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(array_column($rows, 'Field'));
