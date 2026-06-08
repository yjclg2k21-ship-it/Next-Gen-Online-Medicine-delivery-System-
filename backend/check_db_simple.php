<?php
$dbPath = __DIR__ . '/database/medimitra.db';
try {
    $pdo = new PDO("sqlite:" . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->query("SELECT id, status FROM orders LIMIT 5");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: " . $row['id'] . " | Status: '" . $row['status'] . "'\n";
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
