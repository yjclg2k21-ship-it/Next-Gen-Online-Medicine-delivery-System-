<?php
/**
 * Mediflow Backend API - Entry Point
 * Handles all incoming requests and routes them to the appropriate controller.
 */

require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Router;
use App\Middleware\CorsMiddleware;

// Initialize Global Constraints (CORS, Security Headers)
CorsMiddleware::handle();

header("Content-Type: application/json; charset=UTF-8");
error_reporting(0);
ini_set('display_errors', 0);

// Global exception & error handlers to prevent silent 500s
set_exception_handler(function ($e) {
    $logDir = __DIR__ . '/../storage/logs';
    if (!is_dir($logDir)) mkdir($logDir, 0777, true);
    file_put_contents($logDir . '/fatal.log',
        "[" . date('Y-m-d H:i:s') . "] UNCAUGHT " . get_class($e) . ": " . $e->getMessage() .
        " in " . $e->getFile() . ":" . $e->getLine() . "\n" . $e->getTraceAsString() . "\n\n",
        FILE_APPEND);
    if (!headers_sent()) {
        http_response_code(500);
        header("Content-Type: application/json; charset=UTF-8");
    }
    echo json_encode(['success' => false, 'message' => 'Internal server error.', 'debug_error' => $e->getMessage()]);
    exit;
});

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
});

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$router = new Router();
require_once __DIR__ . '/../routes/api.php';

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

