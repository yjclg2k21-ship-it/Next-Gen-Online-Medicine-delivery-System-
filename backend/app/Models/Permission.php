<?php
namespace App\Models;

use App\Core\Model;

/**
 * Permission Model
 */
class Permission extends Model {
    protected $table = 'permissions';
    protected $useSoftDelete = false;

    /**
     * Get Permission by slug
     */
    public function findBySlug($slug) {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }
}
