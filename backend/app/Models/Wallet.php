<?php
namespace App\Models;

use App\Core\Model;

/**
 * Wallet Model
 * Manages user wallet balance and history.
 */
class Wallet extends Model {
    protected $table = 'users'; // Wallet balance is stored in users table

    /**
     * Get balance for a user
     */
    public function getBalance($userId) {
        $stmt = $this->db->prepare("SELECT wallet_balance FROM `{$this->table}` WHERE id = ?");
        $stmt->execute([$userId]);
        $res = $stmt->fetch();
        return $res ? (float)$res['wallet_balance'] : 0.00;
    }

    /**
     * Add funds to wallet
     */
    public function addFunds($userId, $amount, $description = 'Refund/Credit') {
        try {
            $this->db->beginTransaction();
            
            // Update user table
            $stmt = $this->db->prepare("UPDATE `{$this->table}` SET wallet_balance = wallet_balance + ? WHERE id = ?");
            $stmt->execute([$amount, $userId]);

            // Log transaction
            $tx = new WalletTransaction();
            $tx->create([
                'user_id' => $userId,
                'amount' => $amount,
                'type' => 'credit',
                'description' => $description
            ]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Deduct funds from wallet
     */
    public function deductFunds($userId, $amount, $description = 'Payment/Debit') {
        try {
            $this->db->beginTransaction();
            
            // Check balance
            $balance = $this->getBalance($userId);
            if ($balance < $amount) throw new \Exception("Insufficient balance");

            // Update user table
            $stmt = $this->db->prepare("UPDATE `{$this->table}` SET wallet_balance = wallet_balance - ? WHERE id = ?");
            $stmt->execute([$amount, $userId]);

            // Log transaction
            $tx = new WalletTransaction();
            $tx->create([
                'user_id' => $userId,
                'amount' => $amount,
                'type' => 'debit',
                'description' => $description
            ]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
