<?php
namespace App\Compliance;

use App\Core\Database;

/**
 * PrescriptionVerifier — Enhanced Clinical Validation
 * Validates: approval status, expiry date, and reuse limits.
 */
class PrescriptionVerifier {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Check if a cart requires a verified prescription and if it's valid.
     * Returns array of medicines missing a valid prescription.
     */
    public function verifyCart(int $userId, array $cartItems): array {
        $missingRx = [];
        foreach ($cartItems as $item) {
            if (!empty($item['requires_prescription'])) {
                $prescriptionId = $item['prescription_id'] ?? null;
                $validation = $this->validatePrescription($userId, $prescriptionId);
                if (!$validation['valid']) {
                    $missingRx[] = [
                        'name' => $item['name'],
                        'reason' => $validation['reason']
                    ];
                }
            }
        }
        return $missingRx;
    }

    /**
     * Full validation of a prescription — checks approval, expiry, and reuse.
     */
    public function validatePrescription(int $userId, ?int $prescriptionId): array {
        if (!$prescriptionId) {
            return ['valid' => false, 'reason' => 'No prescription linked.'];
        }

        $stmt = $this->db->prepare("SELECT * FROM prescriptions WHERE id = ? AND user_id = ?");
        $stmt->execute([$prescriptionId, $userId]);
        $rx = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$rx) {
            return ['valid' => false, 'reason' => 'Prescription not found or does not belong to user.'];
        }

        // Status check
        if ($rx['status'] !== 'approved') {
            return ['valid' => false, 'reason' => 'Prescription not yet approved by pharmacist. Status: ' . $rx['status']];
        }

        // Expiry check (explicit expiry_date if column exists)
        if (!empty($rx['expiry_date'])) {
            if (strtotime($rx['expiry_date']) < time()) {
                $daysExpired = (int)((time() - strtotime($rx['expiry_date'])) / 86400);
                return ['valid' => false, 'reason' => "Prescription expired {$daysExpired} day(s) ago on {$rx['expiry_date']}."];
            }
        } else {
            // Fallback: 180 days from issued_date or created_at
            $baseDate = $rx['issued_date'] ?? $rx['created_at'];
            $issuedAt = strtotime($baseDate);
            $ageInDays = (time() - $issuedAt) / 86400;
            if ($ageInDays > 180) {
                return ['valid' => false, 'reason' => 'Prescription older than 180 days (no explicit expiry set).'];
            }
        }

        // Reuse prevention check (only if columns exist)
        if (array_key_exists('times_used', $rx) && array_key_exists('max_uses', $rx)) {
            $timesUsed = (int)($rx['times_used'] ?? 0);
            $maxUses = (int)($rx['max_uses'] ?? 1);
            if ($timesUsed >= $maxUses) {
                return ['valid' => false, 'reason' => "Prescription reuse limit reached ({$timesUsed}/{$maxUses}). Upload a new prescription."];
            }
        }

        return ['valid' => true, 'reason' => '', 'prescription' => $rx];
    }

    /**
     * Simple boolean check (backward compatible)
     */
    public function isValidPrescription(int $userId, ?int $prescriptionId): bool {
        $result = $this->validatePrescription($userId, $prescriptionId);
        return $result['valid'];
    }

    /**
     * Logic Parity Wrapper — verifies a medicine has a valid prescription
     */
    public function verifyMedicine($userId, $medicineId) {
        try {
            $stmt = $this->db->prepare(
                "SELECT id FROM prescriptions 
                 WHERE user_id = ? AND status = 'approved' 
                   AND (expiry_date IS NULL OR expiry_date >= CURDATE())
                   AND times_used < max_uses
                 ORDER BY created_at DESC LIMIT 1"
            );
            $stmt->execute([$userId]);
            $rxId = $stmt->fetchColumn();
        } catch (\Exception $e) {
            // Fallback if new columns don't exist
            $stmt = $this->db->prepare(
                "SELECT id FROM prescriptions 
                 WHERE user_id = ? AND status = 'approved'
                 ORDER BY created_at DESC LIMIT 1"
            );
            $stmt->execute([$userId]);
            $rxId = $stmt->fetchColumn();
        }
        
        $result = $this->validatePrescription($userId, (int)$rxId);
        return [
            'valid' => $result['valid'],
            'reason' => $result['reason'],
            'prescription_id' => $rxId ?: null
        ];
    }

    /**
     * Record that a prescription was used for an order (called at checkout)
     */
    public function recordUsage(int $prescriptionId, int $orderId): bool {
        try {
            // Check if already recorded
            $stmt = $this->db->prepare("SELECT id FROM prescription_usage_log WHERE prescription_id = ? AND order_id = ?");
            $stmt->execute([$prescriptionId, $orderId]);
            if ($stmt->fetch()) return true; // Already recorded

            $stmt = $this->db->prepare("INSERT INTO prescription_usage_log (prescription_id, order_id) VALUES (?, ?)");
            $stmt->execute([$prescriptionId, $orderId]);

            // Increment usage counter
            $stmt = $this->db->prepare("UPDATE prescriptions SET times_used = times_used + 1, last_used_at = NOW() WHERE id = ?");
            $stmt->execute([$prescriptionId]);

            return true;
        } catch (\Exception $e) {
            // Tables don't exist yet — silently skip
            return false;
        }
    }

    /**
     * Get expiry warning for prescriptions expiring within N days
     */
    public function getExpiringPrescriptions(int $userId, int $withinDays = 30): array {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, doctor_name, expiry_date, 
                        DATEDIFF(expiry_date, CURDATE()) as days_remaining
                 FROM prescriptions 
                 WHERE user_id = ? 
                   AND status = 'approved'
                   AND deleted_at IS NULL
                   AND expiry_date IS NOT NULL
                   AND expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
                 ORDER BY expiry_date ASC"
            );
            $stmt->execute([$userId, $withinDays]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }
}
