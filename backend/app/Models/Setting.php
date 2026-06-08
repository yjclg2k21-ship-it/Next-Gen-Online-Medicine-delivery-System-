<?php
namespace App\Models;

use App\Core\Model;

/**
 * Setting Model
 */
class Setting extends Model {
    protected $table = 'settings';

    /**
     * Get value by key
     */
    public function get($key) {
        $stmt = $this->db->prepare("SELECT value FROM `{$this->table}` WHERE `key` = ?");
        $stmt->execute([$key]);
        $row = $stmt->fetch();
        return $row ? $row['value'] : null;
    }

    /**
     * Update or Create setting
     */
    public function set($key, $value, $description = null) {
        $existing = $this->get($key);
        if ($existing !== null) {
            $stmt = $this->db->prepare("UPDATE `{$this->table}` SET value = ?, description = ? WHERE `key` = ?");
            return $stmt->execute([$value, $description, $key]);
        } else {
            return $this->create([
                'key' => $key,
                'value' => $value,
                'description' => $description
            ]);
        }
    }
}
