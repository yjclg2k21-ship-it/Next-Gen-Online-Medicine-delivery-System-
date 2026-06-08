<?php
namespace App\Models;

use App\Core\Model;

/**
 * ClinicalRecommendation Model
 */
class ClinicalRecommendation extends Model {
    protected $table = 'clinical_recommendations';

    public function getByUser($userId) {
        $stmt = $this->db->prepare("SELECT r.*, m.name as medicine_name, c.name as category_name 
                                    FROM `{$this->table}` r 
                                    LEFT JOIN `medicines` m ON r.medicine_id = m.id 
                                    LEFT JOIN `categories` c ON r.category_id = c.id 
                                    WHERE r.user_id = ? 
                                    ORDER BY r.score DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
