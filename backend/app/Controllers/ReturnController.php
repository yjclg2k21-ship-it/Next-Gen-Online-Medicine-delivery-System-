<?php
namespace App\Controllers;

use App\Core\ResponseHandler;
use App\Middleware\AuthGuard;
use App\Middleware\RoleCheck;
use App\Models\ReturnRequest;
use Exception;

/**
 * Return Controller
 * Manages order returns, product inspections, and logistics for replacement.
 */
class ReturnController extends BaseController {

    private $returnModel;

    public function __construct() {
        $this->returnModel = new ReturnRequest();
    }

    public function index() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        try {
            $returns = $this->returnModel->getByUser($user['id']);
            return ResponseHandler::success(['returns' => $returns]);
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    public function vendorIndex() {
        AuthGuard::handle();
        RoleCheck::handle('vendor');
        $user = AuthGuard::getUser();
        try {
            $returns = $this->returnModel->getByVendor($user['id']);
            return ResponseHandler::success(['returns' => $returns]);
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    public function requestReturn($orderId) {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        try {
            $data = $this->getPostData();
            if (empty($data['reason'])) {
                return ResponseHandler::badRequest('Reason is required for pharmaceutical reversal.');
            }

            // Verify order belongs to user and is delivered
            $orderModel = new \App\Models\Order();
            $order = $orderModel->findById($orderId);
            if (!$order || $order['user_id'] != $user['id']) {
                return ResponseHandler::forbidden('You do not have authorization for this clinical node.');
            }
            if ($order['status'] !== 'delivered') {
                return ResponseHandler::badRequest('Pharmaceutical reversal can only be initiated for delivered orders.');
            }

            // Create return record
            $id = $this->returnModel->create([
                'order_id' => $orderId,
                'user_id' => $user['id'],
                'reason' => $data['reason'],
                'status' => 'pending'
            ]);

            // Update order status
            $orderModel->update($orderId, ['status' => 'return_requested']);

            return ResponseHandler::success(['id' => $id], 'Return request protocol initiated. Status transitioned to return_requested.', 201);
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    public function updateStatus($id) {
        AuthGuard::handle();
        $admin = AuthGuard::getUser();
        if ($admin['role'] !== 'admin' && $admin['role'] !== 'vendor') return ResponseHandler::forbidden();

        try {
            $data = $this->getPostData();
            $status = $data['status'] ?? 'pending';
            
            $returnRequest = $this->returnModel->findById($id);
            if (!$returnRequest) {
                return ResponseHandler::notFound("Return request not found.");
            }

            $this->returnModel->update($id, ['status' => $status]);

            // Sync order status based on return request status
            $orderModel = new \App\Models\Order();
            if ($status === 'approved' || $status === 'completed') {
                $orderModel->update($returnRequest['order_id'], ['status' => 'returned']);
            } else if ($status === 'rejected') {
                $orderModel->update($returnRequest['order_id'], ['status' => 'delivered']);
            }

            return ResponseHandler::success([], "Return #$id protocol transitioned to $status.");
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    public function assignRider($id) {
        AuthGuard::handle();
        RoleCheck::handle('admin');
        $data = $this->getPostData();
        $riderId = $data['rider_id'] ?? null;

        if (!$riderId) return ResponseHandler::badRequest("Rider node ID required.");

        try {
            $this->returnModel->update($id, [
                'rider_id' => $riderId,
                'status' => 'assigned_for_pickup'
            ]);
            return ResponseHandler::success([], "Rider assigned for clinical return pickup.");
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }
}
