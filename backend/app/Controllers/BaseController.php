<?php
namespace App\Controllers;

/**
 * Base Controller
 * Provides helper methods for JSON responses and input handling.
 */
class BaseController {
    
    protected function json($data, $status = 200) {
        http_response_code($status);
        $json = json_encode($data);
        echo $json;
        return $json;
    }

    protected function getCurrentUserId() {
        $user = get_current_user_data();
        return $user ? $user['id'] : null;
    }

    public function options() {
        http_response_code(204);
        exit;
    }

    protected function getPostData() {
        $json = json_decode(file_get_contents('php://input'), true) ?? [];
        return array_merge($_POST, $json);
    }
}
