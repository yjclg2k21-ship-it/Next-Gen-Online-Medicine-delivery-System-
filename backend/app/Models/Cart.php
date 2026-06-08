<?php
namespace App\Models;

use App\Core\Model;

/**
 * Cart Model
 */
class Cart extends Model {
    protected $table = 'cart_items';

    /**
     * Get cart for a user with medicine details
     */
    public function getForUser($userId) {
        $stmt = $this->db->prepare("SELECT ci.*, m.name, m.price, m.image, m.salt, m.requires_prescription, m.vendor_id, u.name as vendor_name, b.type as brand_type 
                                    FROM `{$this->table}` ci 
                                    JOIN `medicines` m ON ci.medicine_id = m.id 
                                    LEFT JOIN `users` u ON m.vendor_id = u.id
                                    LEFT JOIN `brands` b ON m.brand_id = b.id
                                    WHERE ci.user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Clear cart for a user
     */
    public function clear($userId) {
        $stmt = $this->db->prepare("DELETE FROM `{$this->table}` WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }

    /**
     * Add item to cart or update quantity if exists (Atomic)
     */
    public function add($userId, $medicineId, $quantity = 1) {
        $stmt = $this->db->prepare("SELECT id, quantity FROM `{$this->table}` WHERE user_id = ? AND medicine_id = ?");
        $stmt->execute([$userId, $medicineId]);
        $existing = $stmt->fetch();

        if ($existing) {
            $newQty = $existing['quantity'] + $quantity;
            $stmt = $this->db->prepare("UPDATE `{$this->table}` SET quantity = ? WHERE id = ?");
            return $stmt->execute([$newQty, $existing['id']]);
        } else {
            $stmt = $this->db->prepare("INSERT INTO `{$this->table}` (user_id, medicine_id, quantity) VALUES (?, ?, ?)");
            return $stmt->execute([$userId, $medicineId, $quantity]);
        }
    }
}
