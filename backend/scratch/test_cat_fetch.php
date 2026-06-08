<?php
require_once 'app/Models/Category.php';
$catModel = new \App\Models\Category();
$res = $catModel->findAll();
echo "Found " . count($res) . " categories.\n";
print_r($res);
