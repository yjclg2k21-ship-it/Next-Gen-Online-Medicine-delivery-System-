<?php
namespace App\Models;

use App\Core\Model;

/**
 * Prescription Model — Enhanced with expiry, linking, and reuse tracking
 */
class Prescription extends Model {
    protected $table = 'prescriptions';
    protected $useSoftDelete = true;

    /**
     * Get prescriptions for a specific user
     */
    public function getByUserId($userId) {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` 
                                    WHERE user_id = ? AND deleted_at IS NULL 
                                    ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Get only valid (non-expired, approved, reusable) prescriptions for a user
     */
    public function getValidForUser($userId) {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM `{$this->table}` 
                 WHERE user_id = ? 
                   AND deleted_at IS NULL 
                   AND status = 'approved'
                   AND (expiry_date IS NULL OR expiry_date >= CURDATE())
                   AND times_used < max_uses
                 ORDER BY created_at DESC"
            );
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            // Fallback if new columns don't exist
            $stmt = $this->db->prepare(
                "SELECT * FROM `{$this->table}` 
                 WHERE user_id = ? AND deleted_at IS NULL AND status = 'approved'
                 ORDER BY created_at DESC"
            );
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        }
    }

    /**
     * Get prescription reviews
     */
    public function getReviews($prescriptionId) {
        $stmt = $this->db->prepare("SELECT pr.*, u.name as vendor_name 
                                    FROM `prescription_reviews` pr 
                                    JOIN `users` u ON pr.vendor_id = u.id 
                                    WHERE pr.prescription_id = ?");
        $stmt->execute([$prescriptionId]);
        return $stmt->fetchAll();
    }

    /**
     * Get pending prescriptions for a vendor's medicines
     */
    public function getPendingForVendor($vendorId) {
        $stmt = $this->db->prepare(
            "SELECT DISTINCT p.*, 
                    u.name as patient_name,
                    p.doctor_name as doctor,
                    p.image_path as file_path,
                    CASE WHEN p.image_path LIKE '%.pdf' THEN 'pdf' ELSE 'image' END as file_type,
                    p.comment as notes,
                    p.created_at as uploaded_at
             FROM `{$this->table}` p
             LEFT JOIN orders o ON o.id = p.order_id
             LEFT JOIN order_items oi ON oi.order_id = p.order_id
             LEFT JOIN medicines m ON m.id = oi.medicine_id
             LEFT JOIN users u ON u.id = p.user_id
             WHERE (m.vendor_id = ? OR o.vendor_id = ?)
               AND p.deleted_at IS NULL
             ORDER BY p.created_at DESC"
        );
        $stmt->execute([$vendorId, $vendorId]);
        return $stmt->fetchAll();
    }

    /**
     * Get ALL prescriptions for a vendor (all statuses)
     */
    public function getAllForVendor($vendorId) {
        $stmt = $this->db->prepare(
            "SELECT DISTINCT p.*,
                    u.name as patient_name,
                    p.doctor_name as doctor,
                    p.image_path as file_path,
                    CASE WHEN p.image_path LIKE '%.pdf' THEN 'pdf' ELSE 'image' END as file_type,
                    p.comment as notes,
                    p.created_at as uploaded_at
             FROM `{$this->table}` p
             LEFT JOIN orders o ON o.id = p.order_id
             LEFT JOIN order_items oi ON oi.order_id = p.order_id
             LEFT JOIN medicines m ON m.id = oi.medicine_id
             LEFT JOIN users u ON u.id = p.user_id
             WHERE (m.vendor_id = ? OR o.vendor_id = ? OR p.order_id IS NULL)
               AND p.deleted_at IS NULL
             ORDER BY p.created_at DESC"
        );
        $stmt->execute([$vendorId, $vendorId]);
        return $stmt->fetchAll();
    }

    /**
     * Get medicines linked to a prescription via prescription_medicines table
     */
    public function getLinkedMedicines($prescriptionId) {
        try {
            $stmt = $this->db->prepare(
                "SELECT pm.*, m.name as resolved_name, m.price, m.image 
                 FROM `prescription_medicines` pm
                 LEFT JOIN `medicines` m ON m.id = pm.medicine_id
                 WHERE pm.prescription_id = ?"
            );
            $stmt->execute([$prescriptionId]);
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            // Table doesn't exist yet (migration pending)
            return [];
        }
    }

    /**
     * Get medicines linked via order_items (legacy linkage)
     */
    public function getMedicines($prescriptionId) {
        $stmt = $this->db->prepare(
            "SELECT m.name 
             FROM medicines m
             JOIN order_items oi ON oi.medicine_id = m.id
             JOIN orders o ON o.id = oi.order_id
             WHERE o.prescription_id = ?
             LIMIT 10"
        );
        $stmt->execute([$prescriptionId]);
        return array_column($stmt->fetchAll(), 'name');
    }

    /**
     * Get usage history for a prescription
     */
    public function getUsageLog($prescriptionId) {
        try {
            $stmt = $this->db->prepare(
                "SELECT pul.*, o.status as order_status, o.grand_total 
                 FROM `prescription_usage_log` pul
                 JOIN `orders` o ON o.id = pul.order_id
                 WHERE pul.prescription_id = ?
                 ORDER BY pul.used_at DESC"
            );
            $stmt->execute([$prescriptionId]);
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Check if prescription can be used (not expired, approved, within reuse limit)
     */
    public function canBeUsed($prescriptionId): bool {
        $rx = $this->findById($prescriptionId);
        if (!$rx) return false;
        if ($rx['status'] !== 'approved') return false;

        // Expiry check
        if (!empty($rx['expiry_date']) && strtotime($rx['expiry_date']) < time()) {
            return false;
        }
        // Fallback 180-day check
        $baseDate = $rx['issued_date'] ?? $rx['created_at'];
        if ($baseDate && ((time() - strtotime($baseDate)) / 86400) > 180) {
            return false;
        }

        // Reuse check
        $timesUsed = (int)($rx['times_used'] ?? 0);
        $maxUses = (int)($rx['max_uses'] ?? 1);
        return $timesUsed < $maxUses;
    }
}
