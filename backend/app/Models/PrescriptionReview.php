<?php
namespace App\Models;

use App\Core\Model;

/**
 * PrescriptionReview Model
 */
class PrescriptionReview extends Model {
    protected $table = 'prescription_reviews';

    /**
     * Get reviews for a specific prescription
     */
    public function getByPrescriptionId($prescriptionId) {
        $stmt = $this->db->prepare("SELECT pr.*, u.name as vendor_name 
                                    FROM `{$this->table}` pr 
                                    JOIN users u ON pr.vendor_id = u.id 
                                    WHERE pr.prescription_id = ?");
        $stmt->execute([$prescriptionId]);
        return $stmt->fetchAll();
    }
}
