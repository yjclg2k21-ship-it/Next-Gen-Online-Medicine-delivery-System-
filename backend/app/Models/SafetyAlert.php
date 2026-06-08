<?php
namespace App\Models;

use App\Core\Model;

/**
 * Safety Alert Model
 * Handles clinical warnings, interactions, and dosage alerts.
 */
class SafetyAlert extends Model {
    protected $table = 'safety_alerts';
    protected $useSoftDelete = false;

    /**
     * Get pending alerts for a user
     */
    public function getPendingByUser($userId) {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE user_id = ? AND is_acknowledged = 0 ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
