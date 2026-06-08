<?php
namespace App\Models;

use App\Core\Model;

/**
 * AuditLog Model
 */
class AuditLog extends Model {
    protected $table = 'audit_logs';
    protected $useSoftDelete = false;

    /**
     * Log an action
     */
    public function log($userId, $action, $targetModel = null, $targetId = null, $payload = null) {
        return $this->create([
            'user_id' => $userId,
            'action' => $action,
            'target_model' => $targetModel,
            'target_id' => $targetId,
            'payload' => is_string($payload) ? $payload : ($payload ? json_encode($payload) : null)
        ]);
    }
    /**
     * Get latest audit logs with user details
     * 
     * @param int $limit Maximum number of logs to retrieve.
     * @return array[] A list of audit logs.
     */
    public function getLatest($limit = 50) {
        $stmt = $this->db->prepare("
            SELECT a.*, u.name as actor, u.name as user_name, u.role, u.role as user_role, DATE_FORMAT(a.created_at, '%h:%i %p') as time, DATE_FORMAT(a.created_at, '%b %d') as date
            FROM audit_logs a
            LEFT JOIN users u ON a.user_id = u.id
            ORDER BY a.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    /**
     * Get a specific log with user details
     */
    public function findWithDetails($id) {
        $stmt = $this->db->prepare("
            SELECT a.*, u.name as user_name, u.email as user_email, u.role as user_role
            FROM audit_logs a
            LEFT JOIN users u ON a.user_id = u.id
            WHERE a.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Get aggregate stats for logs
     * 
     * @return array|false The aggregate statistics.
     */
    public function getStats() {
        $stmt = $this->db->query("
            SELECT 
                COUNT(*) as logs_today,
                (SELECT COUNT(*) FROM audit_logs WHERE created_at >= NOW() - INTERVAL 1 HOUR) as recent_alerts
            FROM audit_logs
            WHERE DATE(created_at) = CURDATE()
        ");
        return $stmt->fetch();
    }
}
