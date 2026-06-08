<?php
namespace App\Services;

use App\Core\Database;

/**
 * CommissionEngine Service
 * Calculates and settles vendor commissions for every order.
 */
class CommissionEngine {
    private $db;
    private float $defaultRate = 10.0; // 10% default commission

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Calculate and record commission for a placed order.
     */
    public function calculate(int $orderId, int $vendorId): void {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch();
        if (!$order) return;

        // Get rate from settings table
        $rateStmt = $this->db->prepare("SELECT value FROM settings WHERE `key` = 'vendor_commission_percent'");
        $rateStmt->execute();
        $rate = (float)($rateStmt->fetchColumn() ?? $this->defaultRate);

        $commission = round(($order['total_amount'] * $rate) / 100, 2);

        $this->db->prepare(
            "INSERT IGNORE INTO vendor_commissions (vendor_id, order_id, commission_amount) VALUES (?, ?, ?)"
        )->execute([$vendorId, $orderId, $commission]);
    }

    /**
     * Mark commission as settled when vendor is paid out.
     */
    public function settle(int $orderId): void {
        $this->db->prepare("UPDATE vendor_commissions SET status = 'settled' WHERE order_id = ?")
                 ->execute([$orderId]);
    }

    /**
     * Get total pending commission amount for a vendor.
     */
    public function getPendingAmount(int $vendorId): float {
        $stmt = $this->db->prepare("SELECT SUM(commission_amount) FROM vendor_commissions WHERE vendor_id = ? AND status = 'pending'");
        $stmt->execute([$vendorId]);
        return (float)$stmt->fetchColumn();
    }
}
