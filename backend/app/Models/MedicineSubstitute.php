<?php
namespace App\Models;

use App\Core\Model;

/**
 * MedicineSubstitute Model
 */
class MedicineSubstitute extends Model {
    protected $table = 'medicine_substitutes';

    /**
     * Get substitutes for a medicine
     */
    public function getByMedicine($medicineId) {
        $stmt = $this->db->prepare("SELECT ms.*, m.name, m.price, m.image 
                                    FROM `{$this->table}` ms 
                                    JOIN medicines m ON ms.substitute_id = m.id 
                                    WHERE ms.medicine_id = ?");
        $stmt->execute([$medicineId]);
        return $stmt->fetchAll();
    }
}
