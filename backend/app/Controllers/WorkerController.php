<?php
namespace App\Controllers;

use App\Core\ResponseHandler;
use App\Models\Medicine;
use App\Models\Order;
use App\Models\VendorCommission;
use App\Models\VendorPayout;
use App\Services\SLATracker;
use App\Models\AuditLog;

/**
 * Worker Controller — Aligned with Unified Model Architecture
 */
class WorkerController extends BaseController {
    
    private $medicineModel;
    private $orderModel;
    private $auditModel;

    public function __construct() {
        $this->medicineModel = new Medicine();
        $this->orderModel = new Order();
        $this->auditModel = new AuditLog();
    }

    /**
     * Daily Cron Job
     */
    public function runDailyTasks() {
        $today = date('Y-m-d');
        
        // 1. Mark expired medicines
        // Using direct DB via model instance for bulk update
        $db = \App\Core\Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE medicines SET status = 0, approval_status = 'expired' WHERE expiry_date < ? AND status = 1");
        $stmt->execute([$today]);
        $expiredCount = $stmt->rowCount();

        if ($expiredCount > 0) {
            $this->auditModel->log(0, "SYSTEM_AUTO_MAINTENANCE", 'System', 0, [
                'expired_count' => $expiredCount, 
                'date' => $today
            ]);
        }

        return ResponseHandler::success([
            'tasks' => [
                'expiry_check' => ['status' => 'completed', 'affected_rows' => $expiredCount]
            ]
        ], 'Daily system maintenance completed.');
    }

    /**
     * SLA Breach Monitor
     */
    public function checkSLA() {
        $db = \App\Core\Database::getInstance()->getConnection();
        $stmt = $db->query(
            "SELECT o.id, o.user_id, o.is_emergency, o.delivery_method_id, dm.sla_hours, dm.sla_minutes, o.created_at 
             FROM orders o 
             JOIN delivery_methods dm ON o.delivery_method_id = dm.id 
             WHERE o.status NOT IN ('delivered', 'cancelled')"
        );
        
        $breaches = 0;
        $emergencyBreaches = 0;
        $now = time();

        while ($order = $stmt->fetch()) {
            $isEmergency = ($order['is_emergency'] == 1 || $order['delivery_method_id'] == 3);
            
            if ($isEmergency) {
                // Emergency: use sla_minutes (default 40 minutes)
                $slaMinutes = !empty($order['sla_minutes']) ? (int)$order['sla_minutes'] : 40;
                $deadline = strtotime($order['created_at']) + ($slaMinutes * 60);
                
                if ($now > $deadline) {
                    $this->auditModel->log($order['user_id'], "EMERGENCY_SLA_BREACH", 'Order', $order['id'], [
                        'deadline_expired' => date('Y-m-d H:i:s', $deadline),
                        'sla_minutes' => $slaMinutes,
                        'breach_type' => 'EMERGENCY_SLA_BREACH'
                    ]);
                    $emergencyBreaches++;
                    $breaches++;
                }
            } else {
                // Standard: use sla_hours
                $deadline = strtotime($order['created_at']) + ($order['sla_hours'] * 3600);
                if ($now > $deadline) {
                    $this->auditModel->log($order['user_id'], "SLA_BREACH_DETECTED", 'Order', $order['id'], ['deadline_expired' => date('Y-m-d H:i:s', $deadline)]);
                    $breaches++;
                }
            }
        }

        return ResponseHandler::success([
            'sla_breaches_detected' => $breaches,
            'emergency_breaches' => $emergencyBreaches
        ], $breaches > 0 ? "Critical SLA breaches identified." : "No SLA breaches detected.");
    }

    /**
     * Weekly Auto-Payout — Settles all vendor pending commissions every Monday
     * POST /worker/weekly-payout
     */
    public function weeklyPayout() {
        $db = \App\Core\Database::getInstance()->getConnection();
        $commissionModel = new VendorCommission();
        $payoutModel = new VendorPayout();

        // Get all vendors with pending commissions
        $stmt = $db->query("
            SELECT vendor_id, SUM(commission_amount) as pending_total 
            FROM vendor_commissions 
            WHERE status = 'pending' 
            GROUP BY vendor_id 
            HAVING pending_total > 0
        ");
        $vendors = $stmt->fetchAll();

        if (empty($vendors)) {
            return ResponseHandler::success(['settled' => 0], 'No pending commissions to settle.');
        }

        $settledCount = 0;
        $totalAmount = 0;

        foreach ($vendors as $vendor) {
            $amount = (float)$vendor['pending_total'];
            
            // Create payout record (auto-processed)
            $payoutModel->create([
                'vendor_id' => $vendor['vendor_id'],
                'amount' => $amount,
                'status' => 'processed',
                'processed_at' => date('Y-m-d H:i:s')
            ]);

            // Mark commissions as settled
            $updateStmt = $db->prepare("UPDATE vendor_commissions SET status = 'settled' WHERE vendor_id = ? AND status = 'pending'");
            $updateStmt->execute([$vendor['vendor_id']]);

            $settledCount++;
            $totalAmount += $amount;
        }

        $this->auditModel->log(0, 'WEEKLY_AUTO_PAYOUT', 'System', 0, [
            'vendors_settled' => $settledCount,
            'total_amount' => $totalAmount,
            'cycle_date' => date('Y-m-d')
        ]);

        return ResponseHandler::success([
            'settled' => $settledCount,
            'total_amount' => $totalAmount,
            'cycle' => date('Y-m-d')
        ], "Weekly payout completed. {$settledCount} vendors settled for ₹{$totalAmount}.");
    }
}
