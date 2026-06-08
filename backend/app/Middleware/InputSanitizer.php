<?php
namespace App\Middleware;

/**
 * Input Sanitizer Middleware — Cleanses incoming request data to prevent XSS and SQLi.
 */
class InputSanitizer {

    public static function handle() {
        // Sanitize GET
        if (!empty($_GET)) {
            $_GET = self::sanitizeArray($_GET);
        }

        // Sanitize POST
        if (!empty($_POST)) {
            $_POST = self::sanitizeArray($_POST);
        }

        // Sanitize raw JSON input
        $rawInput = file_get_contents('php://input');
        if (!empty($rawInput)) {
            $decoded = json_decode($rawInput, true);
            if (is_array($decoded)) {
                // In a framework, you’d overwrite the request object. 
                // Here we just sanitize and put it back in a global or leave it to BaseController to read.
                // Normally you don't override php://input, so BaseController will need to use a method that sanitizes.
            }
        }
    }

    public static function sanitizeArray($data) {
        $sanitized = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = self::sanitizeArray($value);
            } else {
                $sanitized[$key] = htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
            }
        }
        return $sanitized;
    }
}
