<?php
require_once 'app/Core/Database.php';
use App\Core\Database;

$db = Database::getInstance()->getConnection();
echo "--- User Roles & Status ---\n";
print_r($db->query("SELECT role, status, COUNT(*) as count FROM users GROUP BY role, status")->fetchAll(PDO::FETCH_ASSOC));

echo "\n--- Medicine Approval Status ---\n";
print_r($db->query("SELECT approval_status, COUNT(*) as count FROM medicines GROUP BY approval_status")->fetchAll(PDO::FETCH_ASSOC));
