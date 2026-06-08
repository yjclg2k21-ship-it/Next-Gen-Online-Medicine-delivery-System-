<?php
namespace App\Models;

use App\Core\Model;

/**
 * Vendor Model (Uses user table filtered by role)
 */
class Vendor extends Model {
    protected $table = 'users';

    /**
     * Get orders assigned to a vendor
     */
    public function getOrders($vendorId) {
        $stmt = $this->db->prepare("SELECT * FROM `orders` 
                                    WHERE vendor_id = ? AND deleted_at IS NULL 
                                    ORDER BY created_at DESC");
        $stmt->execute([$vendorId]);
        return $stmt->fetchAll();
    }

    /**
     * Get inventory items for a vendor
     */
    public function getInventory($vendorId) {
        $stmt = $this->db->prepare("SELECT * FROM `medicines` 
                                    WHERE vendor_id = ? AND deleted_at IS NULL");
        $stmt->execute([$vendorId]);
        return $stmt->fetchAll();
    }

    /**
     * Get pharmacy profile details
     */
    public function getProfile($vendorId) {
        $stmt = $this->db->prepare("SELECT * FROM `vendor_profiles` WHERE user_id = ?");
        $stmt->execute([$vendorId]);
        return $stmt->fetch();
    }
}
