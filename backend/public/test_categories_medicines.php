<?php
require 'C:/xampp/htdocs/Next Gen Online Medicine Delivery System/backend/app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();

echo "Medicines category counts:\n";
$stmt = $db->query("SELECT category_id, COUNT(*) as count FROM medicines GROUP BY category_id");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

echo "\nCategories:\n";
$stmt = $db->query("SELECT id, name FROM categories");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
