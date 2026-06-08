<?php
namespace App\Models;

use App\Core\Model;

/**
 * Role Model
 */
class Role extends Model {
    protected $table = 'roles';
    protected $useSoftDelete = false;

    /**
     * Get all permissions assigned to this role
     */
    public function getPermissions($roleId) {
        $sql = "SELECT p.* FROM permissions p 
                JOIN role_permissions rp ON p.id = rp.permission_id 
                WHERE rp.role_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$roleId]);
        return $stmt->fetchAll();
    }

    /**
     * Assign a permission to a role
     */
    public function assignPermission($roleId, $permissionId) {
        $stmt = $this->db->prepare("INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
        return $stmt->execute([$roleId, $permissionId]);
    }

    /**
     * Remove a permission from a role
     */
    public function removePermission($roleId, $permissionId) {
        $stmt = $this->db->prepare("DELETE FROM role_permissions WHERE role_id = ? AND permission_id = ?");
        return $stmt->execute([$roleId, $permissionId]);
    }
}
