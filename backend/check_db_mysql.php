<?php
$host = '127.0.0.1';
$db   = 'medicine_delivery';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     $stmt = $pdo->query("SELECT id, status FROM orders LIMIT 5");
     while ($row = $stmt->fetch()) {
         echo "ID: " . $row['id'] . " | Status: '" . $row['status'] . "'\n";
     }
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
