<?php
namespace App\Controllers;

use App\Core\Database;
use App\Core\ResponseHandler;
use App\Middleware\AuthGuard;

class ReportController extends BaseController {
    
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function financial() {
        AuthGuard::handle();
        if (AuthGuard::getUser()['role'] !== 'admin') return ResponseHandler::forbidden();

        try {
            $revenueStmt = $this->db->query("SELECT SUM(grand_total) as total FROM orders WHERE payment_status = 'paid'");
            $commissionStmt = $this->db->query("SELECT SUM(commission_amount) as total FROM vendor_commissions WHERE status = 'settled'");
            
            $report = [
                'total_revenue' => (float)($revenueStmt->fetch()['total'] ?? 0),
                'total_commissions' => (float)($commissionStmt->fetch()['total'] ?? 0),
                'monthly_breakdown' => [] // Could be expanded with GROUP BY MONTH
            ];
            return ResponseHandler::success($report, 'Financial report synchronized.');
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    public function inventory() {
        AuthGuard::handle();
        if (AuthGuard::getUser()['role'] !== 'admin') return ResponseHandler::forbidden();

        try {
            $stmt = $this->db->query("SELECT m.name, m.stock, v.pharmacy_name as vendor 
                                      FROM medicines m 
                                      JOIN vendor_profiles v ON m.vendor_id = v.user_id 
                                      WHERE m.stock < 20 ORDER BY m.stock ASC");
            $data = $stmt->fetchAll();
            return ResponseHandler::success(['low_stock' => $data], 'Inventory health report generated.');
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    public function analytical() {
        AuthGuard::handle();
        if (AuthGuard::getUser()['role'] !== 'admin') return ResponseHandler::forbidden();

        try {
            $stmt = $this->db->query("SELECT alert_type, severity, COUNT(*) as count FROM safety_alerts GROUP BY alert_type, severity");
            $alerts = $stmt->fetchAll();
            return ResponseHandler::success(['safety_trends' => $alerts], 'Analytical clinical data generated.');
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }
}
