<?php
require 'C:/xampp/htdocs/Next Gen Online Medicine Delivery System/backend/app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();
$hash = password_hash('password123', PASSWORD_DEFAULT);
$db->query("UPDATE users SET password = '$hash'");
echo "All passwords reset to password123\n";
