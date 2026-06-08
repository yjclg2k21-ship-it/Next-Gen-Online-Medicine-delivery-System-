<?php
require_once 'backend/app/bootstrap.php';
$db = App\Core\Database::getInstance()->getConnection();
try {
    $q = $db->query("SELECT id, name, email, role, created_at FROM users ORDER BY id DESC LIMIT 10");
    print_r($q->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
