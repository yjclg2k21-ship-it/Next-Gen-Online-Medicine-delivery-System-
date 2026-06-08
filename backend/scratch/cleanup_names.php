<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=medicine_delivery', 'root', '');

// Remove the branding suffixes from names
$pdo->exec("UPDATE medicines SET name = REPLACE(name, ' (MNC Brand)', '')");
$pdo->exec("UPDATE medicines SET name = REPLACE(name, ' (Local Brand)', '')");
$pdo->exec("UPDATE medicines SET name = REPLACE(name, ' (Generic)', '')");
$pdo->exec("UPDATE medicines SET name = REPLACE(name, ' (Herbal Extract)', '')");

echo "Medicine names cleaned up in database.";
