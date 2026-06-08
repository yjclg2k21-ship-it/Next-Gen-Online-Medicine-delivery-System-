<?php
namespace App\Models;

use App\Core\Model;

/**
 * Drug Interaction Model
 * Stores clinical data regarding medicine combination risks.
 */
class DrugInteraction extends Model {
    protected $table = 'drug_interactions';
    protected $useSoftDelete = false;

    /**
     * Check interaction between two medicines
     */
    public function findInteraction($idA, $idB) {
        $stmt = $this->db->prepare("
            SELECT * FROM `{$this->table}` 
            WHERE (medicine_a_id = ? AND medicine_b_id = ?) 
               OR (medicine_a_id = ? AND medicine_b_id = ?)
        ");
        $stmt->execute([$idA, $idB, $idB, $idA]);
        return $stmt->fetch();
    }
}
