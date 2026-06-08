<?php
namespace App\Models;

use App\Core\Model;

/**
 * PosSale Model
 */
class PosSale extends Model {
    protected $table = 'pos_sales';
    protected $useSoftDelete = false;

    /**
     * Get sales by vendor ID
     */
    public function getByVendor($vendorId) {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE vendor_id = ? ORDER BY created_at DESC");
        $stmt->execute([$vendorId]);
        return $stmt->fetchAll();
    }

    /**
     * Get details of a sale including items
     */
    public function getDetails($saleId) {
        $sale = $this->findById($saleId);
        if (!$sale) return null;

        $itemModel = new PosSaleItem();
        $sale['items'] = $itemModel->getBySale($saleId);
        return $sale;
    }
}
