<?php
namespace App\Models;

use App\Core\Model;

/**
 * OrderItem Model
 */
class OrderItem extends Model {
    protected $table = 'order_items';

    /**
     * Get items for a specific order
     */
    public function getByOrderId($orderId) {
        $stmt = $this->db->prepare("SELECT oi.*, m.name, m.image 
                                    FROM `{$this->table}` oi 
                                    JOIN medicines m ON oi.medicine_id = m.id 
                                    WHERE oi.order_id = ?");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }
}
