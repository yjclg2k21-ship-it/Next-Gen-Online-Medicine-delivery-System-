<?php
namespace App\Middleware;

/**
 * Rate Limiter Middleware — Basic prevention against DDoS and brute force attacks
 */
class RateLimiter {

    // Simple file-based or array-based memory mock for rate limiting
    // In production, this would use Redis or Memcached
    public static function handle($maxRequests = 100, $timeWindowSeconds = 60) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $key = 'rate_limit_' . md5($ip);
        $now = time();

        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 1, 'start_time' => $now];
        } else {
            $data = $_SESSION[$key];
            if ($now - $data['start_time'] < $timeWindowSeconds) {
                if ($data['count'] >= $maxRequests) {
                    http_response_code(429);
                    header('Content-Type: application/json');
                    echo json_encode([
                        'error' => 'Too Many Requests',
                        'retry_after' => $timeWindowSeconds - ($now - $data['start_time'])
                    ]);
                    exit;
                }
                $_SESSION[$key]['count']++;
            } else {
                $_SESSION[$key] = ['count' => 1, 'start_time' => $now];
            }
        }
    }
}
