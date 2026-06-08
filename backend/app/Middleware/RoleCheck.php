<?php
namespace App\Middleware;

/**
 * Role Check Middleware — Validates if the authenticated user has the required role
 */
class RoleCheck {

    public static function handle($requiredRoles) {
        $user = AuthGuard::getUser();

        if (!$user || empty($user['role'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden. Access denied.']);
            exit;
        }

        if (is_string($requiredRoles)) {
            $requiredRoles = [$requiredRoles];
        }

        if (!in_array($user['role'], $requiredRoles)) {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden. Insufficient permissions.']);
            exit;
        }
    }
}
