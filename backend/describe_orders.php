<?php
$host = '127.0.0.1';
$db   = 'medicine_delivery';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
try {
     $pdo = new PDO($dsn, $user, $pass);
     $stmt = $pdo->query("DESCRIBE orders");
     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
         print_r($row);
     }
} catch (\PDOException $e) {
     echo "Error: " . $e->getMessage();
}
