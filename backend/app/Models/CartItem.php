<?php
namespace App\Models;

use App\Core\Model;

class CartItem extends Model {
    protected $table = 'cart_items';

    public function getForUser($userId) {
        $stmt = $this->db->prepare("SELECT ci.*, m.name, m.price, m.image, m.requires_prescription 
                                    FROM `{$this->table}` ci 
                                    JOIN `medicines` m ON ci.medicine_id = m.id 
                                    WHERE ci.user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
