<?php
namespace App\Models;

use App\Core\Model;

/**
 * WalletTransaction Model
 */
class WalletTransaction extends Model {
    protected $table = 'wallet_transactions';
    protected $useSoftDelete = false;

    /**
     * Get history for a user
     */
    public function getByUser($userId) {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
