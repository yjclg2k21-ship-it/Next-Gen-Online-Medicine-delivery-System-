<?php
$_SERVER['REQUEST_URI'] = '/api/orders/5/tracking';
$_SERVER['REQUEST_METHOD'] = 'GET';
$headers = ['Authorization: Bearer ' . 'test']; // Need a valid token or just mock the AuthGuard

require_once __DIR__ . '/backend/app/bootstrap.php';
require_once __DIR__ . '/backend/app/Core/Database.php';

// Quick manual check of getWithItems(5)
$orderModel = new \App\Models\Order();
$order = $orderModel->getWithItems(5);
if (!$order) {
    echo "Order 5 not found or getWithItems returned null.\n";
} else {
    echo "Order 5 found. User ID: " . $order['user_id'] . "\n";
}
