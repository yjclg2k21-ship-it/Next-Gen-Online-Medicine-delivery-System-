<?php
namespace App\Models;

use App\Core\Model;

/**
 * ReturnRequest Model
 */
class ReturnRequest extends Model {
    protected $table = 'return_requests'; // Aligned with DB table name
    protected $useSoftDelete = true;

    /**
     * Get for a vendor
     */
    public function getByVendor($vendorId) {
        $stmt = $this->db->prepare(
            "SELECT rr.*, o.grand_total, o.created_at as order_date, u.name as customer_name 
             FROM `{$this->table}` rr 
             JOIN orders o ON rr.order_id = o.id 
             JOIN users u ON rr.user_id = u.id
             WHERE o.vendor_id = ? AND rr.deleted_at IS NULL"
        );
        $stmt->execute([$vendorId]);
        return $stmt->fetchAll();
    }

    /**
     * Get for a user
     */
    public function getByUser($userId) {
        $stmt = $this->db->prepare(
            "SELECT rr.*, o.grand_total, o.created_at as order_date 
             FROM `{$this->table}` rr 
             JOIN orders o ON rr.order_id = o.id 
             WHERE rr.user_id = ? AND rr.deleted_at IS NULL"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
