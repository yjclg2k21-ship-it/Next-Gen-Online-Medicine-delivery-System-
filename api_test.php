<?php
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/api/medicines/search';
$_SERVER['SCRIPT_NAME'] = '/Next Gen Online Medicine Delivery System/backend/public/index.php';

// Mock headers
if (!function_exists('getallheaders')) {
    function getallheaders() { return []; }
}

ob_start();
require_once __DIR__ . '/backend/public/index.php';
$output = ob_get_clean();

echo "--- START OUTPUT ---\n";
echo bin2hex($output);
echo "\n--- END OUTPUT ---\n";
echo $output;
