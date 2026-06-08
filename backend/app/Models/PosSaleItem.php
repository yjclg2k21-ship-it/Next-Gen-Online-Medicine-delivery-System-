<?php
namespace App\Models;

use App\Core\Model;

/**
 * PosSaleItem Model
 */
class PosSaleItem extends Model {
    protected $table = 'pos_sale_items';
    protected $useSoftDelete = false;

    /**
     * Get items by sale ID
     */
    public function getBySale($saleId) {
        $stmt = $this->db->prepare("SELECT i.*, m.name as medicine_name FROM `{$this->table}` i 
                                    JOIN medicines m ON i.medicine_id = m.id 
                                    WHERE i.pos_sale_id = ?");
        $stmt->execute([$saleId]);
        return $stmt->fetchAll();
    }
}
