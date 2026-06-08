<?php
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Core/Model.php';

try {
    $db = App\Core\Database::getInstance()->getConnection();
    $stmt = $db->query("DESCRIBE revoked_tokens");
    print_r($stmt->fetchAll(PDO::FETCH_COLUMN));
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
