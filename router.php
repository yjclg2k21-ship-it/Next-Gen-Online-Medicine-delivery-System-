<?php
/**
 * MediMitra Central Router
 * Handles static assets and API routing for the PHP built-in server.
 */

// Support decode for URLs with spaces/encoded characters
function decodeURI($uri) {
    return parse_url($uri, PHP_URL_PATH);
}

$uri = decodeURI($_SERVER['REQUEST_URI']);

// 1. Static Asset Handling (Look in frontend/ folder)
$frontendPath = __DIR__ . '/frontend' . $uri;

if ($uri !== '/' && file_exists($frontendPath) && !is_dir($frontendPath)) {
    // Set proper MIME type
    $extension = pathinfo($frontendPath, PATHINFO_EXTENSION);
    $mimes = [
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif',
        'svg'  => 'image/svg+xml',
        'html' => 'text/html'
    ];
    if (isset($mimes[$extension])) {
        header("Content-Type: {$mimes[$extension]}");
    }
    readfile($frontendPath);
    return true;
}

    // 2. Route API requests to the backend entry point
    if (strpos($uri, '/api') !== false) {
        $_SERVER['SCRIPT_NAME'] = '/backend/public/index.php';
        require __DIR__ . '/backend/public/index.php';
        return;
    }

// 3. Default to serving index.html if accessing root or non-existent paths
if ($uri === '/' || !file_exists($frontendPath)) {
    $indexPath = __DIR__ . '/frontend/index.html';
    if (file_exists($indexPath)) {
        header("Content-Type: text/html");
        readfile($indexPath);
        return;
    }
}

return false;
