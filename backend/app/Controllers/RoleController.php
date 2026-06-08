<?php
namespace App\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Exception;

class RoleController extends BaseController {
    
    private $roleModel;
    private $permissionModel;

    public function __construct() {
        $this->roleModel = new Role();
        $this->permissionModel = new Permission();
    }

    public function getRoles() {
        try {
            $roles = $this->roleModel->findAll();
            
            // Enrich roles with permission count and user count (simplified for now)
            foreach ($roles as &$role) {
                $permissions = $this->roleModel->getPermissions($role['id']);
                $role['permissions_count'] = count($permissions);
            }
            
            return $this->json(['success' => true, 'data' => ['roles' => $roles]]);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getPermissions() {
        try {
            $permissions = $this->permissionModel->findAll();
            return $this->json(['success' => true, 'data' => ['permissions' => $permissions]]);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function assignPermission() {
        $data = $this->getPostData();
        $roleId = $data['role_id'] ?? null;
        $permissionId = $data['permission_id'] ?? null;

        if (!$roleId || !$permissionId) {
            return $this->json(['success' => false, 'message' => 'role_id and permission_id required'], 400);
        }

        try {
            $this->roleModel->assignPermission($roleId, $permissionId);
            return $this->json([
                'success' => true, 
                'message' => 'Permission assigned successfully.',
                'synced_at' => date('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
