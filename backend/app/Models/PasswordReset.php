<?php
namespace App\Models;

use App\Core\Model;

/**
 * Password Reset Model
 * Manages secure recovery tokens for clinical accounts.
 */
class PasswordReset extends Model {
    protected $table = 'password_resets';

    public function __construct() {
        parent::__construct();
        $this->ensureExpiresAtColumn();
    }

    /**
     * Ensure expires_at column exists (auto-migration safety net)
     */
    private function ensureExpiresAtColumn() {
        try {
            $stmt = $this->db->query("SHOW COLUMNS FROM `{$this->table}` LIKE 'expires_at'");
            if ($stmt->rowCount() === 0) {
                $this->db->exec("ALTER TABLE `{$this->table}` ADD COLUMN `expires_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `token`");
            }
        } catch (\Exception $e) {
            // Silently fail - table might not exist yet
        }
    }

    public function createToken($email) {
        // Delete old tokens for this email
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE email = ?");
        $stmt->execute([$email]);

        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $this->db->prepare("INSERT INTO {$this->table} (email, token, expires_at) VALUES (?, ?, ?)");
        if ($stmt->execute([$email, $token, $expiry])) {
            return $token;
        }
        return false;
    }

    public function verifyToken($token) {
        $stmt = $this->db->prepare("SELECT email FROM {$this->table} WHERE token = ? AND expires_at > NOW()");
        $stmt->execute([$token]);
        $result = $stmt->fetch();
        return $result ? $result['email'] : false;
    }

    public function deleteToken($token) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE token = ?");
        return $stmt->execute([$token]);
    }
}
