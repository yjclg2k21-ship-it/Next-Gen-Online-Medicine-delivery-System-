<?php
require 'C:/xampp/htdocs/Next Gen Online Medicine Delivery System/backend/app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();

echo "Medicines brand counts:\n";
$stmt = $db->query("SELECT brand_id, COUNT(*) as count FROM medicines GROUP BY brand_id");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

echo "\nBrands:\n";
try {
    $stmt = $db->query("SELECT * FROM brands");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
