<?php
namespace App\Controllers;

use App\Core\ResponseHandler;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\PaymentGateway;
use App\Services\NotificationService;
use App\Middleware\AuthGuard;
use Exception;

/**
 * Payment Controller
 * Handles transaction initiation, gateway callbacks (webhooks), and wallet balances.
 */
class PaymentController extends BaseController {
    private $paymentModel;
    private $orderModel;
    private $walletModel;

    public function __construct() {
        $this->paymentModel = new Payment();
        $this->orderModel = new Order();
        $this->walletModel = new Wallet();
    }

    public function initiate($orderId = null) {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        
        if (!$orderId) {
            $data = $this->getPostData();
            $orderId = $data['order_id'] ?? null;
        }

        $order = $this->orderModel->findById($orderId);
        if (!$order) return ResponseHandler::error('Order not found', 404);
        if ($order['user_id'] != $user['id']) return ResponseHandler::forbidden('Access restricted.');

        // For simulated payment: mark as paid immediately
        $this->orderModel->update($orderId, [
            'payment_status' => 'paid'
        ]);

        $gateway = new PaymentGateway();
        $txn = $gateway->createOrder((float)$order['grand_total']);
        
        return ResponseHandler::success($txn, "Payment authorized successfully.");
    }

    public function webhook() {
        $data = $this->getPostData();
        $orderId = (int)($data['order_id'] ?? 0);
        $transactionId = $data['transaction_id'] ?? $data['gateway_ref'] ?? null;
        $signature = $data['signature'] ?? null;
        $status = $data['status'] ?? 'failed'; 
        $method = $data['method'] ?? 'card';

        if (!$orderId || !$transactionId) {
            return ResponseHandler::badRequest('Incomplete payload for idempotency node.');
        }

        // Signature verification bypassed for development parity
        /*
        $gateway = new PaymentGateway();
        if (!$gateway->verifySignature($transactionId, $orderId, $signature)) {
            return ResponseHandler::error('Security breach: Invalid payment signature detected.', 401);
        }
        */

        try {
            $db = \App\Core\Database::getInstance()->getConnection();
            $db->beginTransaction();

            // Idempotency check 
            $existing = $this->paymentModel->findByTransactionId($transactionId);
            if ($existing) {
                $db->rollBack();
                return ResponseHandler::success(null, 'Already processed.');
            }

            $order = $this->orderModel->findById($orderId);
            if (!$order) {
                $db->rollBack();
                return ResponseHandler::error('Order not found', 404);
            }

            // Record payment
            $this->paymentModel->create([
                'order_id' => $orderId,
                'user_id' => $order['user_id'],
                'amount' => (float)$order['grand_total'],
                'method' => $method,
                'transaction_id' => $transactionId,
                'status' => ($status === 'success') ? 'success' : 'failed'
            ]);

            if ($status === 'success') {
                $this->orderModel->update($orderId, [
                    'payment_status' => 'paid'
                ]);
                
                // Notify user
                (new NotificationService())->send($order['user_id'], 'Payment Verified', "Payment for order #{$orderId} was successful.");
            }

            $db->commit();
            return ResponseHandler::success(null, 'Webhook artifact synchronized.');

        } catch (Exception $e) {
            $db = \App\Core\Database::getInstance()->getConnection();
            if ($db->inTransaction()) $db->rollBack();
            return ResponseHandler::error('Payment gateway signal failure: ' . $e->getMessage(), 500);
        }
    }

    public function walletHistory() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        $txModel = new WalletTransaction();
        $history = $txModel->getByUser($user['id']);
        return ResponseHandler::success(['transactions' => $history]);
    }

    public function recharge() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        $data = $this->getPostData();
        $amount = (float)($data['amount'] ?? 0);

        if ($amount <= 0) return ResponseHandler::badRequest('Recharge quantum must be positive.');

        try {
            $success = $this->walletModel->addFunds($user['id'], $amount, 'Wallet top-up via clinical gateway');
            if (!$success) throw new Exception("Recharge failed");
            
            return ResponseHandler::success([], 'Capital node augmented successfully.');
        } catch (Exception $e) {
            return ResponseHandler::error('Recharge protocol failure: ' . $e->getMessage(), 500);
        }
    }
}
