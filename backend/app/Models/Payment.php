<?php
namespace App\Models;

use App\Core\Model;

/**
 * Payment Model
 */
class Payment extends Model {
    protected $table = 'payments';
    
    public function getByOrderId($orderId) {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE order_id = ?");
        $stmt->execute([$orderId]);
        return $stmt->fetch();
    }

    public function getHistory($userId) {
        $stmt = $this->db->prepare("SELECT p.*, o.grand_total 
                                    FROM `{$this->table}` p 
                                    JOIN `orders` o ON p.order_id = o.id 
                                    WHERE o.user_id = ? 
                                    ORDER BY p.created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Find payment record by transaction ID (idempotency node)
     */
    public function findByTransactionId($transactionId) {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE transaction_id = ? LIMIT 1");
        $stmt->execute([$transactionId]);
        return $stmt->fetch();
    }
}
