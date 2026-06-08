<?php
require 'C:/xampp/htdocs/Next Gen Online Medicine Delivery System/backend/app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();
try {
    $stmt = $db->query("
        SELECT c.*, 
               (SELECT COUNT(*) FROM medicines WHERE category_id = c.id) as medicine_count,
               (SELECT COUNT(*) FROM categories WHERE parent_id = c.id) as sub_count
        FROM categories c
        ORDER BY c.name ASC
    ");
    print_r($stmt->fetchAll());
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
