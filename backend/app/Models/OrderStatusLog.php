<?php
namespace App\Models;

use App\Core\Model;

/**
 * OrderStatusLog Model
 */
class OrderStatusLog extends Model {
    protected $table = 'order_status_logs';

    /**
     * Get history for an order
     */
    public function getHistory($orderId) {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE order_id = ? ORDER BY created_at ASC");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }
}
