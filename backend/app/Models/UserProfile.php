<?php
namespace App\Models;

use App\Core\Model;

/**
 * UserProfile Model
 */
class UserProfile extends Model {
    protected $table = 'user_profiles';

    /**
     * Get profile by User ID
     */
    public function getByUserId($userId) {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }
}
