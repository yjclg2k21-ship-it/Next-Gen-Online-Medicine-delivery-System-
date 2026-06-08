<?php
namespace App\Controllers;

use App\Core\ResponseHandler;
use App\Models\Medicine;
use App\Models\Order;
use App\Models\Prescription;
use App\Models\VendorPayout;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Notification;
use App\Middleware\AuthGuard;
use App\Middleware\RoleCheck;
use App\Services\NotificationService;
use Exception;

/**
 * Vendor Controller — Aligned with Unified Model Architecture
 */
class VendorController extends BaseController {

    private $medicineModel;
    private $orderModel;
    private $prescriptionModel;
    private $payoutModel;
    private $notificationModel;

    public function __construct() {
        $this->medicineModel = new Medicine();
        $this->orderModel = new Order();
        $this->prescriptionModel = new Prescription();
        $this->payoutModel = new VendorPayout();
        $this->notificationModel = new Notification();
    }

    /**
     * GET /vendor/dashboard
     */
    public function dashboard() {
        AuthGuard::handle();
        RoleCheck::handle('vendor');
        $user = AuthGuard::getUser();

        try {
            $stats = $this->orderModel->getVendorStats($user['id']);
            $stats['pending_orders'] = $this->orderModel->count("vendor_id = ? AND status IN ('placed','pending','confirmed')", [$user['id']]);
            $stats['unread_notifs'] = $this->notificationModel->count("user_id = ? AND is_read = 0", [$user['id']]);

            $revenueSeries = $this->orderModel->getRevenueSeries($user['id']);
            $lowStock = $this->medicineModel->getLowStock($user['id']);
            $expiring = $this->medicineModel->getExpiringSoon($user['id']);
            
            $stats['low_stock_alerts'] = $lowStock;
            $stats['expiry_alerts'] = $expiring;

            return ResponseHandler::success([
                'stats'         => $stats,
                'revenue_series'=> $revenueSeries,
                'low_stock'     => $lowStock,
                'recent_orders' => $this->orderModel->getVendorOrders($user['id'], null, 8)
            ], 'Vendor analytics synchronization complete.');
        } catch (Exception $e) {
            return ResponseHandler::error('Dashboard sync error: ' . $e->getMessage(), 500);
        }
    }

    /**
     * GET /vendor/inventory-reports
     */
    public function inventoryReports() {
        AuthGuard::handle();
        RoleCheck::handle('vendor');
        $user = AuthGuard::getUser();
        
        $lowStock = $this->medicineModel->getLowStock($user['id']);
        return ResponseHandler::success(['reports' => $lowStock], 'Inventory reports generated.');
    }

    /**
     * POST /vendor/medicines
     */
    public function storeMedicine() {
        AuthGuard::handle();
        RoleCheck::handle('vendor');
        $user = AuthGuard::getUser();
        
        // Support both JSON and FormData (multipart)
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'multipart/form-data') !== false) {
            $data = $_POST;
        } else {
            $data = $this->getPostData();
        }

        $name = $data['name'] ?? 'Untitled';
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name))) . '-' . bin2hex(random_bytes(2));

        // Handle image upload
        $imageName = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = dirname(__DIR__, 2) . '/../frontend/assets/images/medicines/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $safeName = preg_replace('/[^a-z0-9_]/', '_', strtolower($name)) . '_' . time() . '.' . $ext;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $safeName)) {
                $imageName = $safeName;
            }
        }

        $id = $this->medicineModel->create([
            'vendor_id' => $user['id'],
            'category_id' => $data['category_id'] ?? null,
            'brand_id' => $data['brand_id'] ?? null,
            'name' => $name,
            'slug' => $slug,
            'salt' => $data['salt'] ?? null,
            'price' => $data['price'] ?? 0,
            'mrp' => $data['mrp'] ?? ($data['price'] ?? 0),
            'stock' => $data['stock'] ?? 0,
            'expiry_date' => $data['expiry_date'] ?? null,
            'description' => $data['description'] ?? null,
            'image' => $imageName,
            'requires_prescription' => !empty($data['requires_prescription']) ? 1 : 0,
            'approval_status' => 'pending'
        ]);

        return ResponseHandler::success(['medicine_id' => $id], 'Medicine registered successfully.', 201);
    }

    /**
     * PUT /vendor/medicines/{id}
     */
    public function updateMedicine($id) {
        AuthGuard::handle();
        RoleCheck::handle('vendor');
        $user = AuthGuard::getUser();
        $data = $this->getPostData();

        $medicine = $this->medicineModel->findById($id);
        if (!$medicine || $medicine['vendor_id'] != $user['id']) {
            return ResponseHandler::error('Resource not found or access denied.', 404);
        }

        $this->medicineModel->update($id, [
            'name' => $data['name'] ?? $medicine['name'],
            'price' => $data['price'] ?? $medicine['price'],
            'stock' => $data['stock'] ?? $medicine['stock'],
            'description' => $data['description'] ?? $medicine['description']
        ]);

        return ResponseHandler::success([], 'Medicine updated successfully.');
    }

    /**
     * DELETE /vendor/medicines/{id}
     */
    public function deleteMedicine($id) {
        AuthGuard::handle();
        RoleCheck::handle('vendor');
        $user = AuthGuard::getUser();

        $medicine = $this->medicineModel->findById($id);
        if (!$medicine || $medicine['vendor_id'] != $user['id']) {
            return ResponseHandler::error('Resource not found or access denied.', 404);
        }

        $this->medicineModel->delete($id);
        return ResponseHandler::success([], "Medicine #$id removed from catalog.");
    }

    /**
     * GET /vendor/orders
     */
    public function orders() {
        AuthGuard::handle();
        RoleCheck::handle('vendor');
        $user = AuthGuard::getUser();

        $orders = $this->orderModel->getVendorOrders($user['id'], null, 50);
        
        // Add emergency priority flags and prepare deadline
        foreach ($orders as &$order) {
            $isEmergency = (isset($order['is_emergency']) && $order['is_emergency'] == 1) 
                        || (isset($order['delivery_method_id']) && $order['delivery_method_id'] == 3);
            
            if ($isEmergency) {
                $order['priority'] = 'CRITICAL';
                // Prepare deadline = order created_at + 5 minutes
                $order['prepare_deadline'] = date('Y-m-d H:i:s', strtotime($order['created_at']) + (5 * 60));
            } else {
                $order['priority'] = 'STANDARD';
                $order['prepare_deadline'] = null;
            }
        }
        unset($order);

        return ResponseHandler::success(['orders' => $orders], 'Vendor orders retrieved.');
    }

    /**
     * GET /vendor/medicines
     */
    public function listMedicines() {
        AuthGuard::handle();
        RoleCheck::handle('vendor');
        $user = AuthGuard::getUser();

        $medicines = $this->medicineModel->getByVendor($user['id']);
        return ResponseHandler::success(['medicines' => $medicines], 'Vendor medicines retrieved.');
    }


    /**
     * GET /vendor/orders/{id}
     */
    public function orderDetail($id) {
        AuthGuard::handle();
        RoleCheck::handle('vendor');
        $user = AuthGuard::getUser();

        $order = $this->orderModel->getWithItems($id);
        if (!$order || $order['vendor_id'] != $user['id']) {
            return ResponseHandler::error('Order not found.', 404);
        }

        return ResponseHandler::success(['order' => $order, 'items' => $order['items']], 'Order detail retrieved.');
    }

    /**
     * PUT /vendor/orders/{id}/status
     */
    public function updateOrderStatus($id) {
        AuthGuard::handle();
        RoleCheck::handle('vendor');
        $user = AuthGuard::getUser();
        $data = $this->getPostData();

        $order = $this->orderModel->findById($id);
        if (!$order || $order['vendor_id'] != $user['id']) {
            return ResponseHandler::forbidden();
        }

        $status = $data['status'] ?? 'confirmed';
        // Use the new setStatus method which allows custom statuses
        $this->orderModel->setStatus($id, $status);

        return ResponseHandler::success([], "Order #$id status updated to $status.");
    }

    /**
     * GET /vendor/prescriptions/pending
     * Returns all prescriptions for the vendor (all statuses - frontend filters)
     */
    public function prescriptionReview() {
        AuthGuard::handle();
        RoleCheck::handle('vendor');
        $user = AuthGuard::getUser();

        try {
            // Fetch all prescriptions linked to this vendor's medicines or orders
            $prescriptions = $this->prescriptionModel->getAllForVendor($user['id']);

            foreach ($prescriptions as &$p) {
                try {
                    $p['medicines'] = $this->prescriptionModel->getMedicines($p['id']);
                } catch (\Exception $e) {
                    $p['medicines'] = [];
                }
            }
            return ResponseHandler::success(['prescriptions' => $prescriptions]);
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * POST /vendor/prescriptions/{id}/review
     */
    public function reviewPrescription($id) {
        AuthGuard::handle();
        RoleCheck::handle('vendor');
        $user    = AuthGuard::getUser();
        $data    = $this->getPostData();
        $status  = $data['status']  ?? 'rejected';
        $remarks = $data['remarks'] ?? '';

        $this->prescriptionModel->update($id, [
            'status' => $status,
            'reviewed_by' => $user['id'],
            'reviewed_at' => date('Y-m-d H:i:s'),
            'remarks' => $remarks
        ]);

        $prescription = $this->prescriptionModel->findById($id);
        if ($prescription) {
            (new NotificationService())->send($prescription['user_id'], 'Clinical Review Complete', "Your prescription node #$id was $status by the pharmacy.");
        }

        return ResponseHandler::success([], 'Clinical assessment recorded. Patient notified.');
    }

    /**
     * POST /vendor/payout/request
     */
    public function requestPayout() {
        AuthGuard::handle();
        RoleCheck::handle('vendor');
        $user   = AuthGuard::getUser();
        $data   = $this->getPostData();
        $amount = (float)($data['amount'] ?? 0);

        if ($amount <= 0) {
            return ResponseHandler::badRequest('Invalid payout amount.');
        }

        try {
            // Check pending balance before allowing payout
            $stats = $this->orderModel->getVendorStats($user['id']);
            $totalRevenue = (float)($stats['gross_revenue'] ?? 0);
            
            // Calculate already paid out amount
            $db = \App\Core\Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT COALESCE(SUM(amount), 0) as total_paid FROM vendor_payouts WHERE vendor_id = ? AND status IN ('pending','completed','approved')");
            $stmt->execute([$user['id']]);
            $paidOut = (float)$stmt->fetch(\PDO::FETCH_ASSOC)['total_paid'];
            
            $pendingBalance = $totalRevenue - $paidOut;

            if ($pendingBalance <= 0) {
                return ResponseHandler::badRequest('No pending balance available for withdrawal.');
            }

            if ($amount > $pendingBalance) {
                return ResponseHandler::badRequest("Amount exceeds your pending balance of ₹" . number_format($pendingBalance, 2) . ".");
            }

            $id = $this->payoutModel->create([
                'vendor_id' => $user['id'],
                'amount' => $amount,
                'status' => 'pending'
            ]);

            return ResponseHandler::success(['id' => $id], 'Payout request submitted. Pending settlement.');
        } catch (\Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * GET /vendor/analytics/customers
     */
    public function customers() {
        AuthGuard::handle();
        RoleCheck::handle('vendor');
        $user = AuthGuard::getUser();

        $customers = $this->orderModel->getVendorCustomers($user['id']);
        return ResponseHandler::success(['patrons' => $customers], 'Patron data retrieved.');
    }

    /**
     * GET /vendor/analytics/delivery-mix
     */
    public function deliveryMix() {
        AuthGuard::handle();
        RoleCheck::handle('vendor');
        $user = AuthGuard::getUser();

        $mix = $this->orderModel->getDeliveryMix($user['id']);
        return ResponseHandler::success(['delivery_mix' => $mix]);
    }

    /**
     * GET /vendor/earnings
     */
    public function earnings() {
        AuthGuard::handle();
        RoleCheck::handle('vendor');
        $user = AuthGuard::getUser();

        try {
            $stats = $this->orderModel->getVendorStats($user['id']);
            $payouts = $this->payoutModel->getByVendor($user['id']);
            
            return ResponseHandler::success([
                'total_revenue' => $stats['gross_revenue'] ?? 0,
                'total_orders' => $stats['total_orders'] ?? 0,
                'payouts' => $payouts
            ], 'Earnings data retrieved.');
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * GET /vendor/catalog
     */
    public function catalogConfig() {
        AuthGuard::handle();
        RoleCheck::handle('vendor');
        $user = AuthGuard::getUser();

        $items = $this->medicineModel->getVendorCatalog($user['id']);

        return ResponseHandler::success([
            'catalog' => $items,
            'stats'   => [
                'total_skus'  => count($items),
                'hidden_items'=> 0,
                'low_stock'   => count(array_filter($items, fn($i) => $i['stock'] < 50))
            ]
        ], 'Pharmacy catalog configuration metadata retrieved.');
    }

    /**
     * GET /vendor/taxonomy
     */
    public function taxonomy() {
        AuthGuard::handle();
        RoleCheck::handle('vendor');

        $categories = (new Category())->findAll();
        $brands     = (new Brand())->findAll();
        return ResponseHandler::success(['categories' => $categories, 'brands' => $brands]);
    }

    /**
     * GET /vendor/analytics/reports
     */
    public function analyticalReports() {
        AuthGuard::handle();
        RoleCheck::handle('vendor');
        $user = AuthGuard::getUser();

        try {
            $stats = $this->orderModel->getVendorStats($user['id']);
            $salesTrendRaw = $this->orderModel->getRevenueSeries($user['id']);
            $topMedicinesRaw = $this->medicineModel->getTopSelling($user['id']);
            $deliveryMixRaw = $this->orderModel->getDeliveryMix($user['id']);
            $catBreakdown = $this->orderModel->getRevenueByCategory($user['id']);

            // Formatting stats
            $revenue = $stats['gross_revenue'] ?? 0;
            $orders = $stats['total_orders'] ?? 0;
            $customers = $stats['total_customers'] ?? 0;
            $aov = $orders > 0 ? round($revenue / $orders, 0) : 0;

            // Prepare trend for UI (last 7 available periods max for simple view)
            $trendValues = [];
            $trendLabels = [];
            $slicedTrend = array_slice($salesTrendRaw, 0, 7);
            foreach (array_reverse($slicedTrend) as $t) {
                $trendLabels[] = date('M Y', strtotime($t['month'].'-01'));
                $trendValues[] = (float)$t['revenue'];
            }

            // Top medicines
            $topProducts = [];
            foreach ($topMedicinesRaw as $tm) {
                $topProducts[] = [
                    'name' => $tm['name'],
                    'sales' => $tm['sales'],
                    'revenue' => '₹' . number_format((float)($tm['revenue'] ?? 0))
                ];
            }

            // Delivery Mix
            $totalMix = array_sum(array_column($deliveryMixRaw, 'count'));
            $emergency = 0; $standard = 0; $free = 0;
            if ($totalMix > 0) {
                foreach ($deliveryMixRaw as $dm) {
                    $pct = round(($dm['count'] / $totalMix) * 100);
                    if (stripos($dm['name'], 'Emergency') !== false) $emergency = $pct;
                    elseif (stripos($dm['name'], 'Free') !== false) $free = $pct;
                    else $standard += $pct; // Group rest into standard
                }
            }

            $reports = [
                'kpi' => [
                    'revenue' => '₹' . number_format($revenue),
                    'orders' => $orders,
                    'customers' => $customers,
                    'aov' => '₹' . number_format($aov)
                ],
                'delivery_mix' => [
                    'emergency' => $emergency,
                    'standard' => $standard ?: ($totalMix ? 0 : 100), // default to standard if empty
                    'free' => $free
                ],
                'trend' => empty($trendValues) ? [0] : $trendValues,
                'labels' => empty($trendLabels) ? ['N/A'] : $trendLabels,
                'top_products' => $topProducts,
                'cat_breakdown' => $catBreakdown
            ];
            
            return ResponseHandler::success($reports, 'Analytical reports generated.');
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * GET /vendor/customer/{id}
     */
    public function customerDetail($id) {
        AuthGuard::handle();
        RoleCheck::handle('vendor');
        $user = AuthGuard::getUser();

        try {
            $customer = (new \App\Models\User())->findById($id);
            if (!$customer) return ResponseHandler::notFound('Customer not found.');

            $orders = $this->orderModel->where("user_id = ? AND vendor_id = ?", [$id, $user['id']]);
            return ResponseHandler::success(['customer' => $customer, 'orders' => $orders]);
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * POST /vendor/catalog/toggle/{id}
     */
    public function toggleVisibility($id) {
        AuthGuard::handle();
        RoleCheck::handle('vendor');
        $user = AuthGuard::getUser();

        try {
            $medicine = $this->medicineModel->findById($id);
            if (!$medicine || $medicine['vendor_id'] != $user['id']) return ResponseHandler::forbidden();

            $data = $this->getPostData();
            $newStatus = isset($data['visible']) ? ($data['visible'] ? 1 : 0) : ($medicine['status'] ? 0 : 1);
            $this->medicineModel->update($id, ['status' => $newStatus]);
            return ResponseHandler::success(['status' => $newStatus], 'Visibility toggled.');
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * GET /vendor/profile
     */
    public function profile() {
        AuthGuard::handle();
        RoleCheck::handle('vendor');
        $user = AuthGuard::getUser();

        try {
            $db = \App\Core\Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM vendor_profiles WHERE user_id = ?");
            $stmt->execute([$user['id']]);
            $profile = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$profile) {
                // If it doesn't exist, create an empty one for the user
                $db->prepare("INSERT INTO vendor_profiles (user_id) VALUES (?)")->execute([$user['id']]);
                $stmt->execute([$user['id']]);
                $profile = $stmt->fetch(\PDO::FETCH_ASSOC);
            }

            return ResponseHandler::success(['profile' => $profile]);
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * POST /vendor/profile/update
     */
    public function updateProfile() {
        AuthGuard::handle();
        RoleCheck::handle('vendor');
        $user = AuthGuard::getUser();
        $data = $this->getPostData();

        try {
            $db = \App\Core\Database::getInstance()->getConnection();
            
            // Check if profile exists
            $stmt = $db->prepare("SELECT id FROM vendor_profiles WHERE user_id = ?");
            $stmt->execute([$user['id']]);
            $exists = $stmt->fetch();

            $profileUpdate = [];
            $fields = ['pharmacy_name', 'owner_name', 'license_number', 'pharmacist_reg_number', 'pan_card_number', 'aadhaar_number', 'gst_number', 'address', 'opening_time', 'closing_time', 'sunday_open', 'sunday_opening_time', 'sunday_closing_time', 'bank_name', 'account_number', 'ifsc_code', 'latitude', 'longitude', 'delivery_radius_km'];
            $timeFields = ['opening_time', 'closing_time', 'sunday_opening_time', 'sunday_closing_time'];
            foreach ($fields as $f) {
                if (isset($data[$f])) {
                    if (in_array($f, $timeFields) && empty($data[$f])) {
                        // Empty time string → NULL to avoid MySQL TIME column error
                        $profileUpdate[$f] = null;
                    } elseif ($f === 'sunday_open') {
                        // Cast to integer (0 or 1)
                        $profileUpdate[$f] = (int) $data[$f];
                    } else {
                        $profileUpdate[$f] = $data[$f];
                    }
                }
            }

            if (!empty($profileUpdate)) {
                if ($exists) {
                    $set = implode(' = ?, ', array_keys($profileUpdate)) . ' = ?';
                    $stmt = $db->prepare("UPDATE vendor_profiles SET {$set} WHERE user_id = ?");
                    $stmt->execute([...array_values($profileUpdate), $user['id']]);
                } else {
                    $profileUpdate['user_id'] = $user['id'];
                    $cols = implode(', ', array_keys($profileUpdate));
                    $placeholders = implode(', ', array_fill(0, count($profileUpdate), '?'));
                    $stmt = $db->prepare("INSERT INTO vendor_profiles ({$cols}) VALUES ({$placeholders})");
                    $stmt->execute(array_values($profileUpdate));
                }
            }

            // Also sync user name in base users table if pharmacy_name is updated
            if (!empty($data['pharmacy_name'])) {
                $db->prepare("UPDATE users SET name = ? WHERE id = ?")->execute([$data['pharmacy_name'], $user['id']]);
            }

            return ResponseHandler::success([], 'Vendor profile updated successfully.');
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }
}
