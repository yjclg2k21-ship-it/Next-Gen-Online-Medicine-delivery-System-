<?php
namespace App\Models;

use App\Core\Model;

/**
 * VendorPayout Model – full implementation
 * Table: vendor_payouts
 * Handles creation, status updates, and retrieval of payout records.
 */
class VendorPayout extends Model {
    protected $table = 'vendor_payouts';
    protected $primaryKey = 'id';

    /**
     * Create a new payout record.
     * @param array $data Expected keys: vendor_id, amount, status (optional), payout_date (optional)
     */
    public function createPayout(array $data) {
        $data = array_merge([
            'status' => 'pending',
            'payout_date' => null,
        ], $data);
        return $this->insert($data);
    }

    /**
     * Update payout status.
     */
    public function updateStatus($id, $status) {
        return $this->update($id, ['status' => $status]);
    }

    /**
     * Retrieve payouts for a vendor.
     */
    public function getByVendor($vendorId, $limit = 0) {
        $sql = "SELECT * FROM `{$this->table}` WHERE vendor_id = ? ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$vendorId]);
        $result = $stmt->fetchAll();
        if ($limit > 0) {
            $result = array_slice($result, 0, $limit);
        }
        return $result;
    }

    /**
     * Get pending payouts for a vendor.
     */
    public function getPending($vendorId, $limit = 0) {
        $sql = "SELECT * FROM `{$this->table}` WHERE vendor_id = ? AND status = 'pending' ORDER BY created_at ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$vendorId]);
        $result = $stmt->fetchAll();
        if ($limit > 0) {
            $result = array_slice($result, 0, $limit);
        }
        return $result;
    }
}
