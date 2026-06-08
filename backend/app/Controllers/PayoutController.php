<?php
namespace App\Controllers;

use App\Models\VendorCommission;
use App\Models\VendorPayout;
use App\Middleware\AuthGuard;
use Exception;

class PayoutController extends BaseController {
    
    private $commissionModel;
    private $payoutModel;

    public function __construct() {
        $this->commissionModel = new VendorCommission();
        $this->payoutModel = new VendorPayout();
    }

    public function getVendorCommissions() {
        try {
            AuthGuard::handle();
            $user = AuthGuard::getUser();
            $vendorId = $user['id'];

            $commissions = $this->commissionModel->getByVendor($vendorId);
            $pendingTotal = $this->commissionModel->getPendingTotal($vendorId);

            // Fetch payouts for total earnings calculation
            $payouts = $this->payoutModel->getByVendor($vendorId);
            $clearedAmount = 0;
            foreach ($payouts as $p) {
                if ($p['status'] === 'processed') $clearedAmount += $p['amount'];
            }

            $stats = [
                'total_earnings' => $clearedAmount + $pendingTotal,
                'cleared_amount' => $clearedAmount,
                'pending_amount' => $pendingTotal
            ];

            return $this->json(['success' => true, 'data' => ['commissions' => $commissions, 'stats' => $stats]]);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function requestPayout() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        $data = $this->getPostData();
        $amount = (float)($data['amount'] ?? 0);

        if ($amount <= 0) {
            return $this->json(['success' => false, 'message' => 'Invalid payout amount'], 400);
        }

        try {
            $pendingTotal = $this->commissionModel->getPendingTotal($user['id']);
            if ($amount > $pendingTotal) {
                return $this->json(['success' => false, 'message' => 'Insufficient pending balance'], 400);
            }

            $autoApproveLimit = 5000.00; // ₹5000 threshold

            if ($amount <= $autoApproveLimit) {
                // Auto-approve: instant settlement
                $id = $this->payoutModel->create([
                    'vendor_id' => $user['id'],
                    'amount' => $amount,
                    'status' => 'processed',
                    'processed_at' => date('Y-m-d H:i:s')
                ]);
                return $this->json(['success' => true, 'message' => "Payout of ₹{$amount} auto-processed instantly.", 'id' => $id]);
            } else {
                // Above threshold: requires admin approval
                $id = $this->payoutModel->create([
                    'vendor_id' => $user['id'],
                    'amount' => $amount,
                    'status' => 'pending'
                ]);
                return $this->json(['success' => true, 'message' => "Payout of ₹{$amount} submitted for admin approval (above ₹5000 threshold).", 'id' => $id]);
            }
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /admin/payouts/pending — List all pending vendor payout requests
     */
    public function getPendingPayouts() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        if ($user['role'] !== 'admin') return $this->json(['success' => false, 'message' => 'Forbidden'], 403);

        try {
            $db = \App\Core\Database::getInstance()->getConnection();
            $stmt = $db->query("
                SELECT vp.*, u.name as vendor_name, u.email as vendor_email
                FROM vendor_payouts vp
                JOIN users u ON vp.vendor_id = u.id
                ORDER BY vp.status = 'pending' DESC, vp.created_at DESC
            ");
            $payouts = $stmt->fetchAll();

            return $this->json(['success' => true, 'data' => ['payouts' => $payouts]]);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /admin/payouts/{id}/approve — Approve a vendor payout
     */
    public function approvePayout($id) {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        if ($user['role'] !== 'admin') return $this->json(['success' => false, 'message' => 'Forbidden'], 403);

        try {
            $this->payoutModel->update($id, [
                'status' => 'processed',
                'processed_at' => date('Y-m-d H:i:s')
            ]);
            return $this->json(['success' => true, 'message' => 'Payout approved and processed.']);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /admin/payouts/{id}/reject — Reject a vendor payout
     */
    public function rejectPayout($id) {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        if ($user['role'] !== 'admin') return $this->json(['success' => false, 'message' => 'Forbidden'], 403);

        try {
            $this->payoutModel->update($id, ['status' => 'rejected']);
            return $this->json(['success' => true, 'message' => 'Payout request rejected.']);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /admin/payouts/delivery — List all delivery boy payout requests
     */
    public function getDeliveryPayouts() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        if ($user['role'] !== 'admin') return $this->json(['success' => false, 'message' => 'Forbidden'], 403);

        try {
            $db = \App\Core\Database::getInstance()->getConnection();
            $stmt = $db->query("
                SELECT dp.*, u.name as rider_name, u.email as rider_email
                FROM delivery_payouts dp
                JOIN users u ON dp.rider_id = u.id
                ORDER BY dp.status = 'pending' DESC, dp.created_at DESC
            ");
            $payouts = $stmt->fetchAll();
            return $this->json(['success' => true, 'data' => ['payouts' => $payouts]]);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /admin/payouts/delivery/{id}/approve
     */
    public function approveDeliveryPayout($id) {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        if ($user['role'] !== 'admin') return $this->json(['success' => false, 'message' => 'Forbidden'], 403);

        try {
            $db = \App\Core\Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE delivery_payouts SET status = 'processed' WHERE id = ?");
            $stmt->execute([$id]);
            return $this->json(['success' => true, 'message' => 'Delivery payout approved.']);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /admin/payouts/delivery/{id}/reject
     */
    public function rejectDeliveryPayout($id) {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        if ($user['role'] !== 'admin') return $this->json(['success' => false, 'message' => 'Forbidden'], 403);

        try {
            $db = \App\Core\Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE delivery_payouts SET status = 'failed' WHERE id = ?");
            $stmt->execute([$id]);
            return $this->json(['success' => true, 'message' => 'Delivery payout rejected.']);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
