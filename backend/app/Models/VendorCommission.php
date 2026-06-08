<?php
namespace App\Models;

use App\Core\Model;

/**
 * VendorCommission Model
 */
class VendorCommission extends Model {
    protected $table = 'vendor_commissions';
    protected $useSoftDelete = false;

    /**
     * Get commissions for a vendor
     */
    public function getByVendor($vendorId) {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE vendor_id = ? ORDER BY created_at DESC");
        $stmt->execute([$vendorId]);
        return $stmt->fetchAll();
    }

    /**
     * Get total pending commission for a vendor
     */
    public function getPendingTotal($vendorId) {
        $stmt = $this->db->prepare("SELECT SUM(commission_amount) as total FROM `{$this->table}` WHERE vendor_id = ? AND status = 'pending'");
        $stmt->execute([$vendorId]);
        $res = $stmt->fetch();
        return $res ? (float)$res['total'] : 0.00;
    }
}
