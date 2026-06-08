<?php
namespace App\Middleware;

/**
 * Auth Guard Middleware — Validates Bearer JWT token on protected routes
 * Checks: token present → valid payload → not expired → not blacklisted (revoked)
 */
class AuthGuard {

    public static function handle() {
        $auth = $_SERVER['HTTP_AUTHORIZATION'] ?? ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');
        
        // DEBUG: Log headers for troubleshooting
        $debugLog = __DIR__ . '/../../storage/logs/auth_debug.log';
        if (!is_dir(dirname($debugLog))) mkdir(dirname($debugLog), 0777, true);
        file_put_contents($debugLog, "[" . date('Y-m-d H:i:s') . "] Auth Check: " . substr($auth, 0, 20) . "...\n", FILE_APPEND);

        // Fallback for direct download links (GET)
        if (!$auth && isset($_GET['token'])) {
            $auth = 'Bearer ' . $_GET['token'];
        }

        if (!$auth || strpos($auth, 'Bearer ') !== 0) {
            file_put_contents($debugLog, "[" . date('Y-m-d H:i:s') . "] Failure: No Bearer token found in headers.\n", FILE_APPEND);
            http_response_code(401);
            echo json_encode(['error' => 'Unauthenticated. Bearer token required.']);
            exit;
        }

        $token   = str_replace('Bearer ', '', $auth);
        $parts   = explode('.', $token);
        
        if (count($parts) !== 3) {
            http_response_code(401);
            echo json_encode(['error' => 'Malformed JWT. 3 parts required.']);
            exit;
        }

        list($header64, $payload64, $signature64) = $parts;

        // Verify Signature
        $config = require __DIR__ . '/../../config/app.php';
        $secret = $config['jwt_secret'];
        
        $expectedSig = hash_hmac('sha256', "$header64.$payload64", $secret, true);
        $expectedSig64 = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($expectedSig));
        
        if (!hash_equals($expectedSig64, $signature64)) {
            file_put_contents($debugLog, "[" . date('Y-m-d H:i:s') . "] Failure: Signature mismatch. Expected: " . substr($expectedSig64, 0, 10) . "... Got: " . substr($signature64, 0, 10) . "...\n", FILE_APPEND);
            http_response_code(401);
            echo json_encode(['error' => 'Invalid JWT signature. Authentication bypass blocked.']);
            exit;
        }

        $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $payload64)), true);

        if (!$payload || empty($payload['id']) || empty($payload['exp'])) {
            file_put_contents($debugLog, "[" . date('Y-m-d H:i:s') . "] Failure: Malformed payload.\n", FILE_APPEND);
            http_response_code(401);
            echo json_encode(['error' => 'Invalid or malformed payload.']);
            exit;
        }

        if ($payload['exp'] < time()) {
            http_response_code(401);
            echo json_encode(['error' => 'Token expired. Please login again.']);
            exit;
        }

        // Check persistent token blacklist
        $tokenHash = hash('sha256', $token);
        if ((new \App\Models\RevokedToken())->isRevoked($tokenHash)) {
            http_response_code(401);
            echo json_encode(['error' => 'Token has been revoked. Please login again.']);
            exit;
        }

        // Attach user context to globals for downstream use
        $GLOBALS['auth_user'] = $payload;
    }

    public static function getUser() {
        return $GLOBALS['auth_user'] ?? null;
    }
}
