<?php
namespace App\Models;

use App\Core\Model;

/**
 * Health Metric Model
 * Handles user clinical telemetry (BP, Glucose, Heart Rate).
 */
class HealthMetric extends Model {
    protected $table = 'health_metrics';
    protected $useSoftDelete = false;

    /**
     * Get recent metrics for a user
     */
    public function getLatestByUser($userId, $limit = 10) {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE user_id = ? ORDER BY reading_at DESC LIMIT ?");
        $stmt->execute([$userId, (int)$limit]);
        return $stmt->fetchAll();
    }
}
