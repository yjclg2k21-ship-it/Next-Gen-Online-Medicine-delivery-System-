<?php
namespace App\Models;

use App\Core\Model;

/**
 * DeliveryProfile Model
 */
class DeliveryProfile extends Model {
    protected $table = 'delivery_profiles';

    /**
     * Get profile by User ID
     */
    public function getByUserId($userId) {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }
}
