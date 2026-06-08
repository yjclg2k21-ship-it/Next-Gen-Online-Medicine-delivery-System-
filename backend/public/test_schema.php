<?php
// Mock server environment to run PrescriptionController->upload()
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['CONTENT_TYPE'] = 'multipart/form-data';
$_FILES['prescription'] = [
    'name' => 'prescription.png',
    'type' => 'image/png',
    'tmp_name' => tempnam(sys_get_temp_dir(), 'rx'),
    'error' => UPLOAD_ERR_OK,
    'size' => 1024
];
// Write dummy file to mock tmp file
file_put_contents($_FILES['prescription']['tmp_name'], 'dummy content');

$_POST['doctor_name'] = 'Test Doctor';
$_POST['vendor_id'] = '2';

// Set up mock session
session_start();
$_SESSION['user'] = ['id' => 1, 'role' => 'user'];

require 'C:/xampp/htdocs/Next Gen Online Medicine Delivery System/backend/app/bootstrap.php';

try {
    $controller = new \App\Controllers\PrescriptionController();
    $res = $controller->upload();
    print_r($res);
} catch (Exception $e) {
    echo "Exception Caught: " . $e->getMessage() . "\n" . $e->getTraceAsString();
} catch (Throwable $t) {
    echo "Throwable Caught: " . $t->getMessage() . "\n" . $t->getTraceAsString();
}


