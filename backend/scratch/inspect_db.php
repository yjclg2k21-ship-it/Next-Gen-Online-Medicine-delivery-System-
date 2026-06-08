<?php
require __DIR__ . '/../app/bootstrap.php';
$db = App\Core\Database::getInstance()->getConnection();

echo "Users:\n";
$users = $db->query("SELECT id, name, email, role FROM users")->fetchAll();
print_r($users);

echo "\nUser Profiles:\n";
$profiles = $db->query("SELECT * FROM user_profiles")->fetchAll();
print_r($profiles);

echo "\nVendor Profiles:\n";
$vprofiles = $db->query("SELECT * FROM vendor_profiles")->fetchAll();
print_r($vprofiles);
