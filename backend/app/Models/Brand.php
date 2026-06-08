<?php
namespace App\Models;

use App\Core\Model;

/**
 * Brand Model
 */
class Brand extends Model {
    protected $table = 'brands';
    protected $useSoftDelete = false;

    /**
     * Get active brands
     */
    public function getActive() {
        $stmt = $this->db->query("SELECT * FROM `{$this->table}` WHERE status = 1 ORDER BY name ASC");
        return $stmt->fetchAll();
    }
}
