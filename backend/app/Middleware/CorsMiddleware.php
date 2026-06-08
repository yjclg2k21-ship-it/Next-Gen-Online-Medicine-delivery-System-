<?php
namespace App\Middleware;

/**
 * CORS Middleware
 * Granularly manages Cross-Origin HTTP constraints natively for frontend security.
 */
class CorsMiddleware {
    
    public static function handle() {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
        
        header("Access-Control-Allow-Origin: $origin");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        header("Access-Control-Allow-Credentials: true");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }
}
