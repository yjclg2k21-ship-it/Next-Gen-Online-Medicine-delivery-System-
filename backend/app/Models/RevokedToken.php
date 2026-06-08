<?php
namespace App\Models;

use App\Core\Model;

/**
 * Revoked Token Model
 * Manages persistent storage for blacklisted (logged out) JWT tokens.
 */
class RevokedToken extends Model {
    protected $table = 'revoked_tokens';
    protected $useSoftDelete = false;

    public function isRevoked($tokenHash) {
        $stmt = $this->db->prepare("SELECT id FROM {$this->table} WHERE token_hash = ? AND expires_at > NOW()");
        $stmt->execute([$tokenHash]);
        return $stmt->fetch() !== false;
    }

    public function revoke($tokenHash, $expiry) {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (token_hash, expires_at) VALUES (?, FROM_UNIXTIME(?))");
        return $stmt->execute([$tokenHash, $expiry]);
    }

    public function cleanup() {
        return $this->db->exec("DELETE FROM {$this->table} WHERE expires_at < NOW()");
    }
}
