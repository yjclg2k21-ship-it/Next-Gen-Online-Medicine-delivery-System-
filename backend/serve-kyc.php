<?php
/**
 * KYC File Server
 * Serves KYC document files securely (requires valid admin token)
 */

// Load bootstrap for DB and auth
require_once __DIR__ . '/app/bootstrap.php';

use App\Middleware\AuthGuard;

// Check auth
try {
    AuthGuard::handle();
    $user = AuthGuard::getUser();
    if (!$user || $user['role'] !== 'admin') {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Access denied', 'status' => 403]);
        exit;
    }
} catch (\Exception $e) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized', 'status' => 401]);
    exit;
}

// Get filename
$filename = $_GET['file'] ?? '';
$filename = basename($filename); // Prevent directory traversal

if (empty($filename)) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No file specified', 'status' => 400]);
    exit;
}

// Check both storage locations
$paths = [
    __DIR__ . '/storage/private/kyc/' . $filename,
    __DIR__ . '/storage/uploads/kyc/' . $filename,
];

$filePath = null;
foreach ($paths as $p) {
    if (file_exists($p)) {
        $filePath = $p;
        break;
    }
}

if (!$filePath) {
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'File not found', 'status' => 404]);
    exit;
}

// Serve the file
$mime = mime_content_type($filePath) ?: 'application/octet-stream';
header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: private, max-age=3600');
readfile($filePath);
exit;
