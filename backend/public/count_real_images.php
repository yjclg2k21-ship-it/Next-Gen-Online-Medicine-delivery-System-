<?php
require 'C:/xampp/htdocs/Next Gen Online Medicine Delivery System/backend/app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();

$stmt = $db->query("SELECT id, name, image FROM medicines WHERE image IS NOT NULL AND image != ''");
$medicinesWithImageColumn = $stmt->fetchAll(PDO::FETCH_ASSOC);

$realImageCount = 0;
$imageDir = 'C:/xampp/htdocs/Next Gen Online Medicine Delivery System/frontend/assets/images/medicines/';

foreach ($medicinesWithImageColumn as $med) {
    if (file_exists($imageDir . $med['image'])) {
        $realImageCount++;
    }
}

echo "Total medicines with image entry in DB: " . count($medicinesWithImageColumn) . "\n";
echo "Total medicines with ACTUAL image file on disk: " . $realImageCount . "\n";
