<?php
require 'C:/xampp/htdocs/Next Gen Online Medicine Delivery System/backend/app/bootstrap.php';

// Mock authentication for vendor ID 2
$_SESSION['user'] = ['id' => 2, 'role' => 'vendor'];

$controller = new \App\Controllers\VendorController();
try {
    $response = $controller->dashboard();
    print_r($response);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
