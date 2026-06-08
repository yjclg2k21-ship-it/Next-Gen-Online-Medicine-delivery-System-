<?php
namespace App\Models;

use App\Core\Model;

/**
 * Refund Model
 */
class Refund extends Model {
    protected $table = 'refunds';
    protected $useSoftDelete = false;

    /**
     * Get refunds for an order
     */
    public function getByOrder($orderId) {
        $sql = "SELECT r.* FROM `{$this->table}` r 
                JOIN payments p ON r.payment_id = p.id 
                WHERE p.order_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }
}
