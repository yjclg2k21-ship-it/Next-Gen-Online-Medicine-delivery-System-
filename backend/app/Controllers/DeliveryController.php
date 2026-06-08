<?php
namespace App\Controllers;

use App\Core\Database;
use App\Core\ResponseHandler;
use App\Middleware\AuthGuard;
use App\Middleware\RoleCheck;
use Exception;

/**
 * Delivery Controller — Logistics, Partner History & Order Management
 * All routes require a valid 'delivery' role token.
 */
class DeliveryController extends BaseController {

    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * GET /delivery/history
     */
    public function history() {
        AuthGuard::handle();
        RoleCheck::handle('delivery');
        $user = AuthGuard::getUser();

        try {
            $stmt = $this->db->prepare("SELECT o.id, u.name as customer, o.grand_total as amount, o.delivery_fee as earnings, o.created_at as date, o.status 
                                        FROM orders o 
                                        JOIN users u ON o.user_id = u.id 
                                        WHERE o.delivery_partner_id = ? AND o.status IN ('delivered', 'returned') 
                                        ORDER BY o.created_at DESC");
            $stmt->execute([$user['id']]);
            $history = $stmt->fetchAll();

            $statsStmt = $this->db->prepare("SELECT 
                                                COUNT(*) as total_deliveries, 
                                                AVG(delivery_fee) as avg_earnings 
                                              FROM orders WHERE delivery_partner_id = ? AND status = 'delivered'");
            $statsStmt->execute([$user['id']]);
            $stats = $statsStmt->fetch();

            $ratingStmt = $this->db->prepare("SELECT AVG(rating) as avg_rating FROM delivery_reviews WHERE rider_id = ?");
            $ratingStmt->execute([$user['id']]);
            $rating = $ratingStmt->fetch();

            return ResponseHandler::success([
                'history' => $history, 
                'stats' => [
                    'total_deliveries' => (int)($stats['total_deliveries'] ?? 0),
                    'avg_earnings' => round((float)($stats['avg_earnings'] ?? 0), 2),
                    'rating' => round((float)($rating['avg_rating'] ?? 4.9), 1)
                ]
            ], 'Delivery fulfillment history retrieved.');
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * GET /delivery/dispatch/queue
     */
    public function dispatchQueue() {
        AuthGuard::handle();
        RoleCheck::handle('delivery');
        
        try {
            // Get Agent's current location for distance calculation
            $user = AuthGuard::getUser();
            $locStmt = $this->db->prepare("SELECT latitude, longitude FROM agent_locations WHERE agent_id = ?");
            $locStmt->execute([$user['id']]);
            $agentLoc = $locStmt->fetch();

            $stmt = $this->db->prepare("SELECT o.id, u.name as customer, a.address_line1 as address, o.is_emergency, o.grand_total as amount, o.status, a.latitude, a.longitude 
                                        FROM orders o 
                                        JOIN users u ON o.user_id = u.id 
                                        LEFT JOIN addresses a ON o.address_id = a.id 
                                        WHERE o.status IN ('confirmed', 'dispatched') AND o.delivery_partner_id IS NULL 
                                        ORDER BY o.is_emergency DESC, o.created_at ASC");
            $stmt->execute();
            $queue = $stmt->fetchAll();

            // Formatting for frontend expectations
            foreach ($queue as &$item) {
                $item['priority'] = $item['is_emergency'] ? 'Emergency' : 'Standard';
                $item['type'] = $item['is_emergency'] ? 'emergency' : 'standard';
                $item['eta'] = $item['is_emergency'] ? '15-30 mins' : '2-4 hrs';
                
                if ($agentLoc && !empty($item['latitude'])) {
                    $dist = $this->calculateDistance($agentLoc['latitude'], $agentLoc['longitude'], $item['latitude'], $item['longitude']);
                    $item['distance'] = round($dist, 1) . ' km';
                    $item['distance_val'] = $dist;
                } else {
                    $item['distance'] = 'Nearby'; 
                    $item['distance_val'] = 99999;
                }
            }

            // Route optimization: Sort batched orders by distance (Emergency always first)
            usort($queue, function($a, $b) {
                if ($a['is_emergency'] != $b['is_emergency']) {
                    return $b['is_emergency'] <=> $a['is_emergency'];
                }
                return $a['distance_val'] <=> $b['distance_val'];
            });

            // Fetch stats
            $user = AuthGuard::getUser();
            $pendingStmt = $this->db->query("SELECT COUNT(*) as cnt FROM orders WHERE status IN ('confirmed', 'dispatched') AND delivery_partner_id IS NULL AND is_emergency = 0");
            $activeStmt = $this->db->query("SELECT COUNT(*) as cnt FROM orders WHERE status IN ('confirmed', 'dispatched') AND delivery_partner_id IS NULL AND is_emergency = 1");
            $doneStmt = $this->db->prepare("SELECT COUNT(*) as cnt FROM orders WHERE delivery_partner_id = ? AND status = 'delivered' AND DATE(created_at) = CURDATE()");
            $doneStmt->execute([$user['id']]);

            $stats = [
                'pending' => (int)$pendingStmt->fetch()['cnt'],
                'active' => (int)$activeStmt->fetch()['cnt'],
                'completed_today' => (int)$doneStmt->fetch()['cnt']
            ];

            return ResponseHandler::success(['queue' => $queue, 'stats' => $stats], 'Dispatch priority queue retrieved.');
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * GET /delivery/orders
     */
    public function orders() {
        AuthGuard::handle();
        RoleCheck::handle('delivery');
        $user = AuthGuard::getUser();

        try {
            // Get Agent's current location
            $locStmt = $this->db->prepare("SELECT latitude, longitude FROM agent_locations WHERE agent_id = ?");
            $locStmt->execute([$user['id']]);
            $agentLoc = $locStmt->fetch();

            $stmt = $this->db->prepare("SELECT o.id, u.name as customer, a.address_line1 as address, o.is_emergency as type, o.grand_total as amount, o.status, o.assigned_at, a.latitude, a.longitude 
                                        FROM orders o 
                                        JOIN users u ON o.user_id = u.id 
                                        LEFT JOIN addresses a ON o.address_id = a.id 
                                        WHERE o.delivery_partner_id = ? AND o.status NOT IN ('returned', 'cancelled')");
            $stmt->execute([$user['id']]);
            $orders = $stmt->fetchAll();

            $activeOrders = array_filter($orders, fn($o) => $o['status'] !== 'delivered');
            $deliveredOrders = array_filter($orders, fn($o) => $o['status'] === 'delivered');

            // Phase 5: Route Optimization (Nearest Neighbor Shortest Path) on active orders only
            if (count($activeOrders) > 1 && $agentLoc) {
                $optimizedRoute = [];
                $currentLat = (float)$agentLoc['latitude'];
                $currentLng = (float)$agentLoc['longitude'];
                
                $remainingOrders = array_values($activeOrders);
                while (!empty($remainingOrders)) {
                    $nearestIndex = -1;
                    $minDistance = INF;
                    
                    foreach ($remainingOrders as $index => $order) {
                        // Emergency orders bypass distance logic and are always prioritized first
                        if (!empty($order['type']) && $order['type'] == 1 && empty($optimizedRoute)) {
                            $nearestIndex = $index;
                            break;
                        }
                        
                        $dist = 99999;
                        if (!empty($order['latitude'])) {
                            $dist = $this->calculateDistance($currentLat, $currentLng, $order['latitude'], $order['longitude']);
                        }
                        
                        if ($dist < $minDistance) {
                            $minDistance = $dist;
                            $nearestIndex = $index;
                        }
                    }
                    
                    $nextOrder = $remainingOrders[$nearestIndex];
                    $optimizedRoute[] = $nextOrder;
                    
                    // Update current location to this drop-off point for the next calculation
                    if (!empty($nextOrder['latitude'])) {
                        $currentLat = (float)$nextOrder['latitude'];
                        $currentLng = (float)$nextOrder['longitude'];
                    }
                    
                    unset($remainingOrders[$nearestIndex]);
                    $remainingOrders = array_values($remainingOrders);
                }
                
                $orders = array_merge($optimizedRoute, array_values($deliveredOrders));
            } else {
                $orders = array_merge(array_values($activeOrders), array_values($deliveredOrders));
            }

            return ResponseHandler::success(['orders' => $orders], 'Delivery orders optimized and retrieved.');
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * GET /delivery/orders/{id}
     */
    public function orderDetail($id) {
        AuthGuard::handle();
        RoleCheck::handle('delivery');
        
        try {
            $stmt = $this->db->prepare("SELECT o.*, u.name as customer, u.phone, a.address_line1 as address, COALESCE(v.pharmacy_name, 'Unknown Pharmacy') as vendor, v.address as vendor_address 
                                        FROM orders o 
                                        JOIN users u ON o.user_id = u.id 
                                        LEFT JOIN addresses a ON o.address_id = a.id 
                                        LEFT JOIN vendor_profiles v ON o.vendor_id = v.user_id 
                                        WHERE o.id = ?");
            $stmt->execute([$id]);
            $order = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$order) return ResponseHandler::notFound();

            // Auto-generate OTPs if they are missing
            $updated = false;
            $params = [];
            $sql = "UPDATE orders SET ";
            if (empty($order['pickup_otp'])) {
                $order['pickup_otp'] = sprintf("%06d", rand(100000, 999999));
                $sql .= "pickup_otp = ?, ";
                $params[] = $order['pickup_otp'];
                $updated = true;
            }
            if (empty($order['delivery_otp'])) {
                $order['delivery_otp'] = sprintf("%06d", rand(100000, 999999));
                $sql .= "delivery_otp = ?, ";
                $params[] = $order['delivery_otp'];
                $updated = true;
            }
            if ($updated) {
                $sql = rtrim($sql, ", ") . " WHERE id = ?";
                $params[] = $id;
                $this->db->prepare($sql)->execute($params);
            }

            // Fetch items
            $itemStmt = $this->db->prepare("SELECT oi.*, m.name FROM order_items oi JOIN medicines m ON oi.medicine_id = m.id WHERE oi.order_id = ?");
            $itemStmt->execute([$id]);
            $order['items'] = $itemStmt->fetchAll(\PDO::FETCH_ASSOC);

            return ResponseHandler::success(['order' => $order], 'Order detail retrieved.');
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * POST /delivery/orders/{id}/accept
     */
    public function acceptOrder($id) {
        AuthGuard::handle();
        RoleCheck::handle('delivery');
        $user = AuthGuard::getUser();

        try {
            $stmt = $this->db->prepare("UPDATE orders SET delivery_partner_id = ?, status = 'assigned', assigned_at = NOW() WHERE id = ? AND delivery_partner_id IS NULL");
            $stmt->execute([$user['id'], $id]);
            
            if ($stmt->rowCount() === 0) return ResponseHandler::badRequest("Order $id is already assigned or invalid.");

            return ResponseHandler::success(['order_id' => $id, 'status' => 'assigned'], "Order $id accepted successfully.");
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * POST /delivery/orders/{id}/status
     */
    public function updateStatus($id) {
        AuthGuard::handle();
        RoleCheck::handle('delivery');
        $user   = AuthGuard::getUser();
        $data   = $this->getPostData();
        $status = $data['status'] ?? 'picked_up';

        try {
            // If picking up, verify Pickup OTP from Vendor
            if ($status === 'picked_up') {
                $otp = $data['otp'] ?? '';
                if (empty($otp)) {
                    return ResponseHandler::badRequest('Pharmacy handover OTP is required to mark as picked up.');
                }

                $orderStmt = $this->db->prepare("SELECT pickup_otp FROM orders WHERE id = ?");
                $orderStmt->execute([$id]);
                $order = $orderStmt->fetch();

                if (($order['pickup_otp'] ?? '') !== $otp) {
                    return ResponseHandler::error('Invalid pickup OTP. Pharmacy verification failed.', 401);
                }
            }

            // If delivering, require and verify Delivery OTP from Customer
            if ($status === 'delivered') {
                $otp = $data['otp'] ?? '';
                if (empty($otp)) {
                    return ResponseHandler::badRequest('Clinical handover OTP is required to mark as delivered.');
                }

                $orderStmt = $this->db->prepare("SELECT delivery_otp, delivery_partner_id FROM orders WHERE id = ?");
                $orderStmt->execute([$id]);
                $order = $orderStmt->fetch();

                if (!$order || $order['delivery_partner_id'] != $user['id']) {
                    return ResponseHandler::forbidden('Access denied to this clinical dispatch.');
                }

                if (($order['delivery_otp'] ?? '') !== $otp) {
                    return ResponseHandler::error('Invalid handover OTP. Clinical verification failed.', 401);
                }

                // --- COD Management ---
                // If it was a COD order, increment rider's cash_in_hand
                $orderInfoStmt = $this->db->prepare("SELECT grand_total as amount, payment_method FROM orders WHERE id = ?");
                $orderInfoStmt->execute([$id]);
                $orderInfo = $orderInfoStmt->fetch();

                if (($orderInfo['payment_method'] ?? '') === 'cod') {
                    $this->db->prepare("UPDATE delivery_profiles SET cash_in_hand = cash_in_hand + ? WHERE user_id = ?")
                             ->execute([$orderInfo['amount'], $user['id']]);
                    
                    // Also update payment status to paid
                    $this->db->prepare("UPDATE orders SET payment_status = 'paid' WHERE id = ?")->execute([$id]);
                }
            }

            // Handle Failed Delivery Attempt
             if ($status === 'failed_delivery') {
                 $reason = $data['reason'] ?? 'Not specified';
                 $stmt = $this->db->prepare("UPDATE orders SET status = 'failed_delivery', failure_reason = ?, failure_at = NOW() WHERE id = ? AND delivery_partner_id = ?");
                 $stmt->execute([$reason, $id, $user['id']]);
             } else {
                 $stmt = $this->db->prepare("UPDATE orders SET status = :status, delivered_at = CASE WHEN :status_check = 'delivered' THEN NOW() ELSE NULL END WHERE id = :id");
                 $stmt->execute([
                     'status' => $status,
                     'status_check' => $status,
                     'id' => $id
                 ]);
             }
            
            // Log status change
            $this->db->prepare("INSERT INTO order_status_logs (order_id, status, comment, changed_by) VALUES (?, ?, ?, ?)")
                     ->execute([$id, $status, ($status === 'failed_delivery' ? $data['reason'] ?? '' : null), $user['id']]);

            // Phase 4: Proof of Delivery Notification for Patient
            if ($status === 'delivered') {
              try {
                $orderUserStmt = $this->db->prepare("SELECT user_id FROM orders WHERE id = ?");
                $orderUserStmt->execute([$id]);
                $orderUserId = $orderUserStmt->fetchColumn();
                
                if ($orderUserId) {
                    $proofText = !empty($data['proof_image']) ? "Proof of delivery image has been secured." : "Signed by recipient.";
                    $msg = "Your order #MM-{$id} has been successfully delivered. {$proofText}";
                    $this->db->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, 'Delivery Confirmation', ?, 'order')")
                             ->execute([$orderUserId, $msg]);
                }

                // Auto-create vendor commission on delivery
                $commOrder = $this->db->prepare("SELECT vendor_id, grand_total FROM orders WHERE id = ?");
                $commOrder->execute([$id]);
                $commData = $commOrder->fetch();
                if ($commData) {
                    $rate = 0.15; // Default 15%
                    try {
                        $rateStmt = $this->db->prepare("SELECT value FROM settings WHERE `key` = 'vendor_commission_percent'");
                        $rateStmt->execute();
                        $rateFetch = $rateStmt->fetch();
                        if ($rateFetch) $rate = (float)$rateFetch['value'] / 100;
                    } catch (\Exception $e) {}
                    
                    $commAmt = round((float)$commData['grand_total'] * $rate, 2);
                    try {
                        $this->db->prepare("INSERT IGNORE INTO vendor_commissions (vendor_id, order_id, commission_amount, status) VALUES (?, ?, ?, 'pending')")
                                 ->execute([$commData['vendor_id'], $id, $commAmt]);
                    } catch (\Exception $e) {}
                    
                    // Auto-credit delivery fee to rider
                    try {
                        $this->db->prepare("INSERT INTO delivery_payouts (rider_id, amount, status) VALUES (?, 30.00, 'pending')")
                                 ->execute([$user['id']]);
                    } catch (\Exception $e) {}
                }
              } catch (\Exception $e) {
                // Non-critical post-delivery tasks — don't fail the delivery
              }

                // === EMERGENCY INCENTIVE: ₹50 bonus for emergency delivery ===
                try {
                $emergencyCheckStmt = $this->db->prepare("SELECT is_emergency, delivery_method_id FROM orders WHERE id = ?");
                $emergencyCheckStmt->execute([$id]);
                $emergencyInfo = $emergencyCheckStmt->fetch();
                if ($emergencyInfo && ($emergencyInfo['is_emergency'] == 1 || $emergencyInfo['delivery_method_id'] == 3)) {
                    try {
                        $this->db->prepare("INSERT INTO delivery_payouts (rider_id, amount, status, description) VALUES (?, 50.00, 'pending', 'Emergency delivery bonus')")
                                 ->execute([$user['id']]);
                    } catch (\Exception $e) {
                        // Fallback if description column doesn't exist
                        try {
                            $this->db->prepare("INSERT INTO delivery_payouts (rider_id, amount, status) VALUES (?, 50.00, 'pending')")
                                     ->execute([$user['id']]);
                        } catch (\Exception $e2) { /* table may not exist */ }
                    }
                }
                } catch (\Exception $e) { /* emergency incentive non-critical */ }
            }

            return ResponseHandler::success(['order_id' => $id, 'status' => $status], "Order $id status transitioned to $status.");
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * POST /delivery/orders/{id}/reject
     */
    public function rejectOrder($id) {
        AuthGuard::handle();
        RoleCheck::handle('delivery');
        $user = AuthGuard::getUser();
        $data = $this->getPostData();
        $reason = $data['reason'] ?? 'Logistics constraint';

        try {
            $stmt = $this->db->prepare("UPDATE orders SET delivery_partner_id = NULL, status = 'confirmed', rejection_reason = ? WHERE id = ? AND delivery_partner_id = ?");
            $stmt->execute([$reason, $id, $user['id']]);
            
            if ($stmt->rowCount() === 0) return ResponseHandler::badRequest("Order $id cannot be rejected or is not assigned to you.");

            $this->db->prepare("INSERT INTO order_status_logs (order_id, status, comment, changed_by) VALUES (?, 'returned_to_queue', ?, ?)")
                     ->execute([$id, $reason, $user['id']]);

            return ResponseHandler::success([], "Order $id returned to dispatch queue.");
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * POST /delivery/payout/request
     */
    public function requestPayout() {
        AuthGuard::handle();
        RoleCheck::handle('delivery');
        $user = AuthGuard::getUser();
        $data = $this->getPostData();
        $amount = (float)($data['amount'] ?? 0);

        if ($amount <= 0) return ResponseHandler::badRequest("Invalid payout amount.");

        try {
            // Check available balance (delivered orders not yet paid out)
            $stmt = $this->db->prepare("SELECT SUM(delivery_fee) as earned FROM orders WHERE delivery_partner_id = ? AND status = 'delivered'");
            $stmt->execute([$user['id']]);
            $earned = (float)($stmt->fetch()['earned'] ?? 0);

            $payoutStmt = $this->db->prepare("SELECT SUM(amount) as paid FROM delivery_payouts WHERE rider_id = ? AND status != 'failed'");
            $payoutStmt->execute([$user['id']]);
            $paid = (float)($payoutStmt->fetch()['paid'] ?? 0);

            $balance = $earned - $paid;

            if ($amount > $balance) return ResponseHandler::error("Insufficient balance. Available: ₹$balance", 400);

            $this->db->prepare("INSERT INTO delivery_payouts (rider_id, amount, status) VALUES (?, ?, 'pending')")
                     ->execute([$user['id'], $amount]);

            return ResponseHandler::success([], "Payout request of ₹$amount submitted successfully.");
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    public function updateLocation() {
        AuthGuard::handle();
        RoleCheck::handle('delivery');
        $user = AuthGuard::getUser();
        $data = $this->getPostData();
        $lat = $data['latitude'] ?? null;
        $lng = $data['longitude'] ?? null;

        if (!$lat || !$lng) return ResponseHandler::badRequest("GPS coordinates missing.");

        try {
            $stmt = $this->db->prepare("INSERT INTO agent_locations (agent_id, latitude, longitude, updated_at) 
                                        VALUES (?, ?, ?, NOW()) 
                                        ON DUPLICATE KEY UPDATE latitude = VALUES(latitude), longitude = VALUES(longitude), updated_at = NOW()");
            $stmt->execute([$user['id'], $lat, $lng]);

            return ResponseHandler::success([], "Logistics telemetry synchronized.");
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    public function toggleDuty() {
        AuthGuard::handle();
        RoleCheck::handle('delivery');
        $user = AuthGuard::getUser();
        $data = $this->getPostData();
        $status = $data['status'] ?? 'inactive'; // 'active' or 'inactive'

        try {
            $this->db->prepare("UPDATE delivery_profiles SET status = ? WHERE user_id = ?")
                     ->execute([$status, $user['id']]);
            return ResponseHandler::success(['status' => $status], "Duty status transitioned to $status.");
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    public function uploadProof($id) {
        AuthGuard::handle();
        RoleCheck::handle('delivery');
        
        if (!isset($_FILES['proof'])) {
            return ResponseHandler::badRequest("No image provided for proof.");
        }

        try {
            $uploadDir = 'uploads/proofs';
            $filename = time() . '_' . $_FILES['proof']['name'];
            $targetPath = __DIR__ . '/../../public/' . $uploadDir . '/';
            if (!is_dir($targetPath)) mkdir($targetPath, 0755, true);

            if (move_uploaded_file($_FILES['proof']['tmp_name'], $targetPath . $filename)) {
                $path = $uploadDir . '/' . $filename;
                $this->db->prepare("UPDATE orders SET proof_image = ? WHERE id = ?")
                         ->execute([$path, $id]);
                return ResponseHandler::success(['path' => $path], "Clinical delivery proof anchored.");
            }
            throw new Exception("File move failed.");
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * GET /delivery/earnings
     */
    public function earnings() {
        AuthGuard::handle();
        RoleCheck::handle('delivery');
        $user = AuthGuard::getUser();

        try {
            $stmt = $this->db->prepare("SELECT 
                                        SUM(CASE WHEN DATE(created_at) = CURDATE() THEN delivery_fee ELSE 0 END) as today,
                                        SUM(CASE WHEN YEARWEEK(created_at) = YEARWEEK(CURDATE()) THEN delivery_fee ELSE 0 END) as this_week,
                                        SUM(CASE WHEN MONTH(created_at) = MONTH(CURDATE()) THEN delivery_fee ELSE 0 END) as this_month
                                        FROM orders WHERE delivery_partner_id = ? AND status = 'delivered'");
            $stmt->execute([$user['id']]);
            $totals = $stmt->fetch();

            $payoutStmt = $this->db->prepare("SELECT SUM(amount) as pending FROM delivery_payouts WHERE rider_id = ? AND status = 'pending'");
            $payoutStmt->execute([$user['id']]);
            $payout = $payoutStmt->fetch();

            // Daily breakdown for the last 7 days
            $breakdownStmt = $this->db->prepare("SELECT DATE(delivered_at) as day, SUM(delivery_fee) as yield, COUNT(*) as dispatches 
                                                FROM orders 
                                                WHERE delivery_partner_id = ? AND status = 'delivered' 
                                                AND delivered_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                                                GROUP BY DATE(delivered_at) 
                                                ORDER BY day DESC");
            $breakdownStmt->execute([$user['id']]);
            $breakdown = $breakdownStmt->fetchAll();

            $cashStmt = $this->db->prepare("SELECT cash_in_hand FROM delivery_profiles WHERE user_id = ?");
            $cashStmt->execute([$user['id']]);
            $cash = $cashStmt->fetch();

            return ResponseHandler::success([
                'today'            => (float)($totals['today'] ?? 0),
                'this_week'        => (float)($totals['this_week'] ?? 0),
                'this_month'       => (float)($totals['this_month'] ?? 0),
                'pending_payout'   => (float)($payout['pending'] ?? 0.00),
                'cash_in_hand'     => (float)($cash['cash_in_hand'] ?? 0.00),
                'daily_breakdown'  => $breakdown
            ], 'Earnings data retrieved.');
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * GET /delivery/partner/settings
     */
    public function getSettings() {
        AuthGuard::handle();
        RoleCheck::handle('delivery');
        $user = AuthGuard::getUser();

        try {
            $stmt = $this->db->prepare("SELECT vehicle_type, vehicle_number, zone, license_number, status, aadhaar_number, pan_number, bank_name, account_number, ifsc_code, current_address, permanent_address FROM delivery_profiles WHERE user_id = ?");
            $stmt->execute([$user['id']]);
            $profile = $stmt->fetch();

            // Total Earnings & Deliveries
            $statsStmt = $this->db->prepare("SELECT COUNT(*) as total_deliveries, SUM(delivery_fee) as total_earnings FROM orders WHERE delivery_partner_id = ? AND status = 'delivered'");
            $statsStmt->execute([$user['id']]);
            $stats = $statsStmt->fetch();

            // Query user name and email
            $userStmt = $this->db->prepare("SELECT name, email FROM users WHERE id = ?");
            $userStmt->execute([$user['id']]);
            $userData = $userStmt->fetch();

            // Avg Rating
            $avgRating = (new \App\Models\DeliveryReview())->getAverageRating($user['id']);

            // Combine vehicle type and number for frontend split compatibility
            $vehicleType = !empty($profile['vehicle_type']) ? $profile['vehicle_type'] : 'Two-Wheeler';
            $vehicleNum = !empty($profile['vehicle_number']) ? $profile['vehicle_number'] : 'EV';
            $vehicle = "{$vehicleType} ({$vehicleNum})";

            return ResponseHandler::success([
                'name'                => $userData['name'] ?? 'Rider',
                'email'               => $userData['email'] ?? '',
                'agent_id'            => 'MM-DLV-' . str_pad($user['id'], 4, '0', STR_PAD_LEFT),
                'vehicle'             => $vehicle,
                'vehicle_type'        => $vehicleType,
                'vehicle_number'      => $vehicleNum,
                'license_number'      => $profile['license_number'] ?? 'N/A',
                'zone'                => $profile['zone'] ?? 'Phaltan Hub',
                'aadhaar_number'      => $profile['aadhaar_number'] ?? '',
                'pan_number'          => $profile['pan_number'] ?? '',
                'current_address'     => $profile['current_address'] ?? '',
                'permanent_address'   => $profile['permanent_address'] ?? '',
                'bank_name'           => $profile['bank_name'] ?? '',
                'account_number'      => $profile['account_number'] ?? '',
                'ifsc_code'           => $profile['ifsc_code'] ?? '',
                'total_earnings'      => (float)($stats['total_earnings'] ?? 0.00),
                'total_deliveries'    => (int)($stats['total_deliveries'] ?? 0),
                'rating'              => round((float)($avgRating ?? 5.0), 1),
                'status'              => $profile['status'] ?? 'offline'
            ], 'Delivery profile synchronization complete.');
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * GET /delivery/ratings
     */
    public function ratings() {
        AuthGuard::handle();
        RoleCheck::handle('delivery');
        $user = AuthGuard::getUser();

        $reviews = (new \App\Models\DeliveryReview())->getByRider($user['id']);
        $avg     = (new \App\Models\DeliveryReview())->getAverageRating($user['id']);

        return ResponseHandler::success([
            'average_rating' => $avg,
            'reviews'        => $reviews
        ], 'Performance metrics synchronized.');
    }


    /**
     * PUT /delivery/partner/settings
     */
    public function updateSettings() {
        AuthGuard::handle();
        RoleCheck::handle('delivery');
        $user = AuthGuard::getUser();
        $data = $this->getPostData();

        try {
            $stmt = $this->db->prepare("UPDATE delivery_profiles SET 
                vehicle_type = ?, 
                vehicle_number = ?, 
                zone = ?,
                license_number = ?,
                status = ? 
                WHERE user_id = ?");
            $stmt->execute([
                $data['vehicle_type'] ?? 'Bike',
                $data['vehicle_number'] ?? '',
                $data['zone'] ?? '',
                $data['license_number'] ?? '',
                $data['status'] ?? 'offline',
                $user['id']
            ]);

            return ResponseHandler::success([], 'Partner settings synchronized successfully.');
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * POST /admin/dispatch/auto-assign
     */
    public function autoAssign() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        if ($user['role'] !== 'admin' && $user['role'] !== 'delivery') return ResponseHandler::forbidden();

        try {
            // 1. Get all confirmed orders without a partner
            // Including coordinates from vendor and customer address
            $stmt = $this->db->prepare("SELECT o.id, o.vendor_id, o.address_id, o.is_emergency, o.delivery_method_id,
                                        vp.latitude as v_lat, vp.longitude as v_lng,
                                        a.latitude as c_lat, a.longitude as c_lng
                                        FROM orders o 
                                        LEFT JOIN vendor_profiles vp ON o.vendor_id = vp.user_id
                                        LEFT JOIN addresses a ON o.address_id = a.id
                                        WHERE o.status = 'confirmed' AND o.delivery_partner_id IS NULL 
                                        ORDER BY o.is_emergency DESC, o.delivery_method_id = 3 DESC, o.created_at ASC");
            $stmt->execute();
            $pendingOrders = $stmt->fetchAll();

            if (empty($pendingOrders)) {
                return ResponseHandler::success([], 'No pending clinical dispatches in queue.');
            }

            // 2. Get all active delivery agents with locations
            // First try agents with recent location (30 min)
            $agentStmt = $this->db->prepare("SELECT u.id, al.latitude, al.longitude 
                                             FROM users u 
                                             JOIN agent_locations al ON u.id = al.agent_id
                                             WHERE u.role = 'delivery' AND u.status = 'active'
                                             AND u.deleted_at IS NULL
                                             AND al.updated_at >= DATE_SUB(NOW(), INTERVAL 30 MINUTE)
                                             ORDER BY al.updated_at DESC");
            $agentStmt->execute();
            $activeAgents = $agentStmt->fetchAll();

            // Fallback: if no recent locations, get any active agent with last known location
            if (empty($activeAgents)) {
                $agentStmt = $this->db->prepare("SELECT u.id, al.latitude, al.longitude 
                                                 FROM users u 
                                                 JOIN agent_locations al ON u.id = al.agent_id
                                                 WHERE u.role = 'delivery' AND u.status = 'active'
                                                 AND u.deleted_at IS NULL
                                                 ORDER BY al.updated_at DESC");
                $agentStmt->execute();
                $activeAgents = $agentStmt->fetchAll();
                // Deduplicate - keep only latest location per agent
                $seen = [];
                $uniqueAgents = [];
                foreach ($activeAgents as $a) {
                    if (!in_array($a['id'], $seen)) {
                        $seen[] = $a['id'];
                        $uniqueAgents[] = $a;
                    }
                }
                $activeAgents = $uniqueAgents;
            }

            if (empty($activeAgents)) {
                return ResponseHandler::error('No active logistics nodes available for assignment.', 404);
            }

            $assignedCount = 0;
            $batchSize = 3; // Max orders per rider
            $emergencyAssignedAgents = []; // Track agents assigned to emergency (exclusive)

            // === PHASE 1: Assign EMERGENCY orders FIRST (dedicated rider, no batching) ===
            // Emergency = is_emergency = 1 OR delivery_method_id = 3
            $emergencyOrders = array_filter($pendingOrders, fn($o) => $o['is_emergency'] == 1 || $o['delivery_method_id'] == 3);
            $standardOrders = array_filter($pendingOrders, fn($o) => $o['is_emergency'] != 1 && $o['delivery_method_id'] != 3);

            foreach ($emergencyOrders as $order) {
                $checkStmt = $this->db->prepare("SELECT delivery_partner_id FROM orders WHERE id = ?");
                $checkStmt->execute([$order['id']]);
                if ($checkStmt->fetch()['delivery_partner_id']) continue;

                $nearestAgentId = null;
                $minDistance = 999999;
                $within5km = false;

                // Find nearest AVAILABLE agent WITHIN 5KM of vendor (not already on emergency duty)
                foreach ($activeAgents as $agent) {
                    if (in_array($agent['id'], $emergencyAssignedAgents)) continue;
                    
                    // Check agent doesn't have active emergency orders (max 1 emergency at a time)
                    $loadStmt = $this->db->prepare("SELECT COUNT(*) as cnt FROM orders WHERE delivery_partner_id = ? AND (is_emergency = 1 OR delivery_method_id = 3) AND status NOT IN ('delivered', 'cancelled', 'returned')");
                    $loadStmt->execute([$agent['id']]);
                    if ($loadStmt->fetch()['cnt'] > 0) continue; // Already on emergency

                    $dist = $this->calculateDistance($order['v_lat'], $order['v_lng'], $agent['latitude'], $agent['longitude']);
                    
                    // Only consider riders within 5km for emergency
                    if ($dist <= 5.0 && $dist < $minDistance) {
                        $minDistance = $dist;
                        $nearestAgentId = $agent['id'];
                        $within5km = true;
                    }
                }

                // Fallback: If no rider within 5km, assign nearest available but log warning
                if (!$nearestAgentId) {
                    foreach ($activeAgents as $agent) {
                        if (in_array($agent['id'], $emergencyAssignedAgents)) continue;
                        
                        $loadStmt = $this->db->prepare("SELECT COUNT(*) as cnt FROM orders WHERE delivery_partner_id = ? AND (is_emergency = 1 OR delivery_method_id = 3) AND status NOT IN ('delivered', 'cancelled', 'returned')");
                        $loadStmt->execute([$agent['id']]);
                        if ($loadStmt->fetch()['cnt'] > 0) continue;

                        $dist = $this->calculateDistance($order['v_lat'], $order['v_lng'], $agent['latitude'], $agent['longitude']);
                        if ($dist < $minDistance) {
                            $minDistance = $dist;
                            $nearestAgentId = $agent['id'];
                        }
                    }

                    // Log warning: no rider within 5km for emergency order
                    if ($nearestAgentId) {
                        error_log("[EMERGENCY_WARNING] Order #{$order['id']}: No rider within 5km of vendor. Assigned rider #{$nearestAgentId} at {$minDistance}km distance.");
                        try {
                            $this->db->prepare("INSERT INTO order_status_logs (order_id, status, comment, changed_by) VALUES (?, 'emergency_warning', ?, 0)")
                                     ->execute([$order['id'], "No rider within 5km. Nearest rider at {$minDistance}km assigned."]);
                        } catch (\Exception $e) { /* log table issue */ }
                    }
                }

                if ($nearestAgentId) {
                    // Assign with emergency SLA (40 minutes)
                    $slaDeadline = date('Y-m-d H:i:s', strtotime('+40 minutes'));
                    $update = $this->db->prepare("UPDATE orders SET delivery_partner_id = ?, status = 'assigned', assigned_at = NOW(), sla_deadline = ? WHERE id = ?");
                    $update->execute([$nearestAgentId, $slaDeadline, $order['id']]);
                    $emergencyAssignedAgents[] = $nearestAgentId; // Lock this agent for emergency
                    $assignedCount++;
                }
            }

            // === PHASE 2: Assign STANDARD orders (batching allowed) ===
            foreach ($standardOrders as $order) {
                $checkStmt = $this->db->prepare("SELECT delivery_partner_id FROM orders WHERE id = ?");
                $checkStmt->execute([$order['id']]);
                if ($checkStmt->fetch()['delivery_partner_id']) continue;

                $nearestAgentId = null;
                $minDistance = 999999;

                // Find nearest agent to Vendor (exclude emergency-locked agents)
                foreach ($activeAgents as $agent) {
                    if (in_array($agent['id'], $emergencyAssignedAgents)) continue;
                    $dist = $this->calculateDistance($order['v_lat'], $order['v_lng'], $agent['latitude'], $agent['longitude']);
                    if ($dist < $minDistance) {
                        $minDistance = $dist;
                        $nearestAgentId = $agent['id'];
                    }
                }

                if ($nearestAgentId) {
                    // Assign main order
                    $update = $this->db->prepare("UPDATE orders SET delivery_partner_id = ?, status = 'assigned', assigned_at = NOW() WHERE id = ?");
                    $update->execute([$nearestAgentId, $order['id']]);
                    $assignedCount++;

                    // --- BATCHING LOGIC ---
                    // Look for other orders from SAME vendor or NEARBY customer (within 2km)
                    foreach ($pendingOrders as $otherOrder) {
                        if ($otherOrder['id'] === $order['id']) continue;
                        
                        $isSameVendor = ($otherOrder['vendor_id'] === $order['vendor_id']);
                        $isNearby = $this->calculateDistance($order['c_lat'], $order['c_lng'], $otherOrder['c_lat'], $otherOrder['c_lng']) < 2.0;

                        if ($isSameVendor || $isNearby) {
                            // Check rider current load
                            $loadStmt = $this->db->prepare("SELECT COUNT(*) as cnt FROM orders WHERE delivery_partner_id = ? AND status NOT IN ('delivered', 'cancelled', 'returned')");
                            $loadStmt->execute([$nearestAgentId]);
                            if ($loadStmt->fetch()['cnt'] < $batchSize) {
                                $this->db->prepare("UPDATE orders SET delivery_partner_id = ?, status = 'assigned', assigned_at = NOW() WHERE id = ? AND delivery_partner_id IS NULL")
                                         ->execute([$nearestAgentId, $otherOrder['id']]);
                                $assignedCount++;
                            }
                        }
                    }
                }
            }

            return ResponseHandler::success(['assigned' => $assignedCount], "Logistics optimization protocol executed. $assignedCount clinical orders assigned/batched.");
        } catch (Exception $e) {
            return ResponseHandler::error('Dispatch failure: ' . $e->getMessage(), 500);
        }
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        if (!$lat1 || !$lon1 || !$lat2 || !$lon2) return 999999;
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        return ($miles * 1.609344); // Kilometers
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Emergency Order Priority Endpoints (Task 6)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * GET /delivery/priority-queue
     * Returns all assigned orders for the authenticated delivery partner.
     * Emergency orders sorted first by remaining SLA time ascending, then standard orders.
     * Includes: SLA countdown, vendor pickup location, route info.
     */
    public function priorityQueue() {
        AuthGuard::handle();
        RoleCheck::handle('delivery');
        $user = AuthGuard::getUser();

        try {
            $priorityEngine = new \App\Services\PriorityEngine();

            // Fetch all active orders assigned to this delivery partner
            $stmt = $this->db->prepare(
                "SELECT o.id, o.user_id, o.vendor_id, o.status, o.is_emergency,
                        o.created_at, o.sla_deadline, o.emergency_locked,
                        o.emergency_acknowledged_at, o.emergency_ready_at,
                        o.emergency_picked_up_at, o.grand_total, o.delivery_fee,
                        u.name AS customer_name, u.phone AS customer_phone,
                        a.address_line1 AS customer_address, a.latitude AS customer_lat, a.longitude AS customer_lng,
                        vp.pharmacy_name AS vendor_name, vp.address AS vendor_address,
                        vp.latitude AS vendor_lat, vp.longitude AS vendor_lng
                 FROM orders o
                 JOIN users u ON o.user_id = u.id
                 LEFT JOIN addresses a ON o.address_id = a.id
                 LEFT JOIN vendor_profiles vp ON o.vendor_id = vp.user_id
                 WHERE o.delivery_partner_id = ?
                   AND o.status NOT IN ('delivered', 'cancelled', 'returned')
                 ORDER BY o.is_emergency DESC, o.sla_deadline ASC, o.created_at ASC"
            );
            $stmt->execute([$user['id']]);
            $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get rider's current location for route info
            $locStmt = $this->db->prepare("SELECT latitude, longitude FROM agent_locations WHERE agent_id = ?");
            $locStmt->execute([$user['id']]);
            $riderLoc = $locStmt->fetch(\PDO::FETCH_ASSOC);

            $emergencyOrders = [];
            $standardOrders = [];

            foreach ($orders as $order) {
                $item = [
                    'order_id' => (int) $order['id'],
                    'status' => $order['status'],
                    'is_emergency' => (bool) $order['is_emergency'],
                    'customer' => [
                        'name' => $order['customer_name'],
                        'phone' => $order['customer_phone'],
                        'address' => $order['customer_address'],
                    ],
                    'vendor' => [
                        'name' => $order['vendor_name'] ?? 'Unknown Pharmacy',
                        'address' => $order['vendor_address'],
                        'latitude' => $order['vendor_lat'],
                        'longitude' => $order['vendor_lng'],
                    ],
                    'amount' => (float) $order['grand_total'],
                    'delivery_fee' => (float) $order['delivery_fee'],
                    'created_at' => $order['created_at'],
                ];

                // Add route info (distance from rider to vendor/customer)
                if ($riderLoc && $riderLoc['latitude'] && $riderLoc['longitude']) {
                    if (!$order['emergency_picked_up_at'] && $order['vendor_lat'] && $order['vendor_lng']) {
                        // Not picked up yet - show distance to vendor
                        $distToVendor = $this->calculateDistance(
                            $riderLoc['latitude'], $riderLoc['longitude'],
                            $order['vendor_lat'], $order['vendor_lng']
                        );
                        $item['route'] = [
                            'destination' => 'vendor',
                            'distance_km' => round($distToVendor, 2),
                        ];
                    } elseif ($order['customer_lat'] && $order['customer_lng']) {
                        // Picked up - show distance to customer
                        $distToCustomer = $this->calculateDistance(
                            $riderLoc['latitude'], $riderLoc['longitude'],
                            $order['customer_lat'], $order['customer_lng']
                        );
                        $item['route'] = [
                            'destination' => 'customer',
                            'distance_km' => round($distToCustomer, 2),
                        ];
                    }
                }

                // Add SLA info for emergency orders
                if ($order['is_emergency'] && $order['sla_deadline']) {
                    $slaDeadlineTs = strtotime($order['sla_deadline']);
                    $now = time();
                    $remainingSeconds = $slaDeadlineTs - $now;

                    $item['sla'] = [
                        'deadline' => $order['sla_deadline'],
                        'remaining_seconds' => $remainingSeconds,
                        'countdown' => $remainingSeconds > 0
                            ? sprintf('%02d:%02d:%02d', floor($remainingSeconds / 3600), floor(($remainingSeconds % 3600) / 60), $remainingSeconds % 60)
                            : '00:00:00',
                        'status' => $remainingSeconds < 0 ? 'breached' : ($remainingSeconds < 600 ? 'warning' : 'normal'),
                        'is_breached' => $remainingSeconds < 0,
                    ];

                    $emergencyOrders[] = $item;
                } else {
                    $standardOrders[] = $item;
                }
            }

            // Sort emergency orders by remaining SLA time ascending (most urgent first)
            usort($emergencyOrders, function ($a, $b) {
                return ($a['sla']['remaining_seconds'] ?? 0) <=> ($b['sla']['remaining_seconds'] ?? 0);
            });

            $queue = array_merge($emergencyOrders, $standardOrders);

            return ResponseHandler::success([
                'queue' => $queue,
                'emergency_count' => count($emergencyOrders),
                'standard_count' => count($standardOrders),
            ], 'Priority queue retrieved.');
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * POST /delivery/emergency/{id}/accept
     * Record acceptance timestamp in assignment log, transition order status to "accepted".
     * Validate acceptance is within timeout window (90 seconds).
     */
    public function acceptEmergency($id) {
        AuthGuard::handle();
        RoleCheck::handle('delivery');
        $user = AuthGuard::getUser();

        try {
            // Verify the order exists, is emergency, and is assigned to this partner
            $stmt = $this->db->prepare(
                "SELECT o.id, o.status, o.delivery_partner_id, o.is_emergency, o.assigned_at
                 FROM orders o
                 WHERE o.id = ? AND o.is_emergency = 1"
            );
            $stmt->execute([$id]);
            $order = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$order) {
                return ResponseHandler::notFound('Emergency order not found.');
            }

            if ((int) $order['delivery_partner_id'] !== (int) $user['id']) {
                return ResponseHandler::forbidden('This emergency order is not assigned to you.');
            }

            if ($order['status'] === 'accepted') {
                return ResponseHandler::badRequest('Order has already been accepted.');
            }

            // Validate acceptance is within 90-second timeout window
            if ($order['assigned_at']) {
                $assignedAt = strtotime($order['assigned_at']);
                $now = time();
                $elapsed = $now - $assignedAt;

                if ($elapsed > 90) {
                    return ResponseHandler::error('Acceptance timeout exceeded. The 90-second window has expired.', 422);
                }
            }

            // Transition order status to "accepted"
            $updateStmt = $this->db->prepare(
                "UPDATE orders SET status = 'accepted' WHERE id = ?"
            );
            $updateStmt->execute([$id]);

            // Record acceptance timestamp in emergency_assignment_log
            $logStmt = $this->db->prepare(
                "UPDATE emergency_assignment_log
                 SET status = 'accepted', accepted_at = NOW()
                 WHERE order_id = ? AND partner_id = ? AND status = 'assigned'
                 ORDER BY created_at DESC LIMIT 1"
            );
            $logStmt->execute([$id, $user['id']]);

            // Log status change
            $this->db->prepare(
                "INSERT INTO order_status_logs (order_id, status, comment, changed_by) VALUES (?, 'accepted', 'Emergency order accepted by delivery partner', ?)"
            )->execute([$id, $user['id']]);

            return ResponseHandler::success([
                'order_id' => (int) $id,
                'status' => 'accepted',
                'accepted_at' => date('Y-m-d H:i:s'),
            ], "Emergency order #{$id} accepted successfully.");
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * POST /delivery/emergency/{id}/pickup
     * Set emergency_picked_up_at timestamp, update order status to "picked_up".
     */
    public function pickupEmergency($id) {
        AuthGuard::handle();
        RoleCheck::handle('delivery');
        $user = AuthGuard::getUser();

        try {
            // Verify the order exists, is emergency, and is assigned to this partner
            $stmt = $this->db->prepare(
                "SELECT o.id, o.status, o.delivery_partner_id, o.is_emergency
                 FROM orders o
                 WHERE o.id = ? AND o.is_emergency = 1"
            );
            $stmt->execute([$id]);
            $order = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$order) {
                return ResponseHandler::notFound('Emergency order not found.');
            }

            if ((int) $order['delivery_partner_id'] !== (int) $user['id']) {
                return ResponseHandler::forbidden('This emergency order is not assigned to you.');
            }

            if ($order['status'] === 'picked_up') {
                return ResponseHandler::badRequest('Order has already been picked up.');
            }

            // Update order: set emergency_picked_up_at and status
            $updateStmt = $this->db->prepare(
                "UPDATE orders SET status = 'picked_up', emergency_picked_up_at = NOW() WHERE id = ?"
            );
            $updateStmt->execute([$id]);

            // Log status change
            $this->db->prepare(
                "INSERT INTO order_status_logs (order_id, status, comment, changed_by) VALUES (?, 'picked_up', 'Emergency order picked up by delivery partner', ?)"
            )->execute([$id, $user['id']]);

            return ResponseHandler::success([
                'order_id' => (int) $id,
                'status' => 'picked_up',
                'picked_up_at' => date('Y-m-d H:i:s'),
            ], "Emergency order #{$id} picked up successfully.");
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * POST /delivery/emergency/{id}/deliver
     * Set emergency_delivered_at timestamp, update order status to "delivered".
     * Unlock delivery partner (PriorityEngine::unlockPartner).
     * Log SLA breach if delivery time exceeded 40-minute deadline.
     */
    public function deliverEmergency($id) {
        AuthGuard::handle();
        RoleCheck::handle('delivery');
        $user = AuthGuard::getUser();

        try {
            // Verify the order exists, is emergency, and is assigned to this partner
            $stmt = $this->db->prepare(
                "SELECT o.id, o.status, o.delivery_partner_id, o.is_emergency,
                        o.sla_deadline, o.created_at
                 FROM orders o
                 WHERE o.id = ? AND o.is_emergency = 1"
            );
            $stmt->execute([$id]);
            $order = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$order) {
                return ResponseHandler::notFound('Emergency order not found.');
            }

            if ((int) $order['delivery_partner_id'] !== (int) $user['id']) {
                return ResponseHandler::forbidden('This emergency order is not assigned to you.');
            }

            if ($order['status'] === 'delivered') {
                return ResponseHandler::badRequest('Order has already been delivered.');
            }

            $now = date('Y-m-d H:i:s');
            $slaBreach = false;

            // Check if SLA was breached (delivery time exceeded 40-minute deadline)
            if ($order['sla_deadline']) {
                $slaDeadlineTs = strtotime($order['sla_deadline']);
                if (time() > $slaDeadlineTs) {
                    $slaBreach = true;
                }
            }

            // Update order: set emergency_delivered_at, status, and sla_breach flag
            $updateStmt = $this->db->prepare(
                "UPDATE orders
                 SET status = 'delivered',
                     emergency_delivered_at = NOW(),
                     delivered_at = NOW(),
                     sla_breach = ?
                 WHERE id = ?"
            );
            $updateStmt->execute([$slaBreach ? 1 : 0, $id]);

            // Unlock delivery partner using PriorityEngine
            $priorityEngine = new \App\Services\PriorityEngine();
            $priorityEngine->unlockPartner((int) $user['id']);

            // Log SLA breach if applicable
            if ($slaBreach) {
                $elapsedSeconds = time() - strtotime($order['created_at']);
                $breachSeconds = time() - strtotime($order['sla_deadline']);

                // Log to emergency_escalations
                try {
                    $this->db->prepare(
                        "INSERT INTO emergency_escalations (order_id, escalation_level, reason, triggered_at, partner_id, remaining_minutes, action_taken, created_at)
                         VALUES (?, 'breach', ?, NOW(), ?, ?, 'Delivery completed after SLA breach', NOW())"
                    )->execute([
                        $id,
                        "SLA breached: delivery completed {$breachSeconds}s after deadline",
                        $user['id'],
                        round(-$breachSeconds / 60, 1),
                    ]);
                } catch (\Exception $e) {
                    error_log("Failed to log SLA breach escalation: " . $e->getMessage());
                }
            }

            // Log status change
            $this->db->prepare(
                "INSERT INTO order_status_logs (order_id, status, comment, changed_by) VALUES (?, 'delivered', ?, ?)"
            )->execute([
                $id,
                $slaBreach ? 'Emergency order delivered (SLA BREACHED)' : 'Emergency order delivered within SLA',
                $user['id']
            ]);

            // Notify customer
            try {
                $customerStmt = $this->db->prepare("SELECT user_id FROM orders WHERE id = ?");
                $customerStmt->execute([$id]);
                $customerId = $customerStmt->fetchColumn();

                if ($customerId) {
                    $this->db->prepare(
                        "INSERT INTO notifications (user_id, title, message, type) VALUES (?, 'Emergency Delivery Complete', ?, 'order')"
                    )->execute([$customerId, "Your emergency order #MM-{$id} has been delivered."]);
                }
            } catch (\Exception $e) {
                error_log("Failed to send delivery notification: " . $e->getMessage());
            }

            return ResponseHandler::success([
                'order_id' => (int) $id,
                'status' => 'delivered',
                'delivered_at' => $now,
                'sla_breached' => $slaBreach,
            ], "Emergency order #{$id} delivered successfully." . ($slaBreach ? ' (SLA breached)' : ''));
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }
}
