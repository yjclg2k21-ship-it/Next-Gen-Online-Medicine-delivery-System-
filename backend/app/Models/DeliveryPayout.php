<?php
namespace App\Models;

use App\Core\Model;

/**
 * DeliveryPayout Model
 */
class DeliveryPayout extends Model {
    protected $table = 'delivery_payouts';

    public function getByRider($riderId) {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE rider_id = ? ORDER BY requested_at DESC");
        $stmt->execute([$riderId]);
        return $stmt->fetchAll();
    }
}
