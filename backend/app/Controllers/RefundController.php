<?php
namespace App\Controllers;

/**
 * Refund Controller
 * Dedicated controller for handling financial disputes and wallet crediting.
 */
class RefundController extends BaseController {

    public function approve($refundId) {
        $refundModel = new \App\Models\Refund();
        $refund = $refundModel->findById($refundId);

        if (!$refund) {
            return $this->json(['success' => false, 'message' => 'Refund artifact not found.'], 404);
        }

        if ($refund['status'] === 'processed') {
            return $this->json(['success' => false, 'message' => 'Refund node already finalized.'], 400);
        }

        // Fetch user associated with the payment
        $db = \App\Core\Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT p.user_id FROM payments p WHERE p.id = ?");
        $stmt->execute([$refund['payment_id']]);
        $payment = $stmt->fetch();

        if (!$payment) {
            return $this->json(['success' => false, 'message' => 'Payment trace lost.'], 404);
        }

        // Credit user wallet
        $wallet = new \App\Models\Wallet();
        $success = $wallet->addFunds($payment['user_id'], $refund['amount'], "Refund for #{$refundId}");

        if ($success) {
            $refundModel->update($refundId, ['status' => 'processed']);
            return $this->json([
                'success' => true,
                'message' => "Refund protocol finalized. Wallet credited with ₹{$refund['amount']}."
            ]);
        }

        return $this->json(['success' => false, 'message' => 'Wallet synchronization failed.'], 500);
    }

    public function disputeList() {
        $refundModel = new \App\Models\Refund();
        $disputes = $refundModel->findAll(); // All refunds effectively act as disputes/requests here
        
        return $this->json([
            'disputes' => $disputes
        ]);
    }
}
