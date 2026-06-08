<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=medicine_delivery;charset=utf8', 'root', '');
    $stmt = $pdo->query('SHOW COLUMNS FROM addresses');
    echo "---addresses COLUMNS---\n";
    print_r($stmt->fetchAll(PDO::FETCH_COLUMN));
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
