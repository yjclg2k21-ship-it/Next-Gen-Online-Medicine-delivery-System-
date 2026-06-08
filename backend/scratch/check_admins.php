<?php
require_once __DIR__ . '/../app/Core/Database.php';

try {
    $db = App\Core\Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT id, name, email, role, deleted_at FROM users WHERE role = 'admin'");
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($admins);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
