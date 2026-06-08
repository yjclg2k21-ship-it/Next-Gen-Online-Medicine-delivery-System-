<?php
namespace App\Models;

use App\Core\Model;

/**
 * Banner Model
 */
class Banner extends Model {
    protected $table = 'banners';
    protected $useSoftDelete = false;

    /**
     * Get active banners
     */
    public function getActive() {
        $stmt = $this->db->query("SELECT * FROM `{$this->table}` WHERE status = 'active' ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
}
