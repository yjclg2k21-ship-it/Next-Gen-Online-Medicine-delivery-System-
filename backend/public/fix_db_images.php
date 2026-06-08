<?php
require 'C:/xampp/htdocs/Next Gen Online Medicine Delivery System/backend/app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();

// Update medicines table to keep only the filename in the image column
$sql = "UPDATE medicines SET image = SUBSTRING_INDEX(image, '/', -1) WHERE image IS NOT NULL AND image != '' AND image LIKE '%/%'";
$stmt = $db->query($sql);
echo "Updated " . $stmt->rowCount() . " medicines image paths.\n";

// Update banners table just in case
$sql2 = "UPDATE banners SET image_url = SUBSTRING_INDEX(image_url, '/', -1) WHERE image_url IS NOT NULL AND image_url != '' AND image_url LIKE '%/%'";
$stmt2 = $db->query($sql2);
echo "Updated " . $stmt2->rowCount() . " banners image paths.\n";

// Test output
$stmt3 = $db->query('SELECT id, name, image FROM medicines LIMIT 5');
print_r($stmt3->fetchAll(PDO::FETCH_ASSOC));
