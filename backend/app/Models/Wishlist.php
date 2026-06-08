<?php
namespace App\Models;

use App\Core\Model;

/**
 * Wishlist Model
 */
class Wishlist extends Model {
    protected $table = 'wishlists';
    protected $useSoftDelete = false;

    /**
     * Get for a user
     */
    public function getByUser($userId) {
        $stmt = $this->db->prepare("SELECT m.* FROM `{$this->table}` w 
                                    JOIN medicines m ON w.medicine_id = m.id 
                                    WHERE w.user_id = ? AND m.deleted_at IS NULL");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Is in wishlist
     */
    public function exists($userId, $medicineId) {
        $stmt = $this->db->prepare("SELECT id FROM `{$this->table}` WHERE user_id = ? AND medicine_id = ?");
        $stmt->execute([$userId, $medicineId]);
        return (bool)$stmt->fetch();
    }

    /**
     * Toggle
     */
    public function toggle($userId, $medicineId) {
        if ($this->exists($userId, $medicineId)) {
            $stmt = $this->db->prepare("DELETE FROM `{$this->table}` WHERE user_id = ? AND medicine_id = ?");
            $stmt->execute([$userId, $medicineId]);
            return false;
        } else {
            $this->create(['user_id' => $userId, 'medicine_id' => $medicineId]);
            return true;
        }
    }
}
