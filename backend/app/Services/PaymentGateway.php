<?php
namespace App\Services;

/**
 * Payment Gateway Service
 * Simulates integration with a third-party gateway (e.g., Razorpay, Stripe)
 */
class PaymentGateway {

    public function createOrder($amount, $currency = 'INR') {
        $key = $_ENV['RAZORPAY_KEY'] ?? getenv('RAZORPAY_KEY') ?? 'rzp_test_placeholder';
        $isSandbox = strpos($key, 'test') !== false || $key === 'rzp_test_placeholder';

        return [
            'order_id' => 'order_' . strtoupper(bin2hex(random_bytes(6))),
            'amount' => $amount,
            'currency' => $currency,
            'status' => 'created',
            'mode' => $isSandbox ? 'sandbox' : 'live',
            'key' => $key
        ];
    }

    public function verifySignature($paymentId, $orderId, $signature) {
        $secret = $_ENV['RAZORPAY_SECRET'] ?? getenv('RAZORPAY_SECRET') ?? '';
        if (empty($secret)) return false;
        
        $expectedSignature = hash_hmac('sha256', $orderId . '|' . $paymentId, $secret);
        return hash_equals($expectedSignature, $signature);
    }

    public function processRefund($paymentId, $amount) {
        return [
            'refund_id' => 'REF_' . uniqid(),
            'status' => 'processed',
            'amount' => $amount
        ];
    }
}
