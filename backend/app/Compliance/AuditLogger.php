<?php
namespace App\Compliance;

use App\Core\Database;

/**
 * AuditLogger
 * Regulatory compliance: Logs all critical user and system actions.
 */
class AuditLogger {
    private static $db;

    private static function getDb() {
        if (!self::$db) {
            self::$db = Database::getInstance()->getConnection();
        }
        return self::$db;
    }

    public static function log(?int $userId, string $action, array $payload = []): void {
        $db = self::getDb();
        $stmt = $db->prepare("INSERT INTO audit_logs (user_id, action, target_model, target_id, payload, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $userId === 0 ? null : $userId, 
            $action, 
            null,
            null,
            !empty($payload) ? json_encode($payload) : null
        ]);
    }

    public static function orderPlaced(int $userId, int $orderId): void {
        self::log($userId, 'ORDER_PLACED', ['order_id' => $orderId]);
    }

    public static function orderCancelled(int $userId, int $orderId): void {
        self::log($userId, 'ORDER_CANCELLED', ['order_id' => $orderId]);
    }

    public static function passwordChanged(int $userId): void {
        self::log($userId, 'PASSWORD_CHANGED');
    }

    public static function loginSuccess(int $userId): void {
        self::log($userId, 'LOGIN_SUCCESS');
    }

    public static function loginFailed(string $email): void {
        // Log without userId since we don't know it
        self::log(null, 'LOGIN_FAILED', ['email' => $email]);
    }

    public static function prescriptionUploaded(int $userId, int $prescriptionId): void {
        self::log($userId, 'PRESCRIPTION_UPLOADED', ['prescription_id' => $prescriptionId]);
    }
}
