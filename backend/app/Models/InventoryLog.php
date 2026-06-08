<?php
namespace App\Models;

use App\Core\Model;

/**
 * InventoryLog Model
 */
class InventoryLog extends Model {
    protected $table = 'inventory_logs';

    /**
     * Log a stock change
     */
    public function log($medicineId, $vendorId, $qty, $reason) {
        return $this->create([
            'medicine_id' => $medicineId,
            'vendor_id' => $vendorId,
            'change_qty' => $qty,
            'reason' => $reason
        ]);
    }

    /**
     * Get logs for a specific medicine
     */
    public function getByMedicine($medicineId) {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE medicine_id = ? ORDER BY created_at DESC");
        $stmt->execute([$medicineId]);
        return $stmt->fetchAll();
    }
}
