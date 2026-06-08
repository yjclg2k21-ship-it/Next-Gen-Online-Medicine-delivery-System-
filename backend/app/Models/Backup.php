<?php
namespace App\Models;

use App\Core\Model;

/**
 * Backup Model
 */
class Backup extends Model {
    protected $table = 'backups';

    /**
     * Get recent backups
     */
    public function getRecent($limit = 10) {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` ORDER BY created_at DESC LIMIT " . (int)$limit);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
