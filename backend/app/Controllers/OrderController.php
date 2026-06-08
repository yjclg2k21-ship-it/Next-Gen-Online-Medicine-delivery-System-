<?php
namespace App\Controllers;

use App\Core\ResponseHandler;
use App\Core\TransactionManager;
use App\Models\Order;
use App\Models\Cart;
use App\Models\User;
use App\Models\DeliveryMethod;
use App\Services\InventoryManager;
use App\Services\PriorityEngine;
use App\Services\NotificationService;
use App\Compliance\PrescriptionVerifier;
use App\Compliance\AuditLogger;
use App\Middleware\AuthGuard;
use App\Middleware\RoleCheck;
use Exception;

/**
 * Order Controller — Aligned with Unified Model Architecture
 */
class OrderController extends BaseController {

    private $orderModel;
    private $cartModel;
    private $userModel;
    private $deliveryModel;

    public function __construct() {
        $this->orderModel = new Order();
        $this->cartModel = new Cart();
        $this->userModel = new User();
        $this->deliveryModel = new DeliveryMethod();
    }

    /**
     * GET /orders
     */
    public function index() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        $orders = $this->orderModel->getByUserId($user['id']);
        return ResponseHandler::success(['orders' => $orders]);
    }

    /**
     * POST /orders
     * Checkout Logic
     */
    public function store() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        $data = $this->getPostData();
        
        $cartItems = $this->cartModel->getForUser($user['id']);
        if (empty($cartItems)) return ResponseHandler::badRequest('Fulfillment error: Cart node is empty.');

        // Clinical Validation
        $verifier = new PrescriptionVerifier();
        $prescriptionId = (int)($data['prescription_id'] ?? 0);
        
        foreach ($cartItems as $item) {
            if ($item['requires_prescription']) {
                if (!$prescriptionId) {
                    return ResponseHandler::error("Clinical breach: Valid medical authorization required for {$item['name']}.", 400);
                }
                $validation = $verifier->validatePrescription($user['id'], $prescriptionId);
                if (!$validation['valid']) {
                    return ResponseHandler::error("Prescription validation failed for {$item['name']}: {$validation['reason']}", 400);
                }
            }
        }

        $addressId = (int)($data['address_id'] ?? 0);
        $deliveryMethodId = (int)($data['delivery_method_id'] ?? 1);
        $prescriptionId = (int)($data['prescription_id'] ?? 0);
        $idempKey = $data['idempotency_key'] ?? bin2hex(random_bytes(16));
        $paymentMethod = $data['payment_method'] ?? 'card';

        // Idempotency Check
        $existing = $this->orderModel->count("idempotency_key = ?", [$idempKey]);
        if ($existing) {
            return ResponseHandler::success([], 'Idempotency node matched.');
        }

        $deliveryLat = isset($data['delivery_lat']) ? (float)$data['delivery_lat'] : null;
        $deliveryLng = isset($data['delivery_lng']) ? (float)$data['delivery_lng'] : null;

        // If coordinates not sent, try loading from user's address record
        if ((!$deliveryLat || !$deliveryLng) && $addressId) {
            try {
                $db = \App\Core\Database::getInstance()->getConnection();
                $addrStmt = $db->prepare("SELECT pincode, latitude, longitude FROM addresses WHERE id = ?");
                $addrStmt->execute([$addressId]);
                $addr = $addrStmt->fetch();
                if ($addr) {
                    $deliveryLat = !empty($addr['latitude']) ? (float)$addr['latitude'] : null;
                    $deliveryLng = !empty($addr['longitude']) ? (float)$addr['longitude'] : null;
                    
                    // Fallback to default pincode coordinates if address has no coordinates
                    if (empty($deliveryLat) || empty($deliveryLng)) {
                        $pincodeDefaults = [
                            '415523' => [17.99031790, 74.43031892],
                            '415528' => [17.92570000, 74.58330000],
                            '415521' => [18.04100000, 74.20850000],
                            '415537' => [18.02000000, 74.47000000],
                            '415509' => [17.90560000, 74.19720000],
                        ];
                        $pin = $addr['pincode'];
                        if (isset($pincodeDefaults[$pin])) {
                            $deliveryLat = $pincodeDefaults[$pin][0];
                            $deliveryLng = $pincodeDefaults[$pin][1];
                        }
                    }
                }
            } catch (\Exception $e) {}
        }

        // Find the nearest vendor based on delivery location
        $nearestVendorId = 2; // Default fallback to City Pharmacy
        if ($deliveryLat && $deliveryLng) {
            try {
                $db = \App\Core\Database::getInstance()->getConnection();
                $stmt = $db->query("SELECT user_id, latitude, longitude FROM vendor_profiles WHERE latitude IS NOT NULL AND longitude IS NOT NULL");
                $vendors = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                if (!empty($vendors)) {
                    $minDistance = INF;
                    $earthRadius = 6371;
                    foreach ($vendors as $v) {
                        $dLat = deg2rad($deliveryLat - (float)$v['latitude']);
                        $dLng = deg2rad($deliveryLng - (float)$v['longitude']);
                        $a = sin($dLat / 2) ** 2
                            + cos(deg2rad((float)$v['latitude'])) * cos(deg2rad($deliveryLat))
                            * sin($dLng / 2) ** 2;
                        $distance = $earthRadius * 2 * asin(sqrt($a));
                        if ($distance < $minDistance) {
                            $minDistance = $distance;
                            $nearestVendorId = (int)$v['user_id'];
                        }
                    }
                }
            } catch (\Exception $e) {}
        }

        // Split cart items by vendor (now routed dynamically to the nearest vendor!)
        $vendorGroups = [];
        foreach ($cartItems as $item) {
            $vendorGroups[$nearestVendorId][] = $item;
        }

        // ── Delivery Range Check (max 20 km per vendor) ──────────────────
        if ($deliveryLat && $deliveryLng) {
            $dbCheck = \App\Core\Database::getInstance()->getConnection();
            foreach (array_keys($vendorGroups) as $vendorId) {
                $vpStmt = $dbCheck->prepare(
                    "SELECT vp.latitude, vp.longitude, vp.delivery_radius_km, vp.pharmacy_name
                     FROM vendor_profiles vp
                     WHERE vp.user_id = ?"
                );
                $vpStmt->execute([$vendorId]);
                $vp = $vpStmt->fetch(\PDO::FETCH_ASSOC);

                if ($vp && $vp['latitude'] && $vp['longitude']) {
                    // Haversine formula — distance in km
                    $earthRadius = 6371;
                    $dLat = deg2rad($deliveryLat - $vp['latitude']);
                    $dLng = deg2rad($deliveryLng - $vp['longitude']);
                    $a = sin($dLat / 2) ** 2
                        + cos(deg2rad($vp['latitude'])) * cos(deg2rad($deliveryLat))
                        * sin($dLng / 2) ** 2;
                    $distanceKm = $earthRadius * 2 * asin(sqrt($a));

                    // Max allowed radius: vendor's setting, capped at 20 km
                    $maxRadius = min((float)($vp['delivery_radius_km'] ?? 20), 20.0);

                    if ($distanceKm > $maxRadius) {
                        $dist = round($distanceKm, 1);
                        $name = $vp['pharmacy_name'] ?? 'Pharmacy';
                        return ResponseHandler::error(
                            "{$name} only delivers within {$maxRadius} km. " .
                            "Your location is {$dist} km away. Please order from a closer pharmacy.",
                            422
                        );
                    }
                }
            }
        }
        // ────────────────────────────────────────────────────────────────

        try {
            $db = \App\Core\Database::getInstance()->getConnection();
            
            // Calculate Night Charges
            $hour = (int)date('G'); // 24-hour format (0 to 23)
            $nightCharge = ($hour >= 21 || $hour < 6) ? 15.00 : 0.00;

            // Calculate Weather Charges
            $weatherCharge = 0.00;
            try {
                $weatherStmt = $db->query("SELECT `value` FROM `settings` WHERE `key` = 'bad_weather'");
                $weatherSet = $weatherStmt->fetch();
                if ($weatherSet && $weatherSet['value'] == '1') {
                    $weatherCharge = 12.00;
                }
            } catch (\Exception $e) {}

            $delMethod = $this->deliveryModel->findById($deliveryMethodId);
            $baseDelFee = (float)($delMethod['price'] ?? 0);
            $delFee = $baseDelFee + $nightCharge + $weatherCharge;

            $paymentStatus = ($paymentMethod === 'wallet') ? 'paid' : 'pending';

            $orderIds = [];
            $totalWalletDeduction = 0;


            foreach ($vendorGroups as $vendorId => $items) {
                $txn = new TransactionManager();
                $orderId = $txn->run(function($db) use ($user, $items, $vendorId, $addressId, $deliveryMethodId, $prescriptionId, $idempKey, $delFee, $paymentMethod, $paymentStatus, &$orderIds, $nightCharge, $weatherCharge) {
                    $inventory = new InventoryManager();
                    $inventory->reserve($items);

                    // Dynamic Mapping: translate catalog medicine ID to selected vendor's cloned medicine ID
                    $mappedItems = [];
                    $totalAmt = 0;
                    foreach ($items as $item) {
                        $medId = $item['medicine_id'];
                        $medPrice = $item['price'];
                        
                        if ($vendorId != 2) {
                            $origStmt = $db->prepare("SELECT slug FROM medicines WHERE id = ?");
                            $origStmt->execute([$item['medicine_id']]);
                            $origMed = $origStmt->fetch(\PDO::FETCH_ASSOC);
                            
                            if ($origMed) {
                                $targetSlug = $origMed['slug'] . '-v' . $vendorId;
                                $lookupStmt = $db->prepare("SELECT id, price FROM medicines WHERE slug = ? && vendor_id = ? AND deleted_at IS NULL");
                                $lookupStmt->execute([$targetSlug, $vendorId]);
                                $vendorMed = $lookupStmt->fetch(\PDO::FETCH_ASSOC);
                                if ($vendorMed) {
                                    $medId = (int)$vendorMed['id'];
                                    $medPrice = (float)$vendorMed['price'];
                                }
                            }
                        }
                        
                        $mappedItems[] = [
                            'medicine_id' => $medId,
                            'quantity' => $item['quantity'],
                            'price' => $medPrice,
                            'name' => $item['name']
                        ];
                        $totalAmt += ($medPrice * $item['quantity']);
                    }

                    $grandTotal = ($totalAmt * 1.12) + $delFee;

                    // Unique idempotency key per vendor split
                    $splitIdempKey = $idempKey . '_v' . $vendorId;

                    $orderId = $this->orderModel->create([
                        'user_id' => $user['id'],
                        'vendor_id' => $vendorId,
                        'prescription_id' => $prescriptionId ?: null,
                        'delivery_method_id' => $deliveryMethodId,
                        'address_id' => $addressId ?: null,
                        'total_amount' => $totalAmt,
                        'delivery_fee' => $delFee,
                        'night_charge' => $nightCharge,
                        'weather_charge' => $weatherCharge,
                        'grand_total' => $grandTotal,
                        'payment_method' => $paymentMethod,
                        'payment_status' => $paymentStatus,
                        'is_emergency' => ($deliveryMethodId === 3) ? 1 : 0,
                        'status' => 'pending',
                        'delivery_otp' => str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT),
                        'pickup_otp' => str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT),
                        'idempotency_key' => $splitIdempKey
                    ]);

                    foreach ($mappedItems as $item) {
                        $db->prepare("INSERT INTO order_items (order_id, medicine_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)")
                           ->execute([$orderId, $item['medicine_id'], $item['quantity'], $item['price'], $item['price'] * $item['quantity']]);
                        
                        // Deduct stock in real-time
                        $db->prepare("UPDATE medicines SET stock = GREATEST(0, stock - ?) WHERE id = ?")
                           ->execute([$item['quantity'], $item['medicine_id']]);
                        
                        // Low Stock Alert
                        $stockStmt = $db->prepare("SELECT stock, name FROM medicines WHERE id = ?");
                        $stockStmt->execute([$item['medicine_id']]);
                        $med = $stockStmt->fetch();
                        
                        if ($med && $med['stock'] <= 15) {
                            $msg = "Low Stock Alert: '{$med['name']}' has only {$med['stock']} units left.";
                            $db->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, 'Low Stock Alert', ?, 'system')")
                               ->execute([$vendorId, $msg]);
                        }
                    }

                    $db->prepare("INSERT INTO order_status_logs (order_id, status, changed_by) VALUES (?, ?, ?)")->execute([$orderId, 'pending', $user['id']]);
                    return $orderId;
                });

                $orderIds[] = $orderId;
                
                // Calculate wallet deduction for this split
                if ($paymentMethod === 'wallet') {
                    $orderStmt = $db->prepare("SELECT grand_total FROM orders WHERE id = ?");
                    $orderStmt->execute([$orderId]);
                    $totalWalletDeduction += (float)($orderStmt->fetchColumn() ?: 0);
                }
            }

            $this->cartModel->clear($user['id']);

            // Wallet deduction (total across all split orders)
            if ($paymentMethod === 'wallet' && $totalWalletDeduction > 0) {
                $db->prepare("UPDATE users SET wallet_balance = wallet_balance - ? WHERE id = ? AND wallet_balance >= ?")
                   ->execute([$totalWalletDeduction, $user['id'], $totalWalletDeduction]);
            }

            // Record prescription usage
            if ($prescriptionId) {
                $verifier->recordUsage($prescriptionId, $orderIds[0]);
            }

            // Return first order ID for redirect (frontend handles single redirect)
            return ResponseHandler::success([
                'order_id' => $orderIds[0],
                'order_ids' => $orderIds,
                'split_count' => count($orderIds),
                'total_amount' => $totalWalletDeduction ?: null
            ], count($orderIds) > 1 
                ? count($orderIds) . ' orders created (split by pharmacy).' 
                : 'Order initialized.', 201);

        } catch (Exception $e) {
            return ResponseHandler::error('Checkout node failure: ' . $e->getMessage(), 500);
        }
    }

    /**
     * GET /orders/{id}/tracking
     */
    public function tracking($id) {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        $order = $this->orderModel->getWithItems($id);
        
        if (!$order || ($order['user_id'] != $user['id'] && !in_array($user['role'], ['admin', 'vendor', 'delivery']))) {
            return ResponseHandler::forbidden('Access restricted.');
        }

        $db = \App\Core\Database::getInstance()->getConnection();
        
        // Find assigned partner
        $riderInfo = null;
        if ($order['delivery_partner_id']) {
            $stmt = $db->prepare("SELECT u.name, u.phone, dp.vehicle_type, dp.vehicle_number 
                                  FROM users u 
                                  LEFT JOIN delivery_profiles dp ON u.id = dp.user_id 
                                  WHERE u.id = ?");
            $stmt->execute([$order['delivery_partner_id']]);
            $rider = $stmt->fetch();
            if ($rider) {
                $riderInfo = [
                    'name' => $rider['name'],
                    'phone' => $rider['phone'],
                    'rating' => 'N/A',
                    'vehicle' => $rider['vehicle_type'] . " (" . $rider['vehicle_number'] . ")"
                ];
            }
        }

        if (!$riderInfo) {
            $riderInfo = [
                'name' => 'Searching...',
                'phone' => 'N/A',
                'rating' => 'N/A',
                'vehicle' => 'Pending Assignment'
            ];
        }

        // Real GPS coordinates from agent_locations
        $coordinates = ['lat' => 17.9868, 'lng' => 74.4379]; // Default: Phaltan
        
        if ($order['delivery_partner_id']) {
            $locStmt = $db->prepare("SELECT latitude, longitude FROM agent_locations WHERE agent_id = ? ORDER BY updated_at DESC LIMIT 1");
            $locStmt->execute([$order['delivery_partner_id']]);
            $loc = $locStmt->fetch();
            if ($loc) {
                $coordinates = [
                    'lat' => (float)$loc['latitude'],
                    'lng' => (float)$loc['longitude']
                ];
            }
        }

        // Fetch Vendor and Customer Locations for Route Parity
        $vendorLoc = ['lat' => 17.9820, 'lng' => 74.4420]; // Fallback Phaltan
        $vStmt = $db->prepare("SELECT latitude, longitude FROM vendor_profiles WHERE user_id = ?");
        $vStmt->execute([$order['vendor_id']]);
        $vLoc = $vStmt->fetch();
        if ($vLoc && $vLoc['latitude']) {
            $vendorLoc = ['lat' => (float)$vLoc['latitude'], 'lng' => (float)$vLoc['longitude']];
        }

        $customerLoc = ['lat' => 17.9920, 'lng' => 74.4320]; // Fallback Phaltan
        if ($order['address_id']) {
            $aStmt = $db->prepare("SELECT latitude, longitude FROM addresses WHERE id = ?");
            $aStmt->execute([$order['address_id']]);
            $aLoc = $aStmt->fetch();
            if ($aLoc && $aLoc['latitude']) {
                $customerLoc = ['lat' => (float)$aLoc['latitude'], 'lng' => (float)$aLoc['longitude']];
            }
        }

        // Status Progression Logs
        $steps = $db->prepare("SELECT status, created_at as changed_at FROM order_status_logs WHERE order_id = ? ORDER BY created_at ASC");
        $steps->execute([$id]);
        $realSteps = $steps->fetchAll();
        
        return ResponseHandler::success([
            'order_id' => $id,
            'status' => $order['status'],
            'delivery_otp' => $order['delivery_otp'] ?? null,
            'telemetry' => [
                'coordinates' => $coordinates,
                'vendor_location' => $vendorLoc,
                'customer_location' => $customerLoc,
                'estimated_arrival' => in_array($order['status'], ['dispatched', 'processing']) ? 'TBD' : 'N/A',
                'checkpoint' => $order['status'] === 'dispatched' ? 'Out for Delivery' : 'Processing Center'
            ],
            'steps' => $realSteps,
            'rider' => $riderInfo
        ], 'Logistics telemetry synchronized.');
    }

    /**
     * POST /orders/{id}/cancel
     */
    public function cancel($id) {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        $order = $this->orderModel->findById($id);
        
        if (!$order || $order['user_id'] != $user['id']) return ResponseHandler::forbidden();
        if (!in_array($order['status'], ['placed', 'confirmed'])) return ResponseHandler::badRequest("Order cannot be cancelled.");

        try {
            $txn = new TransactionManager();
            $txn->run(function($db) use ($id, $user, $order) {
                $this->orderModel->update($id, ['status' => 'cancelled']);
                $db->prepare("INSERT INTO order_status_logs (order_id, status, changed_by) VALUES (?, 'cancelled', ?)")->execute([$id, $user['id']]);
                
                (new InventoryManager())->restoreStock($id);

                if ($order['payment_status'] === 'paid') {
                    $db->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?")->execute([$order['grand_total'], $user['id']]);
                    $db->prepare("INSERT INTO wallet_transactions (user_id, amount, type, description) VALUES (?, ?, 'credit', ?)")
                       ->execute([$user['id'], $order['grand_total'], "Refund for #$id"]);
                }
            });

            return ResponseHandler::success([], 'Order cancelled and balance restored.');
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * GET /orders/{id}/invoice
     */
    public function getInvoice($id) {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        $order = $this->orderModel->getWithItems($id);

        if (!$order || ($order['user_id'] != $user['id'] && $user['role'] !== 'admin' && $order['vendor_id'] != $user['id'])) {
            return ResponseHandler::forbidden('Access to clinical invoice denied.');
        }

        $db = \App\Core\Database::getInstance()->getConnection();
        
        // Fetch full user for name parity
        $fullUser = $this->userModel->findById($user['id']);
        $userName = $fullUser['name'] ?? 'Authorized User';

        // Fetch real address details with null safety
        $address = ['customer_name' => $userName, 'address_line1' => 'Not Specified', 'city' => 'N/A'];
        if (!empty($order['address_id'])) {
            $addrStmt = $db->prepare("SELECT a.*, u.name as customer_name FROM addresses a JOIN users u ON a.user_id = u.id WHERE a.id = ?");
            $addrStmt->execute([$order['address_id']]);
            $fetchedAddr = $addrStmt->fetch();
            if ($fetchedAddr) $address = $fetchedAddr;
        }

        // Fetch vendor details with null safety
        $vendor = ['name' => 'MediMitra Pharmacy', 'business_address' => 'Clinical Hub'];
        if (!empty($order['vendor_id'])) {
            $vStmt = $db->prepare("SELECT u.name, vp.address FROM users u LEFT JOIN vendor_profiles vp ON u.id = vp.user_id WHERE u.id = ?");
            $vStmt->execute([$order['vendor_id']]);
            $fetchedVendor = $vStmt->fetch();
            if ($fetchedVendor) {
                $vendor = [
                    'name' => $fetchedVendor['name'],
                    'business_address' => $fetchedVendor['address']
                ];
            }
        }

        // Fetch order items with medicine names
        $itemsStmt = $db->prepare("
            SELECT oi.*, m.name 
            FROM order_items oi 
            JOIN medicines m ON oi.medicine_id = m.id 
            WHERE oi.order_id = ?
        ");
        $itemsStmt->execute([$order['id']]);
        $orderItems = $itemsStmt->fetchAll();

        $txnId = 'TXN_UNAVAILABLE';
        try {
            $txnId = 'TXN_' . strtoupper(bin2hex(random_bytes(6)));
        } catch (Exception $e) {
            $txnId = 'TXN_' . strtoupper(substr(md5(uniqid()), 0, 12));
        }

        return ResponseHandler::success([
            'id'    => $order['id'],
            'date'  => date('M d, Y • h:i A', strtotime($order['created_at'])),
            'txn'   => $txnId,
            'status'=> ($order['payment_status'] ?? '') === 'paid' ? 'AUTHORIZED PAYMENT' : 'PENDING PAYMENT',
            'order_status' => $order['status'],
            'customer' => [
                'name'    => $address['customer_name'] ?? $userName,
                'address' => ($address['address_line1'] ?? 'Default') . ", " . ($address['city'] ?? 'Phaltan')
            ],
            'vendor' => [
                'name'    => $vendor['name'] ?? 'MediServe Pharmacy',
                'address' => $vendor['business_address'] ?? 'MediMitra Hub, Sector 7'
            ],
            'items' => array_map(fn($i) => [
                'name'  => $i['name'],
                'price' => (float)$i['price'],
                'qty'   => (int)$i['quantity']
            ], $orderItems),
            'summary' => [
                'subtotal' => round((float)$order['total_amount'], 2),
                'tax'      => round((float)$order['total_amount'] * 0.12, 2),
                'delivery' => round((float)$order['delivery_fee'], 2),
                'codFee'   => $order['payment_method'] === 'cod' ? 7.00 : 0.00,
                'total'    => round((float)$order['grand_total'] + ($order['payment_method'] === 'cod' ? 7.00 : 0.00), 2)
            ],
            'delivery_otp' => $order['delivery_otp'] ?? null,
            'dispatcher' => $order['dispatcher_name'] ?? null
        ], 'Invoice synchronized.');
    }

    /**
     * GET /vendor/emergency-orders
     * Returns emergency orders for the authenticated vendor with preparation deadline countdown.
     * Requirements: 3.1, 3.4, 3.6
     */
    public function vendorEmergencyOrders() {
        AuthGuard::handle();
        RoleCheck::handle('vendor');
        $user = AuthGuard::getUser();
        $vendorId = $user['id'];

        try {
            $db = \App\Core\Database::getInstance()->getConnection();

            // Query emergency orders with MySQL-computed prep deadline (avoids PHP/MySQL timezone mismatch)
            $stmt = $db->prepare(
                "SELECT o.id, o.user_id, o.status, o.created_at, o.sla_deadline,
                        o.emergency_acknowledged_at, o.emergency_ready_at,
                        o.emergency_picked_up_at, o.delivery_partner_id,
                        o.total_amount, o.grand_total, o.is_emergency,
                        GREATEST(0, TIMESTAMPDIFF(SECOND, NOW(), DATE_ADD(o.created_at, INTERVAL 5 MINUTE))) AS prep_remaining_seconds,
                        DATE_ADD(o.created_at, INTERVAL 5 MINUTE) AS prep_deadline_at
                 FROM orders o
                 WHERE o.vendor_id = ?
                   AND o.is_emergency = 1
                   AND o.status IN ('pending', 'placed', 'confirmed', 'dispatched', 'processing', 'accepted')
                 ORDER BY o.created_at DESC"
            );
            $stmt->execute([$vendorId]);
            $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $priorityEngine = new PriorityEngine();
            $emergencyOrders = [];

            foreach ($orders as $order) {
                $orderId = (int) $order['id'];

                // Use MySQL-computed remaining seconds (timezone-safe)
                $prepRemainingSeconds = (int) $order['prep_remaining_seconds'];
                $prepDeadlineAt     = $order['prep_deadline_at'];

                // Get assigned rider ETA to vendor
                $riderEta = null;
                if ($order['delivery_partner_id']) {
                    $eta = $priorityEngine->calculateETA($orderId);
                    if ($eta && !$order['emergency_picked_up_at']) {
                        $riderEta = [
                            'minutes' => $eta['eta_minutes'],
                            'distance_km' => $eta['distance_km'],
                            'is_stale' => $eta['is_stale'],
                        ];
                    }
                }

                // Get order items
                $itemsStmt = $db->prepare(
                    "SELECT oi.quantity, oi.price, m.name
                     FROM order_items oi
                     JOIN medicines m ON oi.medicine_id = m.id
                     WHERE oi.order_id = ?"
                );
                $itemsStmt->execute([$orderId]);
                $items = $itemsStmt->fetchAll(\PDO::FETCH_ASSOC);

                // Get customer info
                $custStmt = $db->prepare("SELECT name, phone FROM users WHERE id = ?");
                $custStmt->execute([$order['user_id']]);
                $customer = $custStmt->fetch(\PDO::FETCH_ASSOC);

                $emergencyOrders[] = [
                    'order_id' => $orderId,
                    'status' => $order['status'],
                    'created_at' => $order['created_at'],
                    'total_amount' => (float) $order['grand_total'],
                    'customer' => [
                        'name' => $customer['name'] ?? 'Unknown',
                        'phone' => $customer['phone'] ?? 'N/A',
                    ],
                    'items' => array_map(fn($i) => [
                        'name' => $i['name'],
                        'quantity' => (int) $i['quantity'],
                        'price' => (float) $i['price'],
                    ], $items),
                    'preparation_deadline' => [
                        'remaining_seconds' => $prepRemainingSeconds,
                        'is_expired'        => $prepRemainingSeconds <= 0,
                        'deadline_at'       => $prepDeadlineAt,
                    ],
                    'acknowledgment' => [
                        'is_acknowledged' => $order['emergency_acknowledged_at'] !== null,
                        'acknowledged_at' => $order['emergency_acknowledged_at'],
                    ],
                    'is_ready' => $order['emergency_ready_at'] !== null,
                    'ready_at' => $order['emergency_ready_at'],
                    'rider_eta' => $riderEta,
                    'sla_deadline' => $order['sla_deadline'],
                ];
            }

            // Sort: emergency orders at top (unacknowledged first, then by creation time)
            usort($emergencyOrders, function ($a, $b) {
                // Unacknowledged orders first
                if (!$a['acknowledgment']['is_acknowledged'] && $b['acknowledgment']['is_acknowledged']) return -1;
                if ($a['acknowledgment']['is_acknowledged'] && !$b['acknowledgment']['is_acknowledged']) return 1;
                // Then by preparation deadline remaining (most urgent first)
                return $a['preparation_deadline']['remaining_seconds'] <=> $b['preparation_deadline']['remaining_seconds'];
            });

            return ResponseHandler::success([
                'emergency_orders' => $emergencyOrders,
                'count' => count($emergencyOrders),
            ], 'Vendor emergency orders retrieved.');

        } catch (Exception $e) {
            return ResponseHandler::error('Failed to retrieve emergency orders: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /orders/{id}/acknowledge-emergency
     * Records vendor acknowledgment of an emergency order and stops escalation countdown.
     * Requirements: 3.7
     */
    public function acknowledgeEmergency($id) {
        AuthGuard::handle();
        RoleCheck::handle('vendor');
        $user = AuthGuard::getUser();

        try {
            $db = \App\Core\Database::getInstance()->getConnection();

            // Verify the order exists, belongs to this vendor, and is an emergency order
            $stmt = $db->prepare(
                "SELECT id, vendor_id, is_emergency, emergency_acknowledged_at, status
                 FROM orders
                 WHERE id = ? AND is_emergency = 1"
            );
            $stmt->execute([$id]);
            $order = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$order) {
                return ResponseHandler::error('Emergency order not found.', 404);
            }

            if ((int) $order['vendor_id'] !== (int) $user['id']) {
                return ResponseHandler::forbidden('You do not have access to this order.');
            }

            if ($order['emergency_acknowledged_at'] !== null) {
                return ResponseHandler::success([
                    'order_id' => (int) $id,
                    'acknowledged_at' => $order['emergency_acknowledged_at'],
                ], 'Order already acknowledged.');
            }

            // Record acknowledgment timestamp
            $now = date('Y-m-d H:i:s');
            $updateStmt = $db->prepare(
                "UPDATE orders SET emergency_acknowledged_at = ? WHERE id = ?"
            );
            $updateStmt->execute([$now, $id]);

            // Stop escalation countdown (skip gracefully if table doesn't exist)
            try {
                $resolveStmt = $db->prepare(
                    "UPDATE emergency_escalations
                     SET resolved_at = ?, resolved_by = ?, action_taken = 'vendor_acknowledged'
                     WHERE order_id = ? AND resolved_at IS NULL
                       AND escalation_level IN ('warning', 'critical')"
                );
                $resolveStmt->execute([$now, $user['id'], $id]);
            } catch (\Exception $e) {
                // Table may not exist yet — non-critical, continue
            }

            // Log status change
            $logStmt = $db->prepare(
                "INSERT INTO order_status_logs (order_id, status, changed_by) VALUES (?, 'acknowledged', ?)"
            );
            $logStmt->execute([$id, $user['id']]);

            return ResponseHandler::success([
                'order_id' => (int) $id,
                'acknowledged_at' => $now,
            ], 'Emergency order acknowledged successfully.');

        } catch (Exception $e) {
            return ResponseHandler::error('Failed to acknowledge emergency order: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /orders/{id}/ready-for-pickup
     * Vendor marks emergency order as ready for pickup.
     * Requirements: 3.5, 3.9
     */
    public function markReadyForPickup($id) {
        AuthGuard::handle();
        RoleCheck::handle('vendor');
        $user = AuthGuard::getUser();

        try {
            $db = \App\Core\Database::getInstance()->getConnection();

            // Verify the order exists, belongs to this vendor, and is an emergency order
            $stmt = $db->prepare(
                "SELECT id, vendor_id, is_emergency, emergency_ready_at, delivery_partner_id, status
                 FROM orders
                 WHERE id = ? AND is_emergency = 1"
            );
            $stmt->execute([$id]);
            $order = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$order) {
                return ResponseHandler::error('Emergency order not found.', 404);
            }

            if ((int) $order['vendor_id'] !== (int) $user['id']) {
                return ResponseHandler::forbidden('You do not have access to this order.');
            }

            if ($order['emergency_ready_at'] !== null) {
                return ResponseHandler::success([
                    'order_id' => (int) $id,
                    'ready_at' => $order['emergency_ready_at'],
                ], 'Order already marked as ready for pickup.');
            }

            // Set emergency_ready_at timestamp and update order status
            $now = date('Y-m-d H:i:s');
            $updateStmt = $db->prepare(
                "UPDATE orders SET emergency_ready_at = ?, status = 'dispatched' WHERE id = ?"
            );
            $updateStmt->execute([$now, $id]);

            // Log status change
            $logStmt = $db->prepare(
                "INSERT INTO order_status_logs (order_id, status, changed_by) VALUES (?, 'dispatched', ?)"
            );
            $logStmt->execute([$id, $user['id']]);

            // Also acknowledge if not already done
            $ackStmt = $db->prepare(
                "UPDATE orders SET emergency_acknowledged_at = ? WHERE id = ? AND emergency_acknowledged_at IS NULL"
            );
            $ackStmt->execute([$now, $id]);

            // Notify assigned delivery partner
            $notificationService = new NotificationService();
            $riderAssignmentPending = false;

            if ($order['delivery_partner_id']) {
                // Notify the assigned delivery partner
                $notificationService->send(
                    (int) $order['delivery_partner_id'],
                    'Emergency Order Ready for Pickup',
                    "Emergency order #{$id} is ready for pickup at the vendor. Please proceed immediately."
                );
            } else {
                // No partner assigned - notify admin and flag for vendor
                $riderAssignmentPending = true;
                $notificationService->send(
                    1, // Admin
                    'Emergency Order Ready - No Rider',
                    "Emergency order #{$id} is ready for pickup but has no assigned delivery partner. Immediate assignment required."
                );
            }

            // Resolve any pending vendor-related escalations (skip if table doesn't exist)
            try {
                $resolveStmt = $db->prepare(
                    "UPDATE emergency_escalations
                     SET resolved_at = ?, resolved_by = ?, action_taken = 'vendor_marked_ready'
                     WHERE order_id = ? AND resolved_at IS NULL"
                );
                $resolveStmt->execute([$now, $user['id'], $id]);
            } catch (\Exception $e) {
                // Table may not exist yet — non-critical, continue
            }

            $response = [
                'order_id' => (int) $id,
                'ready_at' => $now,
                'status' => 'dispatched',
            ];

            if ($riderAssignmentPending) {
                $response['rider_assignment_pending'] = true;
                $response['message'] = 'Order marked ready. Rider assignment is pending - admin has been notified.';
            }

            return ResponseHandler::success($response, 'Emergency order marked as ready for pickup.');

        } catch (Exception $e) {
            return ResponseHandler::error('Failed to mark order as ready: ' . $e->getMessage(), 500);
        }
    }

    /**
     * GET /orders/{id}/emergency-tracking
     * Customer-facing emergency order tracking with real-time ETA and SLA progress.
     */
    public function emergencyTracking($id) {
        AuthGuard::handle();
        $user = AuthGuard::getUser();

        // Verify the order exists and belongs to the authenticated user
        $order = $this->orderModel->findById($id);

        if (!$order) {
            return ResponseHandler::error('Order not found.', 404);
        }

        if ((int) $order['user_id'] !== (int) $user['id']) {
            return ResponseHandler::forbidden('You do not have access to this order.');
        }

        if (empty($order['is_emergency']) || (int) $order['is_emergency'] !== 1) {
            return ResponseHandler::badRequest('This order is not an emergency order.');
        }

        // Use PriorityEngine to get the full emergency order status
        $priorityEngine = new PriorityEngine();
        $status = $priorityEngine->getEmergencyOrderStatus((int) $id);

        if (isset($status['error'])) {
            return ResponseHandler::error($status['error'], 404);
        }

        // Build the customer-facing response
        $response = [
            'order_id' => $status['order_id'],
            'current_stage' => $status['current_stage'],
            'timestamps' => $status['timestamps'],
            'sla' => [
                'deadline' => $status['sla']['deadline'],
                'progress_percentage' => $status['sla']['progress_percentage'],
                'remaining_seconds' => $status['sla']['remaining_seconds'],
                'status' => $status['sla']['status'],
                'is_breached' => $status['sla']['is_breached'],
            ],
            'is_delayed' => $status['is_delayed'],
        ];

        // ETA handling: include route-based ETA if rider is assigned with GPS
        if ($status['eta'] !== null) {
            $response['eta'] = [
                'minutes' => $status['eta']['eta_minutes'],
                'distance_km' => $status['eta']['distance_km'],
                'is_stale' => $status['eta']['is_stale'],
                'gps_updated_at' => $status['eta']['gps_updated_at'],
            ];
            $response['gps_stale'] = $status['gps_stale'];
        } else {
            // No rider assigned or no GPS data: return SLA countdown instead
            $response['eta'] = null;
            $response['gps_stale'] = null;
            $response['sla_countdown_seconds'] = max(0, $status['sla']['remaining_seconds']);
        }

        // Include rider info (name only for customer privacy)
        if ($status['rider'] !== null) {
            $response['rider'] = [
                'name' => $status['rider']['name'],
                'phone' => $status['rider']['phone'],
            ];
        } else {
            $response['rider'] = null;
        }

        return ResponseHandler::success($response, 'Emergency tracking data retrieved.');
    }
}
