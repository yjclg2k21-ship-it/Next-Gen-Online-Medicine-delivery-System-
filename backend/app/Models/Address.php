<?php
namespace App\Models;

use App\Core\Model;

class Address extends Model {
    protected $table = 'addresses';
    protected $useSoftDelete = true;

    public function getByUserId($userId) {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE user_id = ? AND deleted_at IS NULL");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getByUser($userId) {
        return $this->getByUserId($userId);
    }

    public function setDefault($userId, $addressId) {
        // Reset all to 0
        $stmt = $this->db->prepare("UPDATE `{$this->table}` SET is_default = 0 WHERE user_id = ?");
        $stmt->execute([$userId]);

        // Set one to 1
        $stmt = $this->db->prepare("UPDATE `{$this->table}` SET is_default = 1 WHERE id = ? AND user_id = ?");
        return $stmt->execute([$addressId, $userId]);
    }
}
