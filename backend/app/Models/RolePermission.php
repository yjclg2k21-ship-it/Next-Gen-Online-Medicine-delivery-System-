<?php
namespace App\Models;

use App\Core\Model;

class RolePermission extends Model {
    protected $table = 'role_permissions';

    public function getByRole($role) {
        $stmt = $this->db->prepare("SELECT permission FROM `{$this->table}` WHERE role = ?");
        $stmt->execute([$role]);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
}
