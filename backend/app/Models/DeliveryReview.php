<?php
namespace App\Models;

use App\Core\Model;

/**
 * DeliveryReview Model
 */
class DeliveryReview extends Model {
    protected $table = 'delivery_reviews';

    public function getByRider($riderId) {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE rider_id = ? ORDER BY created_at DESC");
        $stmt->execute([$riderId]);
        return $stmt->fetchAll();
    }

    public function getAverageRating($riderId) {
        $stmt = $this->db->prepare("SELECT AVG(rating) as avg_rating FROM `{$this->table}` WHERE rider_id = ?");
        $stmt->execute([$riderId]);
        $res = $stmt->fetch();
        return (float)($res['avg_rating'] ?? 0);
    }
}
