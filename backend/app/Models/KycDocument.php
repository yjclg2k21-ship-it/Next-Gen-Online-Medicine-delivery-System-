<?php
namespace App\Models;

use App\Core\Model;

/**
 * KycDocument Model
 */
class KycDocument extends Model {
    protected $table = 'kyc_documents';
    protected $useSoftDelete = false;

    /**
     * Get documents by user ID
     */
    public function getByUser($userId) {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Update status of a document
     */
    public function updateStatus($id, $status, $reason = null) {
        $data = [
            'status' => $status,
            'verified_at' => ($status === 'verified') ? date('Y-m-d H:i:s') : null,
            'rejection_reason' => $reason
        ];
        return $this->update($id, $data);
    }
}
