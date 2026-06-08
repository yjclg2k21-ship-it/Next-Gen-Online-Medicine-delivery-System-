<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=medicine_delivery;charset=utf8', 'root', '');
    
    // Check if columns exist first to avoid errors
    $stmt = $pdo->query("SHOW COLUMNS FROM orders LIKE 'delivery_otp'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE orders ADD COLUMN delivery_otp VARCHAR(6) NULL AFTER status");
        echo "Added delivery_otp to orders.\n";
    }

    $stmt = $pdo->query("SHOW COLUMNS FROM order_status_logs LIKE 'changed_by'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE order_status_logs ADD COLUMN changed_by INT NULL AFTER status");
        echo "Added changed_by to order_status_logs.\n";
    }

    echo "Sync Complete.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
