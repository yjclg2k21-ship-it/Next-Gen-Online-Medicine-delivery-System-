<?php
$host = '127.0.0.1';
$db   = 'medicine_delivery';
$user = 'root';
$pass = '';
$dsn = "mysql:host=$host;dbname=$db";
try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $count = $pdo->exec("UPDATE orders SET status = 'pending' WHERE status = ''");
    echo "Successfully updated $count orders to 'pending' status.";
} catch (PDOException $e) {
    echo "Update failed: " . $e->getMessage();
}
