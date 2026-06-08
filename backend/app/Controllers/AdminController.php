<?php
namespace App\Controllers;

use App\Core\ResponseHandler;
use App\Models\User;
use App\Models\Order;
use App\Models\Medicine;
use App\Models\Prescription;
use App\Models\AuditLog;
use App\Services\SLATracker;
use App\Services\InventoryManager;
use App\Services\PriorityEngine;
use App\Services\NotificationService;
use App\Middleware\AuthGuard;
use App\Middleware\RoleCheck;
use App\Models\Category;
use Exception;

/**
 * Admin Controller — Aligned with Unified Model Architecture
 */
class AdminController extends BaseController {

    private $userModel;
    private $orderModel;
    private $medicineModel;
    private $prescriptionModel;
    private $auditModel;
    private $categoryModel;

    public function __construct() {
        $this->userModel = new User();
        $this->orderModel = new Order();
        $this->medicineModel = new Medicine();
        $this->prescriptionModel = new Prescription();
        $this->auditModel = new AuditLog();
        $this->categoryModel = new Category();
    }

    /**
     * GET /admin/dashboard
     */
    public function dashboard() {
        AuthGuard::handle();
        RoleCheck::handle('admin');

        $stats = [
            'total_users'      => $this->userModel->count(),
            'total_vendors'    => $this->userModel->count("role = 'vendor'"),
            'total_orders'     => $this->orderModel->count(),
            'total_revenue'    => $this->orderModel->sum('grand_total', "status = 'delivered'"),
            'total_medicines'  => $this->medicineModel->count(),
            'low_stock_alerts' => $this->medicineModel->count("stock <= 15"),
            'pending_reviews'  => $this->prescriptionModel->count("status = 'pending'")
        ];

        $recentOrders = $this->orderModel->findRecent(10);
        $systemLogs   = $this->auditModel->findRecent(5);
        
        // Fetch unassigned orders for quick dispatch
        $db = \App\Core\Database::getInstance()->getConnection();
        $unassigned = $db->query("SELECT o.id, u.name as customer, o.grand_total as amount, o.is_emergency 
                                  FROM orders o 
                                  JOIN users u ON o.user_id = u.id 
                                  WHERE o.status = 'confirmed' AND o.delivery_partner_id IS NULL 
                                  LIMIT 5")->fetchAll();

        return ResponseHandler::success([
            'stats'         => $stats,
            'recent_orders' => $recentOrders,
            'system_logs'   => $systemLogs,
            'unassigned'    => $unassigned,
            'timestamp'     => date('Y-m-d H:i:s')
        ], 'Global administrative dossier retrieved.');
    }

    /**
     * POST /admin/medicine/{id}/approve
     */
    public function approveMedicine($id) {
        AuthGuard::handle();
        RoleCheck::handle('admin');
        $admin = AuthGuard::getUser();

        $data   = $this->getPostData();
        $status = $data['status'] ?? 'approved';
        
        // Final Sync: Support both 'vetting_note' (new UI) and 'reason' (legacy)
        $reason = $data['vetting_note'] ?? ($data['reason'] ?? 'Clinical standards verified.');

        $this->medicineModel->update($id, [
            'approval_status' => $status,
            'vetting_reason' => $reason,
            'vetted_at' => date('Y-m-d H:i:s'),
            'vetted_by' => $admin['id']
        ]);

        $this->auditModel->log($admin['id'], 'MEDICINE_VETTED', 'Medicine', $id, ['status' => $status]);
        return ResponseHandler::success([], "Medicine node $id status transitioned to $status.");
    }

    /**
     * GET /admin/reports/revenue
     */
    public function reports() {
        AuthGuard::handle();
        RoleCheck::handle('admin');

        // Complex analytical queries kept as direct DB for performance/logic parity
        $db = \App\Core\Database::getInstance()->getConnection();
        
        $revenue = $db->query(
            "SELECT DATE(created_at) as date, SUM(grand_total) as amount FROM orders GROUP BY date ORDER BY date DESC LIMIT 30"
        )->fetchAll();

        $growth = $db->query(
            "SELECT DATE(created_at) as date, COUNT(*) as count FROM users GROUP BY date ORDER BY date DESC LIMIT 30"
        )->fetchAll();

        $topProducts = $db->query(
            "SELECT m.name, SUM(oi.quantity) as sales FROM order_items oi JOIN medicines m ON oi.medicine_id = m.id GROUP BY m.id ORDER BY sales DESC LIMIT 5"
        )->fetchAll();

        // Calculate real-time KPIs
        $retention = $db->query("SELECT (COUNT(DISTINCT user_id) * 100.0 / (SELECT NULLIF(COUNT(DISTINCT user_id), 0) FROM orders)) as rate FROM orders GROUP BY user_id HAVING COUNT(*) > 1")->fetch();
        $aov = $db->query("SELECT AVG(grand_total) as avg FROM orders WHERE status = 'delivered'")->fetch();
        $yield = $db->query("SELECT (COUNT(CASE WHEN status = 'delivered' THEN 1 END) * 100.0 / NULLIF(COUNT(CASE WHEN status != 'cancelled' THEN 1 END), 0)) as yield FROM orders")->fetch();

        return ResponseHandler::success([
            'revenue_series' => $revenue,
            'user_growth'    => $growth,
            'top_products'   => $topProducts,
            'kpis' => [
                'retention_rate'    => ($retention ? round((float)$retention['rate'], 1) : 0) . '%',
                'avg_order_value'   => '₹' . round((float)($aov['avg'] ?? 0), 2),
                'fulfillment_yield' => ($yield ? round((float)$yield['yield'], 1) : 0) . '%'
            ]
        ], 'Reporting series synchronized.');
    }

    /**
     * GET /admin/pulse
     */
    public function pulse() {
        AuthGuard::handle();
        RoleCheck::handle('admin');

        $activeUsers = $this->auditModel->count("created_at >= DATE_SUB(NOW(), INTERVAL 15 MINUTE)");
        $orderVelocity = $this->orderModel->count("created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)");
        
        // Use real system metrics where available
        $load = function_exists('sys_getloadavg') ? sys_getloadavg() : [0, 0, 0];
        $cpuLoad = round($load[0] * 10, 1); // Normalized for display
        $memUsage = round(memory_get_usage(true) / 1024 / 1024, 1);

        return ResponseHandler::success(['pulse' => [
            'cpu'           => $cpuLoad . '%',
            'mem'           => $memUsage . ' MB',
            'latency'       => round((microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]) * 1000, 2) . 'ms',
            'active_nodes'  => $activeUsers,
            'api_uptime'    => '100.00%',
            'order_velocity'=> $orderVelocity . ' orders/hr'
        ]], 'Operational telemetry synchronized.');
    }

    /**
     * POST /admin/pulse/trigger
     */
    public function triggerPulse() {
        AuthGuard::handle();
        RoleCheck::handle('admin');
        $admin = AuthGuard::getUser();

        try {
            $results = [
                'sla_breaches'             => (new SLATracker())->checkBreaches(),
                'inventory_reconciliation' => (new InventoryManager())->reconcileIntegrity(),
                'timestamp'                => date('Y-m-d H:i:s')
            ];
            $this->auditModel->log($admin['id'], 'GLOBAL_PULSE_TRIGGERED', 'System', 0, $results);
            return ResponseHandler::success($results, 'Global systems synchronized. Pulse propagation complete.');
        } catch (Exception $e) {
            return ResponseHandler::error('Pulse synchronization failure: ' . $e->getMessage(), 500);
        }
    }

    /**
     * GET /admin/logs/activity
     */
    public function activityLogs() {
        AuthGuard::handle();
        RoleCheck::handle('admin');

        $logs = $this->auditModel->getLatest(100);

        return ResponseHandler::success([
            'logs'  => $logs,
            'stats' => ['logs_today' => count($logs), 'alerts' => 0, 'retention' => '100%']
        ], 'Forensic audit trails retrieved.');
    }

    /**
     * GET /admin/users
     */
    public function usersList() {
        AuthGuard::handle();
        RoleCheck::handle('admin');

        $users = $this->userModel->where("role != ?", ["admin"]);

        return ResponseHandler::success([
            'users' => $users,
            'stats' => [
                'total_users' => count($users),
                'vendors'     => count(array_filter($users, fn($u) => $u['role'] === 'vendor')),
                'delivery'    => count(array_filter($users, fn($u) => $u['role'] === 'delivery'))
            ]
        ], 'Global entity directory synchronized.');
    }

    /**
     * GET /admin/users/{id}
     */
    public function userDetails($id) {
        AuthGuard::handle();
        RoleCheck::handle('admin');

        $db = \App\Core\Database::getInstance()->getConnection();
        
        // Base user info
        $stmt = $db->prepare("SELECT id, name, email, phone, role, status, created_at, wallet_balance FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user) return ResponseHandler::error('Node not found in registry.', 404);

        // Role-specific profile
        $profile = null;
        if ($user['role'] === 'vendor') {
            $pStmt = $db->prepare("SELECT * FROM vendor_profiles WHERE user_id = ?");
            $pStmt->execute([$id]);
            $profile = $pStmt->fetch(\PDO::FETCH_ASSOC);
        } else if ($user['role'] === 'delivery') {
            $pStmt = $db->prepare("SELECT * FROM delivery_profiles WHERE user_id = ?");
            $pStmt->execute([$id]);
            $profile = $pStmt->fetch(\PDO::FETCH_ASSOC);
        }

        // Recent orders (role-aware)
        $orders = [];
        try {
            if ($user['role'] === 'vendor') {
                $orderStmt = $db->prepare("SELECT id, status, grand_total, created_at FROM orders WHERE vendor_id = ? ORDER BY created_at DESC LIMIT 10");
            } elseif ($user['role'] === 'delivery') {
                $orderStmt = $db->prepare("SELECT id, status, grand_total, created_at FROM orders WHERE delivery_partner_id = ? ORDER BY created_at DESC LIMIT 10");
            } else {
                $orderStmt = $db->prepare("SELECT id, status, grand_total, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
            }
            $orderStmt->execute([$id]);
            $orders = $orderStmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        // Addresses
        $addresses = [];
        try {
            $addrStmt = $db->prepare("SELECT id, label, address_line, city, pincode, is_default FROM addresses WHERE user_id = ? ORDER BY is_default DESC");
            $addrStmt->execute([$id]);
            $addresses = $addrStmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        // Recent activity (last 15 actions)
        $activity = [];
        try {
            $actStmt = $db->prepare("SELECT action, entity_type, entity_id, created_at FROM audit_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT 15");
            $actStmt->execute([$id]);
            $activity = $actStmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        return ResponseHandler::success([
            'user' => $user,
            'profile' => $profile,
            'orders' => $orders,
            'addresses' => $addresses,
            'activity' => $activity
        ], 'Entity diagnostics retrieved.');
    }

    /**
     * GET /admin/vendors
     */
    public function vendors() {
        AuthGuard::handle();
        RoleCheck::handle('admin');

        $db = \App\Core\Database::getInstance()->getConnection();
        $stmt = $db->query("
            SELECT u.id, u.name, u.email, u.phone, u.status,
                   vp.pharmacy_name, vp.license_number, vp.address,
                   (SELECT COUNT(*) FROM orders WHERE vendor_id = u.id) as orders_count
            FROM users u
            LEFT JOIN vendor_profiles vp ON u.id = vp.user_id
            WHERE u.role = 'vendor'
        ");
        
        $vendors = $stmt->fetchAll();

        foreach ($vendors as &$v) {
            $v['name']    = $v['pharmacy_name'] ?? $v['name']; // Use pharmacy name as primary
            $v['address'] = $v['address'] ?? 'Not Provided';
            $v['license'] = $v['license_number'] ?? 'N/A';
            $v['orders']  = (int)$v['orders_count'];
        }

        return ResponseHandler::success([
            'vendors' => array_values($vendors),
            'stats'   => [
                'active'    => count(array_filter($vendors, fn($v) => $v['status'] === 'active')),
                'pending'   => count(array_filter($vendors, fn($v) => $v['status'] === 'pending')),
                'suspended' => count(array_filter($vendors, fn($v) => $v['status'] === 'suspended'))
            ]
        ], 'Pharmacy vendor network synchronized.');
    }

    /**
     * GET /admin/order/pulse
     */
    public function orderPulse() {
        AuthGuard::handle();
        RoleCheck::handle('admin');

        $db = \App\Core\Database::getInstance()->getConnection();
        $stmt = $db->query("
            SELECT o.id, o.status, o.created_at, dm.name as delivery_type,
                   u_v.name as pharmacy, u_c.name as customer,
                   COALESCE(u_d.name, 'Unassigned') as agent,
                   TIMESTAMPDIFF(MINUTE, o.created_at, NOW()) as minutes_ago
            FROM orders o
            JOIN users u_c ON o.user_id = u_c.id
            JOIN users u_v ON o.vendor_id = u_v.id
            JOIN delivery_methods dm ON o.delivery_method_id = dm.id
            LEFT JOIN users u_d ON o.delivery_partner_id = u_d.id
            ORDER BY o.created_at DESC LIMIT 50
        ");

        $orders = $stmt->fetchAll();
        foreach ($orders as &$o) {
            $o['formatted_time'] = date('d M, h:i A', strtotime($o['created_at']));
            $o['time']     = $o['minutes_ago'] . ' min ago';
            $o['incident'] = $o['minutes_ago'] > 60 && !in_array($o['status'], ['delivered', 'cancelled']);
            $o['incidentType'] = $o['incident'] ? 'Delayed' : null;
        }

        return ResponseHandler::success([
            'recent_orders' => $orders,
            'stats' => [
                'active_today' => count(array_filter($orders, fn($o) => ($o['status'] !== 'delivered' && $o['status'] !== 'cancelled'))),
                'in_transit'   => count(array_filter($orders, fn($o) => $o['status'] === 'dispatched')),
                'delivered'    => count(array_filter($orders, fn($o) => $o['status'] === 'delivered')),
                'incidents'    => count(array_filter($orders, fn($o) => ($o['minutes_ago'] > 60 && !in_array($o['status'], ['delivered', 'cancelled']))))
            ]
        ], 'Logistics pulse synchronized.');
    }

    /**
     * POST /admin/orders/{id}/assign
     */
    public function assignOrder($id) {
        AuthGuard::handle();
        RoleCheck::handle('admin');
        $admin = AuthGuard::getUser();

        $data      = $this->getPostData();
        $partnerId = $data['delivery_partner_id'] ?? null;

        if (!$partnerId) return ResponseHandler::error('Agent node ID required.');

        $this->orderModel->update($id, [
            'delivery_partner_id' => $partnerId,
            'assigned_at' => date('Y-m-d H:i:s'),
            'status' => 'processing'
        ]);

        $this->auditModel->log($admin['id'], 'ORDER_ASSIGNED', 'Order', $id, ['partner_id' => $partnerId]);
        return ResponseHandler::success([], "Logistics node $partnerId assigned to dispatch $id.");
    }

    /**
     * POST /admin/users/{id}/block
     */
    public function blockUser($id) {
        AuthGuard::handle();
        RoleCheck::handle('admin');
        $this->userModel->update($id, ['status' => 'blocked']);
        return ResponseHandler::success([], "User #$id has been blocked.");
    }

    /**
     * POST /admin/users/{id}/unblock
     */
    public function unblockUser($id) {
        AuthGuard::handle();
        RoleCheck::handle('admin');
        $this->userModel->update($id, ['status' => 'active']);
        return ResponseHandler::success([], "User #$id has been unblocked.");
    }

    /**
     * POST /admin/vendors/{id}/approve
     */
    public function approveVendor($id) {
        AuthGuard::handle();
        RoleCheck::handle('admin');
        $this->userModel->update($id, ['status' => 'active']);
        return ResponseHandler::success([], "Vendor #$id has been approved.");
    }

    /**
     * POST /admin/vendors/{id}/suspend
     */
    public function suspendVendor($id) {
        AuthGuard::handle();
        RoleCheck::handle('admin');
        $this->userModel->update($id, ['status' => 'suspended']);
        return ResponseHandler::success([], "Vendor #$id has been suspended.");
    }

    /**
     * PUT /admin/vendors/{id}
     * DISABLED: Admin cannot edit vendor profiles directly.
     * Vendors must update their own profiles via /vendor/profile/update
     */
    public function updateVendor($id) {
        AuthGuard::handle();
        RoleCheck::handle('admin');
        
        return ResponseHandler::error('Admin cannot edit vendor profiles directly. Vendors must update their own information through their dashboard.', 403);
    }

    /**
     * GET /admin/medicine/pending
     */
    public function pendingMedicines() {
        AuthGuard::handle();
        RoleCheck::handle('admin');
        
        $db = \App\Core\Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT m.id, m.name, m.price, m.stock, u.name as vendor_name, m.created_at FROM medicines m JOIN users u ON m.vendor_id = u.id WHERE m.approval_status = 'pending' ORDER BY m.created_at DESC");
        
        return ResponseHandler::success(['medicines' => $stmt->fetchAll()], 'Pending medicine approvals retrieved.');
    }

    /**
     * GET /admin/medicines
     */
    public function medicines() {
        AuthGuard::handle();
        RoleCheck::handle('admin');
        
        $db = \App\Core\Database::getInstance()->getConnection();
        $stmt = $db->query("
            SELECT m.*, u.name as vendor_name 
            FROM medicines m 
            JOIN users u ON m.vendor_id = u.id 
            ORDER BY m.created_at DESC
        ");
        
        $medicines = $stmt->fetchAll();
        return ResponseHandler::success([
            'medicines' => $medicines,
            'stats' => [
                'total' => count($medicines),
                'approved' => count(array_filter($medicines, fn($m) => $m['approval_status'] === 'approved')),
                'pending' => count(array_filter($medicines, fn($m) => $m['approval_status'] === 'pending')),
                'out_of_stock' => count(array_filter($medicines, fn($m) => $m['stock'] <= 0))
            ]
        ], 'Global medicine inventory synchronized.');
    }

    /**
     * GET /admin/approval-stats
     */
    public function approvalStats() {
        AuthGuard::handle();
        RoleCheck::handle('admin');
        
        $db = \App\Core\Database::getInstance()->getConnection();
        
        $pendingMeds = $db->query("SELECT COUNT(*) FROM medicines WHERE approval_status = 'pending'")->fetchColumn();
        $pendingVendors = $db->query("SELECT COUNT(*) FROM users WHERE role = 'vendor' AND status = 'pending'")->fetchColumn();
        $approvedToday = $db->query("SELECT COUNT(*) FROM medicines WHERE approval_status = 'approved' AND DATE(updated_at) = CURDATE()")->fetchColumn();
        
        return ResponseHandler::success([
            'pending_medicines' => (int)$pendingMeds,
            'pending_vendors'   => (int)$pendingVendors,
            'approved_today'    => (int)$approvedToday
        ]);
    }

    /**
     * GET /admin/financials
     */
    public function financials() {
        AuthGuard::handle();
        RoleCheck::handle('admin');
        
        $db = \App\Core\Database::getInstance()->getConnection();
        try {
            // 1. Total Platform Revenue (Delivered Orders)
            $revStmt = $db->query("SELECT SUM(grand_total) FROM orders WHERE status = 'delivered'");
            $revenue = (float)$revStmt->fetchColumn() ?: 0.00;
            
            // 2. Net Commission (15% platform fee)
            $commStmt = $db->query("SELECT SUM(commission_amount) FROM vendor_commissions WHERE status = 'settled'");
            $commission = (float)$commStmt->fetchColumn() ?: ($revenue * 0.15); 
            
            // 3. Processed Payouts
            $payStmt = $db->query("SELECT SUM(amount) FROM vendor_payouts WHERE status = 'processed'");
            $payouts = (float)$payStmt->fetchColumn() ?: 0.00;
            
            // 4. Recent Transactions (Commissions)
            $transStmt = $db->query("
                SELECT vc.*, u.name as vendor_name, o.grand_total as order_amount 
                FROM vendor_commissions vc 
                JOIN users u ON vc.vendor_id = u.id 
                JOIN orders o ON vc.order_id = o.id 
                ORDER BY vc.created_at DESC LIMIT 20
            ");
            $transactions = $transStmt->fetchAll();

            return ResponseHandler::success([
                'revenue' => $revenue,
                'commission' => $commission,
                'payouts' => $payouts,
                'transactions' => $transactions
            ], 'Financial telemetry synchronized with nodal ledger.');
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * POST /admin/users/create
     */
    public function createUser() {
        AuthGuard::handle();
        RoleCheck::handle('admin');
        $data = $this->getPostData();

        try {
            $id = $this->userModel->create([
                'name' => $data['name'] ?? 'New User',
                'email' => $data['email'] ?? '',
                'password' => password_hash($data['password'] ?? 'Mediflow123!', PASSWORD_DEFAULT),
                'role' => $data['role'] ?? 'user',
                'status' => 'active'
            ]);
            return ResponseHandler::success(['user_id' => $id], 'Administrative user creation successful.');
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * GET /admin/categories
     */
    public function categories() {
        AuthGuard::handle();
        RoleCheck::handle('admin');

        $db = \App\Core\Database::getInstance()->getConnection();
        $stmt = $db->query("
            SELECT c.*, 
                   (SELECT COUNT(*) FROM medicines WHERE category_id = c.id) as medicine_count,
                   (SELECT COUNT(*) FROM categories WHERE parent_id = c.id) as sub_count
            FROM categories c
            ORDER BY c.name ASC
        ");

        $categories = $stmt->fetchAll();

        return ResponseHandler::success([
            'categories' => $categories
        ], 'Global taxonomy synchronized with inventory nodes.');
    }




    /**
     * GET /admin/agents
     */
    public function agents() {
        AuthGuard::handle();
        RoleCheck::handle(['admin', 'delivery']);
        
        $db = \App\Core\Database::getInstance()->getConnection();
        $stmt = $db->query("
            SELECT u.id, u.name, u.email, u.phone, u.status,
                   dp.vehicle_number, dp.license_number, dp.vehicle_type,
                   (SELECT COUNT(*) FROM orders WHERE delivery_partner_id = u.id) as deliveries_count,
                   (SELECT 5.0) as avg_rating,
                   (SELECT COUNT(*) FROM orders WHERE delivery_partner_id = u.id AND status = 'delivered' AND DATE(updated_at) = CURDATE()) as deliveries_today
            FROM users u
            LEFT JOIN delivery_profiles dp ON u.id = dp.user_id
            WHERE u.role = 'delivery' AND u.deleted_at IS NULL
        ");
        
        $agents = $stmt->fetchAll();
        $fleet_deliveries_today = 0;
        $rating_sum = 0;
        $rating_count = 0;
        
        foreach ($agents as &$a) {
            $a['vehicle'] = $a['vehicle_number'] ? ($a['vehicle_type'] . " (" . $a['vehicle_number'] . ")") : 'Unassigned';
            $a['deliveries'] = (int)$a['deliveries_count'];
            $a['deliveries_today'] = (int)$a['deliveries_today'];
            $a['rating'] = $a['avg_rating'] ? round((float)$a['avg_rating'], 1) : 5.0;
            
            $fleet_deliveries_today += $a['deliveries_today'];
            if ($a['avg_rating']) {
                $rating_sum += $a['avg_rating'];
                $rating_count++;
            }
        }
        
        $fleet_avg = $rating_count > 0 ? round($rating_sum / $rating_count, 1) : 4.8;
        
        return ResponseHandler::success([
            'agents' => array_values($agents),
            'fleet_stats' => [
                'total_fleet' => count($agents),
                'active_fleet' => count(array_filter($agents, fn($a) => $a['status'] === 'active')),
                'avg_rating' => $fleet_avg,
                'deliveries_today' => $fleet_deliveries_today
            ]
        ], 'Logistics personnel directory synchronized with live telemetry.');
    }

    /**
     * POST /admin/categories
     */
    public function createCategory() {
        AuthGuard::handle();
        RoleCheck::handle('admin');
        $data = $this->getPostData();

        try {
            $id = $this->categoryModel->create([
                'name' => $data['name'],
                'slug' => strtolower(str_replace(' ', '-', $data['name'])),
                'description' => $data['description'] ?? '',
                'parent_id' => null
            ]);
            return ResponseHandler::success(['id' => $id], 'Root category established.');
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * POST /admin/categories/{id}/sub
     */
    public function addSubCategory($id) {
        AuthGuard::handle();
        RoleCheck::handle('admin');
        $data = $this->getPostData();

        try {
            $subId = $this->categoryModel->create([
                'name' => $data['name'],
                'slug' => strtolower(str_replace(' ', '-', $data['name'])) . '-' . $id,
                'parent_id' => $id
            ]);
            return ResponseHandler::success(['id' => $subId], 'Sub-category node registered.');
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * DELETE /admin/categories/{id}
     */
    public function deleteCategory($id) {
        AuthGuard::handle();
        RoleCheck::handle('admin');
        $this->categoryModel->delete($id);
        return ResponseHandler::success([], 'Category node decommissioned.');
    }

    /**
     * GET /admin/newsletter/campaigns
     */
    public function campaigns() {
        AuthGuard::handle();
        RoleCheck::handle('admin');
        
        $db = \App\Core\Database::getInstance()->getConnection();
        $campaigns = $db->query("SELECT * FROM newsletter_campaigns ORDER BY created_at DESC")->fetchAll();
        $subCount = $db->query("SELECT COUNT(*) FROM newsletter_subscribers WHERE status = 'active'")->fetchColumn();
        
        return ResponseHandler::success([
            'campaigns' => $campaigns,
            'stats' => [
                'active_reach' => (int)$subCount,
                'avg_open_rate' => '12.4%' 
            ]
        ], 'Communication dossier retrieved.');
    }

    /**
     * POST /admin/newsletter/campaign
     */
    public function createCampaign() {
        AuthGuard::handle();
        RoleCheck::handle('admin');
        $data = $this->getPostData();
        
        if (empty($data['subject']) || empty($data['body'])) {
            return ResponseHandler::badRequest('Subject and body required.');
        }

        $db = \App\Core\Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO newsletter_campaigns (subject, body, sent_at, created_at) VALUES (?, ?, NOW(), NOW())");
        $stmt->execute([$data['subject'], $data['body']]);
        
        return ResponseHandler::success([], 'Campaign broadcasted to clinical subscriber network.');
    }

    /**
     * GET /admin/backups
     */
    public function listBackups() {
        AuthGuard::handle();
        RoleCheck::handle('admin');
        
        $db = \App\Core\Database::getInstance()->getConnection();
        $backups = $db->query("SELECT *, 'Full' as type, 'success' as status, size_mb as size FROM backups ORDER BY created_at DESC")->fetchAll();
        
        foreach($backups as &$b) {
            $b['size'] = $b['size'] . ' MB';
        }

        return ResponseHandler::success(['backups' => $backups]);
    }

    /**
     * POST /admin/backups
     */
    public function createBackup() {
        AuthGuard::handle();
        RoleCheck::handle('admin');
        
        $filename = 'backup_' . date('Y-m-d_His') . '.sql';
        $path = 'storage/backups/' . $filename;
        
        $basePath = dirname(dirname(dirname(__DIR__)));
        $fullPath = $basePath . '/' . $path;

        // Ensure directory exists
        if (!is_dir($basePath . '/storage/backups')) {
            mkdir($basePath . '/storage/backups', 0777, true);
        }

        // Real backup content generation
        $backupService = new \App\Services\DatabaseBackupService();
        try {
            $size = $backupService->createBackup($fullPath);
            
            $db = \App\Core\Database::getInstance()->getConnection();
            $stmt = $db->prepare("INSERT INTO backups (filename, size_mb, path, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$filename, $size, $path]);
            
            return ResponseHandler::success([], 'Database snapshot captured and secured.');
        } catch (Exception $e) {
            return ResponseHandler::error('Backup failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * GET /admin/roles/permissions
     */
    public function getPermissions() {
        AuthGuard::handle();
        RoleCheck::handle('admin');
        
        $db = \App\Core\Database::getInstance()->getConnection();
        $roles = $db->query("SELECT id, name FROM roles")->fetchAll();
        $permissions = $db->query("SELECT id, name, slug, `group` as module FROM permissions")->fetchAll();
        $rp = $db->query("SELECT role_id, permission_id FROM role_permissions")->fetchAll();
        
        // Transform into the matrix format the frontend expects
        $matrix = [];
        foreach ($permissions as $p) {
            $row = [
                'id' => $p['id'],
                'module' => $p['module'] ?? 'General',
                'name' => $p['name'],
                'slug' => $p['slug']
            ];
            
            foreach ($roles as $r) {
                $hasPerm = false;
                foreach ($rp as $rel) {
                    if ($rel['role_id'] == $r['id'] && $rel['permission_id'] == $p['id']) {
                        $hasPerm = true;
                        break;
                    }
                }
                $row[strtolower($r['name'])] = $hasPerm;
            }
            $matrix[] = $row;
        }

        return ResponseHandler::success([
            'roles' => $roles,
            'matrix' => $matrix
        ], 'Access control matrix synchronized.');
    }

    /**
     * POST /admin/roles/assign
     */
    public function savePermissions() {
        AuthGuard::handle();
        RoleCheck::handle('admin');
        $admin = AuthGuard::getUser();
        $data = $this->getPostData();
        $matrix = $data['matrix'] ?? [];

        if (empty($matrix)) return ResponseHandler::badRequest('Matrix data required.');

        $db = \App\Core\Database::getInstance()->getConnection();
        $roles = $db->query("SELECT id, name FROM roles")->fetchAll();

        try {
            $db->beginTransaction();
            
            // Clear existing permissions
            $db->exec("DELETE FROM role_permissions");

            $stmt = $db->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");

            foreach ($matrix as $row) {
                foreach ($roles as $r) {
                    $roleKey = strtolower($r['name']);
                    if (isset($row[$roleKey]) && $row[$roleKey] === true) {
                        $stmt->execute([$r['id'], $row['id']]);
                    }
                }
            }

            $db->commit();
            $this->auditModel->log($admin['id'], 'PERMISSIONS_UPDATED', 'Role', 0, 'Global access matrix redefined.');
            return ResponseHandler::success([], 'Permission matrix securely anchored to core nodes.');
        } catch (Exception $e) {
            $db->rollBack();
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }
    /**
     * GET /admin/kyc/pending
     */
    public function pendingKYC() {
        AuthGuard::handle();
        RoleCheck::handle('admin');

        $db = \App\Core\Database::getInstance()->getConnection();
        // Return ALL KYC documents (frontend handles filtering by status)
        $stmt = $db->query("
            SELECT kd.*, u.name as user_name, u.role as user_role, u.email as user_email
            FROM kyc_documents kd
            JOIN users u ON kd.user_id = u.id
            ORDER BY kd.created_at DESC
        ");

        return ResponseHandler::success(['documents' => $stmt->fetchAll()], 'KYC records retrieved.');
    }

    /**
     * GET /admin/kyc/status/{id}
     */
    public function kycStatus($id) {
        AuthGuard::handle();
        RoleCheck::handle('admin');

        $db = \App\Core\Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT status FROM kyc_documents WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$id]);
        $status = $stmt->fetchColumn();

        return ResponseHandler::success(['status' => $status ?: 'unsubmitted'], 'KYC status retrieved.');
    }

    /**
     * POST /admin/kyc/{id}/status
     */
    public function updateKYCStatus($id) {
        AuthGuard::handle();
        RoleCheck::handle('admin');
        $admin = AuthGuard::getUser();

        $data = $this->getPostData();
        $status = $data['status'] ?? 'verified'; // verified or rejected
        $reason = $data['reason'] ?? null;

        $db = \App\Core\Database::getInstance()->getConnection();
        
        try {
            $db->beginTransaction();

            // 1. Update Document Status
            $stmt = $db->prepare("UPDATE kyc_documents SET status = ?, rejection_reason = ?, verified_at = NOW() WHERE id = ?");
            $stmt->execute([$status, $reason, $id]);

            // 2. If verified, potentially activate user
            if ($status === 'verified') {
                $doc = $db->query("SELECT user_id FROM kyc_documents WHERE id = $id")->fetch();
                if ($doc) {
                    $this->userModel->update($doc['user_id'], ['status' => 'active']);
                }
            }

            $db->commit();
            $this->auditModel->log($admin['id'], 'KYC_STATUS_UPDATED', 'KYC', $id, ['status' => $status]);
            return ResponseHandler::success([], "KYC node $id transitioned to $status.");

        } catch (Exception $e) {
            $db->rollBack();
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    public function userKYC($id) {
        AuthGuard::handle();
        RoleCheck::handle('admin');

        $db = \App\Core\Database::getInstance()->getConnection();
        try {
            $stmt = $db->prepare("SELECT * FROM kyc_documents WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->execute([$id]);
            $documents = $stmt->fetchAll();
            return ResponseHandler::success(['documents' => $documents], 'User KYC records retrieved.');
        } catch (\Exception $e) {
            // Table may not exist or other DB error - return empty gracefully
            return ResponseHandler::success(['documents' => []], 'No KYC records available.');
        }
    }

    // --- BLOG MANAGEMENT ---
    
    public function getBlogs() {
        AuthGuard::handle();
        RoleCheck::handle('admin');
        
        $db = \App\Core\Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT id, title, slug, author_name, status, created_at FROM blogs ORDER BY created_at DESC");
        return ResponseHandler::success(['blogs' => $stmt->fetchAll()], 'Blogs retrieved.');
    }

    public function createBlog() {
        AuthGuard::handle();
        RoleCheck::handle('admin');
        $admin = AuthGuard::getUser();
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (empty($data['title']) || empty($data['content'])) {
            return ResponseHandler::badRequest("Title and content are required.");
        }
        
        // Generate Slug
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['title'])));
        
        $db = \App\Core\Database::getInstance()->getConnection();
        
        try {
            $stmt = $db->prepare("INSERT INTO blogs (title, slug, content, cover_image, author_name, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['title'],
                $slug,
                $data['content'],
                $data['cover_image'] ?? null,
                $data['author_name'] ?? 'Admin',
                $data['status'] ?? 'published'
            ]);
            
            $this->auditModel->log($admin['id'], 'BLOG_CREATED', 'BLOG', $db->lastInsertId(), ['title' => $data['title']]);
            return ResponseHandler::success([], "Blog published successfully.");
        } catch (Exception $e) {
            return ResponseHandler::error("Failed to create blog: " . $e->getMessage(), 500);
        }
    }

    public function deleteBlog($id) {
        AuthGuard::handle();
        RoleCheck::handle('admin');
        $admin = AuthGuard::getUser();
        
        $db = \App\Core\Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM blogs WHERE id = ?");
        $stmt->execute([$id]);
        
        $this->auditModel->log($admin['id'], 'BLOG_DELETED', 'BLOG', $id, []);
        return ResponseHandler::success([], "Blog deleted successfully.");
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Emergency Order Priority System — Admin Endpoints
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * GET /admin/emergency-orders
     *
     * Lists all active emergency orders with SLA status, rider info, and
     * distance to destination. Sorted: breached first (elapsed breach desc),
     * then by remaining SLA time ascending.
     */
    public function emergencyOrders() {
        AuthGuard::handle();
        RoleCheck::handle('admin');

        $db = \App\Core\Database::getInstance()->getConnection();

        try {
            // Query all active emergency orders with rider info
            $stmt = $db->query("
                SELECT o.id, o.user_id, o.vendor_id, o.status, o.created_at,
                       o.sla_deadline, o.sla_breach, o.delivery_partner_id,
                       o.emergency_acknowledged_at, o.emergency_ready_at,
                       o.emergency_picked_up_at, o.reassignment_count,
                       o.grand_total,
                       COALESCE(u_rider.name, 'Unassigned') AS rider_name,
                       u_rider.latitude AS rider_lat,
                       u_rider.longitude AS rider_lng,
                       u_customer.name AS customer_name,
                       a.latitude AS dest_lat,
                       a.longitude AS dest_lng,
                       vp.latitude AS vendor_lat,
                       vp.longitude AS vendor_lng
                FROM orders o
                LEFT JOIN users u_rider ON o.delivery_partner_id = u_rider.id
                LEFT JOIN users u_customer ON o.user_id = u_customer.id
                LEFT JOIN addresses a ON o.address_id = a.id
                LEFT JOIN vendor_profiles vp ON o.vendor_id = vp.user_id
                WHERE o.is_emergency = 1
                  AND o.status IN ('placed', 'confirmed', 'dispatched', 'in-transit', 'processing')
                  AND NOT (o.payment_method IN ('card', 'upi') AND o.payment_status = 'pending')
            ");

            $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $priorityEngine = new PriorityEngine();
            $now = time();

            $emergencyOrders = [];
            foreach ($orders as $order) {
                // Calculate remaining SLA time
                $slaDeadline = $order['sla_deadline']
                    ? strtotime($order['sla_deadline'])
                    : strtotime($order['created_at']) + (40 * 60);
                $remainingSeconds = $slaDeadline - $now;

                // SLA status classification
                if ($remainingSeconds < 0) {
                    $slaStatus = 'breached';
                    $elapsedBreachSeconds = abs($remainingSeconds);
                    $elapsedBreachFormatted = sprintf('%02d:%02d', floor($elapsedBreachSeconds / 60), $elapsedBreachSeconds % 60);
                } elseif ($remainingSeconds < 600) { // < 10 minutes
                    $slaStatus = 'warning';
                    $elapsedBreachSeconds = 0;
                    $elapsedBreachFormatted = null;
                } else {
                    $slaStatus = 'normal';
                    $elapsedBreachSeconds = 0;
                    $elapsedBreachFormatted = null;
                }

                // Format remaining time as HH:MM:SS
                $absRemaining = abs($remainingSeconds);
                $remainingFormatted = sprintf(
                    '%02d:%02d:%02d',
                    floor($absRemaining / 3600),
                    floor(($absRemaining % 3600) / 60),
                    $absRemaining % 60
                );

                // Calculate rider distance to destination
                $riderDistance = null;
                if ($order['rider_lat'] && $order['rider_lng']) {
                    // Determine destination: if picked up → customer, else → vendor
                    if ($order['emergency_picked_up_at'] !== null && $order['dest_lat'] && $order['dest_lng']) {
                        $riderDistance = $priorityEngine->calculateHaversineDistance(
                            (float) $order['rider_lat'], (float) $order['rider_lng'],
                            (float) $order['dest_lat'], (float) $order['dest_lng']
                        );
                    } elseif ($order['vendor_lat'] && $order['vendor_lng']) {
                        $riderDistance = $priorityEngine->calculateHaversineDistance(
                            (float) $order['rider_lat'], (float) $order['rider_lng'],
                            (float) $order['vendor_lat'], (float) $order['vendor_lng']
                        );
                    }
                    if ($riderDistance !== null) {
                        $riderDistance = round($riderDistance, 2);
                    }
                }

                $emergencyOrders[] = [
                    'id' => (int) $order['id'],
                    'customer_name' => $order['customer_name'],
                    'status' => $order['status'],
                    'created_at' => $order['created_at'],
                    'sla_deadline' => $order['sla_deadline'],
                    'grand_total' => $order['grand_total'],
                    'rider_name' => $order['rider_name'],
                    'rider_distance_km' => $riderDistance,
                    'remaining_sla_time' => $remainingFormatted,
                    'remaining_seconds' => $remainingSeconds,
                    'sla_status' => $slaStatus,
                    'elapsed_breach_time' => $elapsedBreachFormatted,
                    'elapsed_breach_seconds' => $elapsedBreachSeconds,
                    'reassignment_count' => (int) $order['reassignment_count'],
                    'delivery_partner_id' => $order['delivery_partner_id'] ? (int) $order['delivery_partner_id'] : null,
                ];
            }

            // Sort: breached first (by elapsed breach time desc), then by remaining SLA time asc
            usort($emergencyOrders, function ($a, $b) {
                // Breached orders come first
                $aBreached = $a['sla_status'] === 'breached' ? 1 : 0;
                $bBreached = $b['sla_status'] === 'breached' ? 1 : 0;

                if ($aBreached !== $bBreached) {
                    return $bBreached - $aBreached; // breached first
                }

                // Within breached: sort by elapsed breach time descending
                if ($aBreached && $bBreached) {
                    return $b['elapsed_breach_seconds'] - $a['elapsed_breach_seconds'];
                }

                // Within non-breached: sort by remaining SLA time ascending
                return $a['remaining_seconds'] - $b['remaining_seconds'];
            });

            return ResponseHandler::success([
                'emergency_orders' => $emergencyOrders,
                'stats' => [
                    'total' => count($emergencyOrders),
                    'breached' => count(array_filter($emergencyOrders, fn($o) => $o['sla_status'] === 'breached')),
                    'warning' => count(array_filter($emergencyOrders, fn($o) => $o['sla_status'] === 'warning')),
                    'normal' => count(array_filter($emergencyOrders, fn($o) => $o['sla_status'] === 'normal')),
                    'unassigned' => count(array_filter($emergencyOrders, fn($o) => $o['delivery_partner_id'] === null)),
                ],
                'timestamp' => date('Y-m-d H:i:s')
            ], 'Emergency order telemetry synchronized.');

        } catch (Exception $e) {
            return ResponseHandler::error('Failed to retrieve emergency orders: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /admin/emergency-orders/{id}/reassign
     *
     * Manual reassignment of an emergency order to the next nearest eligible
     * delivery partner. Unlocks the current partner, assigns the new one,
     * creates an escalation record, and notifies both partners.
     */
    public function manualReassign($id) {
        AuthGuard::handle();
        RoleCheck::handle('admin');
        $admin = AuthGuard::getUser();

        $db = \App\Core\Database::getInstance()->getConnection();

        try {
            // Fetch the emergency order
            $stmt = $db->prepare("
                SELECT o.id, o.vendor_id, o.delivery_partner_id, o.sla_deadline, o.status,
                       vp.latitude AS vendor_lat, vp.longitude AS vendor_lng
                FROM orders o
                LEFT JOIN vendor_profiles vp ON o.vendor_id = vp.user_id
                WHERE o.id = ? AND o.is_emergency = 1
            ");
            $stmt->execute([$id]);
            $order = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$order) {
                return ResponseHandler::error('Emergency order not found.', 404);
            }

            if (!in_array($order['status'], ['placed', 'confirmed', 'dispatched', 'in-transit', 'processing'])) {
                return ResponseHandler::error('Order is not in an active state for reassignment.', 422);
            }

            $priorityEngine = new PriorityEngine();
            $notificationService = new NotificationService();

            $originalPartnerId = $order['delivery_partner_id'] ? (int) $order['delivery_partner_id'] : null;
            $excludePartnerId = $originalPartnerId ?? 0;

            // Unlock the current partner if one is assigned
            if ($originalPartnerId) {
                $priorityEngine->unlockPartner($originalPartnerId);

                // Revoke assignment in the log
                $revokeStmt = $db->prepare("
                    UPDATE emergency_assignment_log 
                    SET status = 'revoked', revoked_at = NOW(), revoke_reason = 'Manual admin reassignment'
                    WHERE order_id = ? AND partner_id = ? AND status = 'assigned'
                ");
                $revokeStmt->execute([$id, $originalPartnerId]);
            }

            // Find next nearest eligible partner
            $vendorLat = (float) ($order['vendor_lat'] ?? 0);
            $vendorLng = (float) ($order['vendor_lng'] ?? 0);

            if (!$vendorLat || !$vendorLng) {
                return ResponseHandler::error('Vendor location not available for reassignment.', 422);
            }

            $newPartner = $priorityEngine->findNearestEligiblePartner($vendorLat, $vendorLng, $excludePartnerId);

            if ($newPartner === null) {
                return ResponseHandler::error('No eligible delivery partner available for reassignment.', 503);
            }

            // Lock and assign the new partner
            $locked = $priorityEngine->lockPartner((int) $newPartner['id'], (int) $id);

            if (!$locked) {
                return ResponseHandler::error('Failed to lock new delivery partner. They may have been assigned elsewhere.', 409);
            }

            // Update order with new partner
            $updateStmt = $db->prepare("
                UPDATE orders 
                SET delivery_partner_id = ?, reassignment_count = reassignment_count + 1
                WHERE id = ?
            ");
            $updateStmt->execute([$newPartner['id'], $id]);

            // Log the new assignment
            $slaDeadline = $order['sla_deadline'] ?? date('Y-m-d H:i:s', strtotime('+40 minutes'));
            $logStmt = $db->prepare("
                INSERT INTO emergency_assignment_log 
                (order_id, partner_id, assignment_timestamp, rider_distance_km, sla_deadline,
                 vendor_lat, vendor_lng, partner_lat, partner_lng, assignment_type, status)
                VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, ?, 'reassignment', 'assigned')
            ");
            $logStmt->execute([
                $id,
                $newPartner['id'],
                $newPartner['distance_km'],
                $slaDeadline,
                $vendorLat,
                $vendorLng,
                (float) $newPartner['latitude'],
                (float) $newPartner['longitude']
            ]);

            // Create escalation record with manual_intervention level
            $escalationStmt = $db->prepare("
                INSERT INTO emergency_escalations 
                (order_id, escalation_level, reason, triggered_at, resolved_at, resolved_by, partner_id, action_taken)
                VALUES (?, 'manual_intervention', ?, NOW(), NOW(), ?, ?, ?)
            ");
            $reason = $originalPartnerId
                ? "Admin manually reassigned from partner #{$originalPartnerId} to partner #{$newPartner['id']}"
                : "Admin manually assigned partner #{$newPartner['id']} to unassigned order";
            $actionTaken = "Reassigned to partner {$newPartner['name']} ({$newPartner['distance_km']}km away)";
            $escalationStmt->execute([
                $id,
                $reason,
                $admin['id'],
                $newPartner['id'],
                $actionTaken
            ]);

            // Notify original partner (if there was one)
            if ($originalPartnerId) {
                $notificationService->send(
                    $originalPartnerId,
                    'Emergency Order Reassigned',
                    "Emergency order #{$id} has been reassigned by admin. You are no longer assigned to this delivery."
                );
            }

            // Notify new partner
            $notificationService->send(
                (int) $newPartner['id'],
                'Emergency Order Assigned',
                "Emergency order #{$id} has been assigned to you by admin. Please accept within 90 seconds. SLA deadline: {$slaDeadline}"
            );

            // Audit log
            $this->auditModel->log($admin['id'], 'EMERGENCY_MANUAL_REASSIGN', 'Order', $id, [
                'original_partner_id' => $originalPartnerId,
                'new_partner_id' => (int) $newPartner['id'],
                'new_partner_name' => $newPartner['name'],
                'distance_km' => $newPartner['distance_km']
            ]);

            return ResponseHandler::success([
                'order_id' => (int) $id,
                'original_partner_id' => $originalPartnerId,
                'new_partner_id' => (int) $newPartner['id'],
                'new_partner_name' => $newPartner['name'],
                'distance_km' => $newPartner['distance_km'],
                'sla_deadline' => $slaDeadline
            ], "Emergency order #{$id} reassigned to {$newPartner['name']}.");

        } catch (Exception $e) {
            return ResponseHandler::error('Reassignment failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /cron/emergency-sla-check
     *
     * Cron-triggered endpoint that evaluates all active emergency orders,
     * processes the retry queue, checks vendor preparation deadlines,
     * and handles partner acceptance timeouts. Uses a file-based execution
     * lock to prevent overlapping cron runs.
     */
    public function emergencySlaCheck()
    {
        // Simple API key check for cron access (no auth middleware needed)
        $headers = getallheaders();
        $apiKey = $headers['X-Cron-Key'] ?? $headers['x-cron-key'] ?? $_GET['cron_key'] ?? null;
        $expectedKey = $_ENV['CRON_API_KEY'] ?? 'emergency-sla-cron-key';

        if ($apiKey !== $expectedKey) {
            return ResponseHandler::error('Unauthorized cron access.', 403);
        }

        // Execution lock to prevent overlapping cron runs
        $lockFile = sys_get_temp_dir() . '/emergency_sla_check.lock';

        if (file_exists($lockFile)) {
            $lockTime = (int) file_get_contents($lockFile);
            // If lock is older than 5 minutes, consider it stale and remove
            if (time() - $lockTime > 300) {
                unlink($lockFile);
            } else {
                return ResponseHandler::success([
                    'status' => 'skipped',
                    'reason' => 'Previous cron run still active',
                    'lock_age_seconds' => time() - $lockTime
                ], 'Cron execution skipped due to active lock.');
            }
        }

        // Acquire lock
        file_put_contents($lockFile, (string) time());

        try {
            $priorityEngine = new PriorityEngine();
            $db = \App\Core\Database::getInstance()->getConnection();
            $summary = [
                'executed_at' => date('Y-m-d H:i:s'),
                'sla_evaluation' => [],
                'retry_queue' => [],
                'vendor_deadlines' => [],
                'partner_timeouts' => [],
                'errors' => []
            ];

            // 1. Evaluate active SLAs (warnings, reassignments, breach logging)
            try {
                $slaResult = $priorityEngine->evaluateActiveSLAs();
                $summary['sla_evaluation'] = $slaResult;
            } catch (\Exception $e) {
                $summary['errors'][] = 'SLA evaluation failed: ' . $e->getMessage();
                error_log("Cron SLA evaluation error: " . $e->getMessage());
            }

            // 2. Process retry queue (retry unassigned orders)
            try {
                $retryResult = $priorityEngine->processRetryQueue();
                $summary['retry_queue'] = $retryResult;
            } catch (\Exception $e) {
                $summary['errors'][] = 'Retry queue processing failed: ' . $e->getMessage();
                error_log("Cron retry queue error: " . $e->getMessage());
            }

            // 3. Check vendor preparation deadlines
            try {
                $vendorResult = $priorityEngine->checkVendorPreparationDeadlines();
                $summary['vendor_deadlines'] = $vendorResult;
            } catch (\Exception $e) {
                $summary['errors'][] = 'Vendor deadline check failed: ' . $e->getMessage();
                error_log("Cron vendor deadline error: " . $e->getMessage());
            }

            // 4. Check partner acceptance timeouts (90s)
            try {
                $timeoutResults = [];
                // Query all active emergency orders with assigned but not accepted partners
                $stmt = $db->query("
                    SELECT o.id
                    FROM orders o
                    INNER JOIN emergency_assignment_log eal 
                        ON eal.order_id = o.id AND eal.status = 'assigned'
                    WHERE o.is_emergency = 1
                      AND o.status IN ('placed', 'confirmed', 'dispatched', 'processing')
                      AND o.delivery_partner_id IS NOT NULL
                      AND TIMESTAMPDIFF(SECOND, eal.assignment_timestamp, NOW()) > 90
                    GROUP BY o.id
                ");
                $timedOutOrders = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                foreach ($timedOutOrders as $order) {
                    $timeoutResult = $priorityEngine->checkPartnerAcceptanceTimeout((int) $order['id']);
                    $timeoutResults[] = [
                        'order_id' => (int) $order['id'],
                        'result' => $timeoutResult
                    ];
                }
                $summary['partner_timeouts'] = [
                    'orders_checked' => count($timedOutOrders),
                    'results' => $timeoutResults
                ];
            } catch (\Exception $e) {
                $summary['errors'][] = 'Partner timeout check failed: ' . $e->getMessage();
                error_log("Cron partner timeout error: " . $e->getMessage());
            }

            // Release lock
            if (file_exists($lockFile)) {
                unlink($lockFile);
            }

            return ResponseHandler::success($summary, 'Emergency SLA check completed.');

        } catch (Exception $e) {
            // Release lock on failure
            if (file_exists($lockFile)) {
                unlink($lockFile);
            }
            error_log("Cron emergency-sla-check fatal error: " . $e->getMessage());
            return ResponseHandler::error('SLA check failed: ' . $e->getMessage(), 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Live Fleet Tracking — Admin Endpoint
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * GET /admin/fleet/live
     *
     * Returns all delivery partners with live GPS positions, current order info,
     * distance to destination, ETA, and color-coded marker type.
     * Supports ?filter=all|online|emergency|idle
     */
    public function fleetLive() {
        AuthGuard::handle();
        RoleCheck::handle('admin');

        $db = \App\Core\Database::getInstance()->getConnection();
        $filter = $_GET['filter'] ?? 'all';

        // Validate filter parameter
        $allowedFilters = ['all', 'online', 'emergency', 'idle'];
        if (!in_array($filter, $allowedFilters)) {
            $filter = 'all';
        }

        try {
            // Query all delivery agents with locations and active orders
            $stmt = $db->query("
                SELECT 
                    u.id,
                    u.name,
                    u.phone,
                    u.status,
                    al.latitude,
                    al.longitude,
                    al.updated_at as last_updated,
                    o.id as current_order_id,
                    o.is_emergency,
                    a.latitude as dest_lat,
                    a.longitude as dest_lng
                FROM users u
                LEFT JOIN agent_locations al ON u.id = al.agent_id
                LEFT JOIN orders o ON o.delivery_partner_id = u.id 
                    AND o.status IN ('assigned', 'picked_up', 'dispatched')
                LEFT JOIN addresses a ON o.address_id = a.id
                WHERE u.role = 'delivery' AND u.deleted_at IS NULL
            ");

            $rawRiders = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $riders = [];
            foreach ($rawRiders as $rider) {
                // Calculate distance to destination using Haversine formula
                $distance = null;
                $eta = null;
                if ($rider['latitude'] && $rider['longitude'] && $rider['dest_lat'] && $rider['dest_lng']) {
                    $distance = $this->haversineDistance(
                        (float)$rider['latitude'],
                        (float)$rider['longitude'],
                        (float)$rider['dest_lat'],
                        (float)$rider['dest_lng']
                    );
                    $distance = round($distance, 2);
                    // ETA: distance / 30 kmh * 60 = minutes
                    $eta = (int)ceil(($distance / 30) * 60);
                }

                // Determine marker type
                $markerType = 'green'; // default: idle
                if ($rider['is_emergency'] && $rider['current_order_id']) {
                    $markerType = 'red';       // emergency delivery
                } elseif ($rider['current_order_id']) {
                    $markerType = 'orange';    // standard delivery
                }

                $riders[] = [
                    'id' => (int)$rider['id'],
                    'name' => $rider['name'],
                    'phone' => $rider['phone'],
                    'status' => $rider['status'],
                    'latitude' => $rider['latitude'] ? (float)$rider['latitude'] : null,
                    'longitude' => $rider['longitude'] ? (float)$rider['longitude'] : null,
                    'current_order_id' => $rider['current_order_id'] ? (int)$rider['current_order_id'] : null,
                    'is_emergency' => (bool)$rider['is_emergency'],
                    'destination_lat' => $rider['dest_lat'] ? (float)$rider['dest_lat'] : null,
                    'destination_lng' => $rider['dest_lng'] ? (float)$rider['dest_lng'] : null,
                    'distance_to_dest' => $distance,
                    'eta_minutes' => $eta,
                    'marker_type' => $markerType,
                    'last_updated' => $rider['last_updated']
                ];
            }

            // Compute stats from full (unfiltered) dataset
            $stats = [
                'total' => count($riders),
                'online' => count(array_filter($riders, fn($r) => $r['status'] !== 'offline')),
                'in_delivery' => count(array_filter($riders, fn($r) => $r['current_order_id'] !== null)),
                'idle' => count(array_filter($riders, fn($r) => $r['status'] === 'active' && $r['current_order_id'] === null)),
                'emergency_active' => count(array_filter($riders, fn($r) => $r['is_emergency'] && $r['current_order_id'] !== null))
            ];

            // Apply filter
            switch ($filter) {
                case 'online':
                    $riders = array_values(array_filter($riders, fn($r) => $r['status'] === 'active' || $r['status'] === 'busy'));
                    break;
                case 'emergency':
                    $riders = array_values(array_filter($riders, fn($r) => $r['is_emergency'] && $r['current_order_id'] !== null));
                    break;
                case 'idle':
                    $riders = array_values(array_filter($riders, fn($r) => $r['status'] === 'active' && $r['current_order_id'] === null));
                    break;
                case 'all':
                default:
                    // No filtering
                    break;
            }

            return ResponseHandler::success([
                'riders' => $riders,
                'stats' => $stats
            ], 'Fleet live telemetry synchronized.');

        } catch (Exception $e) {
            return ResponseHandler::error('Fleet tracking failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Haversine formula to calculate distance between two GPS coordinates
     * @return float Distance in kilometers
     */
    private function haversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float {
        $R = 6371; // Earth radius in km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $R * $c;
    }
}
