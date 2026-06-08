<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=medicine_delivery', 'root', '');
$stmt = $pdo->query("DESCRIBE blogs");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
