<?php
namespace App\Core;

/**
 * ResponseHandler
 * Standardized JSON API responses for all controllers.
 */
class ResponseHandler {

    public static function json(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public static function success($data = [], string $message = 'Success', int $code = 200): void {
        self::json(['success' => true, 'message' => $message, 'data' => $data], $code);
    }

    public static function error(string $message, int $code = 400): void {
        if ($code === 500) {
            self::logError($message);
            $message = 'An internal pharmaceutical server error occurred. Please contact support.';
        }
        self::json(['success' => false, 'message' => $message, 'data' => null], $code);
    }

    private static function logError(string $message): void {
        $logDir = dirname(__DIR__, 2) . '/storage/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        $logPath = $logDir . '/error.log';
        $timestamp = date('Y-m-d H:i:s');
        $entry = "[$timestamp] ERROR: $message" . PHP_EOL;
        file_put_contents($logPath, $entry, FILE_APPEND);
    }

    public static function notFound(string $message = 'Resource not found'): void {
        self::error($message, 404);
    }

    public static function forbidden(string $message = 'Access denied'): void {
        self::error($message, 403);
    }

    public static function badRequest(string $message = 'Bad request'): void {
        self::error($message, 400);
    }

    public static function unauthorized(string $message = 'Unauthorized'): void {
        self::error($message, 401);
    }
}
