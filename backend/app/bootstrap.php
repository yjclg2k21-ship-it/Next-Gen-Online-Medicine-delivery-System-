<?php
/**
 * Mediflow Backend Bootstrap
 * Foundation for the application, autoloader, and global helpers.
 */

// Set default timezone
date_default_timezone_set('Asia/Kolkata');


// 1. PSR-4 Autoloader
spl_autoload_register(function ($class) {
    // Prefix for the namespace
    $prefix = 'App\\';
    // Directory mapping for the prefix
    $base_dir = __DIR__ . '/';

    // Does the class use the prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // Get the relative class name
    $relative_class = substr($class, $len);

    // Replace the namespace prefix with the base directory, 
    // replace namespace separators with directory separators,
    // and add .php extension
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

// 2. Load Environment Variables
\App\Core\DotEnv::load(__DIR__ . '/../.env');

// 3. Global Helper Functions
if (!function_exists('getallheaders')) {
    function getallheaders() {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}

/**
 * Global helper to get the current authenticated user via JWT
 */
function get_current_user_data() {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    
    if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $token = $matches[1];
        try {
            $parts = explode('.', $token);
            if (count($parts) === 3) {
                list($header64, $payload64, $signature64) = $parts;
                
                // Verify Signature using Config
                $config = require __DIR__ . '/../config/app.php';
                $secret = $config['jwt_secret'];
                
                $expectedSig = hash_hmac('sha256', "$header64.$payload64", $secret, true);
                $expectedSig64 = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($expectedSig));
                
                if (hash_equals($expectedSig64, $signature64)) {
                    $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $payload64)), true);
                    if ($payload && isset($payload['id'])) {
                        return [
                            'id'   => $payload['id'],
                            'role' => $payload['role'] ?? 'user'
                        ];
                    }
                }
            }
        } catch (Exception $e) {
            return null;
        }
    }
    return null;
}

if (!function_exists('current_user_role')) {
    /**
     * Get the role of the currently authenticated user based on the session or token.
     */
    function current_user_role() {
        $user = get_current_user_data();
        return $user ? $user['role'] : ($_SESSION['user_role'] ?? 'guest');
    }
}

// 4. Error Reporting
// display_errors is intentionally OFF — PHP warnings must not pollute JSON API responses.
// public/index.php also sets this, but keeping it here as a safety net.
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// 5. Session Start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
