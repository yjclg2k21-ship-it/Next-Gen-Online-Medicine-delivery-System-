<?php
$host = '127.0.0.1';
$db   = 'medicine_delivery';
$user = 'root';
$pass = '';
$dsn = "mysql:host=$host;dbname=$db";
try {
     $pdo = new PDO($dsn, $user, $pass);
     $stmt = $pdo->query("DESCRIBE orders");
     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
         if ($row['Field'] === 'status') {
             print_r($row);
         }
     }
} catch (\PDOException $e) { echo $e->getMessage(); }
