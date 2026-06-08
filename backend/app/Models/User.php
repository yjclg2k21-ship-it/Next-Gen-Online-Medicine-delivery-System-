<?php
namespace App\Models;

use App\Core\Model;

/**
 * User Model
 */
class User extends Model {
    protected $table = 'users';
    protected $useSoftDelete = true;

    // Role Constants
    const ROLE_ADMIN = 'admin';
    const ROLE_VENDOR = 'vendor';
    const ROLE_DELIVERY = 'delivery';
    const ROLE_USER = 'user';

    /**
     * Role Helpers
     */
    public static function isAdmin($user) {
        return ($user['role'] ?? '') === self::ROLE_ADMIN;
    }

    public static function isVendor($user) {
        return ($user['role'] ?? '') === self::ROLE_VENDOR;
    }

    public static function isDelivery($user) {
        return ($user['role'] ?? '') === self::ROLE_DELIVERY;
    }

    public static function isUser($user) {
        return ($user['role'] ?? '') === self::ROLE_USER;
    }

    /**
     * Find user by email
     */
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE email = ? AND deleted_at IS NULL");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    /**
     * Get polymorphic user profile data based on role
     */
    public function getUserProfile($id) {
        $user = $this->findById($id);
        if (!$user) return null;

        $role = $user['role'] ?? self::ROLE_USER;
        $profileTable = 'user_profiles';

        if ($role === self::ROLE_VENDOR) {
            $profileTable = 'vendor_profiles';
        } elseif ($role === self::ROLE_DELIVERY) {
            $profileTable = 'delivery_profiles';
        }

        $stmt = $this->db->prepare("SELECT * FROM `{$profileTable}` WHERE user_id = ?");
        $stmt->execute([$id]);
        $user['profile'] = $stmt->fetch() ?: null;
        
        return $user;
    }

    /**
     * Get user addresses
     */
    public function getAddresses($id) {
        $stmt = $this->db->prepare("SELECT * FROM `addresses` WHERE user_id = ? AND deleted_at IS NULL");
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }

    /**
     * Get Wallet Balance
     */
    public function getWalletBalance($id) {
        $stmt = $this->db->prepare("SELECT wallet_balance FROM `users` WHERE id = ?");
        $stmt->execute([$id]);
        $res = $stmt->fetch();
        return $res ? (float)$res['wallet_balance'] : 0.0;
    }
}
