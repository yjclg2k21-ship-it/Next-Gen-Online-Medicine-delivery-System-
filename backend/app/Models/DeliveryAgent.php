<?php
namespace App\Models;

use App\Core\Model;

/**
 * Delivery Agent Model
 */
class DeliveryAgent extends Model {
    protected $table = 'delivery_profiles';

    /**
     * Get profiles with user basic details
     */
    public function getActiveAgents() {
        $stmt = $this->db->prepare("
            SELECT dp.*, u.name, u.phone 
            FROM `{$this->table}` dp 
            JOIN users u ON dp.user_id = u.id 
            WHERE dp.status = 'active'
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
