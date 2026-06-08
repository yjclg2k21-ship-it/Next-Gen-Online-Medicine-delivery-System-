<?php
namespace App\Models;

use App\Core\Model;

/**
 * Order Model
 */
class Order extends Model {
    protected $table = 'orders';
    protected $useSoftDelete = true;

    /**
     * Get orders for a specific user
     */
    public function getByUserId($userId) {
        $stmt = $this->db->prepare("SELECT *, IF(is_emergency = 1, 'Emergency', 'Standard') as delivery_type FROM `{$this->table}` 
                                    WHERE user_id = ? AND deleted_at IS NULL 
                                    ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Get order details with items
     */
    public function getWithItems($orderId) {
        $order = $this->findById($orderId);
        if (!$order) return null;

        $order['delivery_type'] = ($order['is_emergency'] == 1) ? 'Emergency' : 'Standard';

        $stmt = $this->db->prepare("SELECT oi.*, m.name, m.image, b.type as brand_type 
                                    FROM `order_items` oi 
                                    JOIN `medicines` m ON oi.medicine_id = m.id 
                                    LEFT JOIN `brands` b ON m.brand_id = b.id
                                    WHERE oi.order_id = ?");
        $stmt->execute([$orderId]);
        $order['items'] = $stmt->fetchAll();
        
        return $order;
    }

    /**
     * Get orders for a vendor with status and limit options (Parity logic)
     */
    public function getVendorOrders($vendorId, $status = null, $limit = 0) {
        $sql = "SELECT o.*, u.name as customer_name, a.city, 
                IF(o.is_emergency = 1, 'Emergency', 'Standard') as delivery_type,
                (SELECT GROUP_CONCAT(m.name SEPARATOR ', ') FROM order_items oi JOIN medicines m ON m.id = oi.medicine_id WHERE oi.order_id = o.id LIMIT 3) as items_summary,
                (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) as item_count
                FROM `{$this->table}` o 
                LEFT JOIN users u ON o.user_id = u.id 
                LEFT JOIN addresses a ON o.address_id = a.id 
                WHERE o.vendor_id = ? AND (o.is_emergency = 1 OR NOT (o.payment_method IN ('card', 'upi') AND o.payment_status = 'pending'))";
        $params = [$vendorId];

        if ($status) {
            $sql .= " AND status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY created_at DESC";

        if ($limit > 0) {
            $sql .= " LIMIT " . (int)$limit;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Get vendor dashboard stats
     */
    public function getVendorStats($vendorId) {
        $stmt = $this->db->prepare(
            "SELECT COUNT(DISTINCT id) as total_orders, 
                    SUM(grand_total) as gross_revenue,
                    COUNT(DISTINCT user_id) as total_customers
             FROM `{$this->table}` WHERE vendor_id = ? AND status != 'cancelled' AND deleted_at IS NULL AND NOT (payment_method IN ('card', 'upi') AND payment_status = 'pending')"
        );
        $stmt->execute([$vendorId]);
        return $stmt->fetch() ?: ['total_orders' => 0, 'gross_revenue' => 0, 'total_customers' => 0];
    }

    /**
     * Update order status with optional validation.
     * Allows custom enum values; only ensures non-empty string.
     */
    public function setStatus($orderId, $newStatus) {
        if (empty($newStatus)) {
            return false;
        }
        return $this->update($orderId, ['status' => $newStatus]);
    }

    /**
     * Get revenue series for vendor
     */
    public function getRevenueSeries($vendorId) {
        $stmt = $this->db->prepare("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, SUM(grand_total) as revenue
              FROM `{$this->table}` WHERE vendor_id = ? AND status != 'cancelled' AND deleted_at IS NULL AND NOT (payment_method IN ('card', 'upi') AND payment_status = 'pending')
              GROUP BY month ORDER BY month DESC LIMIT 12");
        $stmt->execute([$vendorId]);
        return $stmt->fetchAll();
    }


    /**
     * Get vendor customers
     */
    public function getVendorCustomers($vendorId) {
        $stmt = $this->db->prepare(
            "SELECT u.id, u.name, u.email, u.phone, COUNT(o.id) as order_count, SUM(o.grand_total) as total_spent
             FROM users u
             JOIN `{$this->table}` o ON o.user_id = u.id
             WHERE o.vendor_id = ? AND o.deleted_at IS NULL AND NOT (o.payment_method IN ('card', 'upi') AND o.payment_status = 'pending')
             GROUP BY u.id ORDER BY total_spent DESC"
        );
        $stmt->execute([$vendorId]);
        return $stmt->fetchAll();
    }

    /**
     * Get delivery mix stats
     */
    public function getDeliveryMix($vendorId) {
        $stmt = $this->db->prepare(
            "SELECT dm.name, COUNT(o.id) as count
             FROM `{$this->table}` o
             JOIN delivery_methods dm ON o.delivery_method_id = dm.id
             WHERE o.vendor_id = ? AND o.deleted_at IS NULL AND NOT (o.payment_method IN ('card', 'upi') AND o.payment_status = 'pending')
             GROUP BY dm.id"
        );
        $stmt->execute([$vendorId]);
        return $stmt->fetchAll();
    }

    /**
     * Get revenue by category for a vendor
     */
    public function getRevenueByCategory($vendorId) {
        $stmt = $this->db->prepare(
            "SELECT c.name as cat, SUM(oi.price * oi.quantity) as rev
             FROM order_items oi
             JOIN `{$this->table}` o ON oi.order_id = o.id
             JOIN medicines m ON oi.medicine_id = m.id
             LEFT JOIN categories c ON m.category_id = c.id
             WHERE o.vendor_id = ? AND o.status != 'cancelled' AND o.deleted_at IS NULL AND NOT (o.payment_method IN ('card', 'upi') AND o.payment_status = 'pending')
             GROUP BY c.id
             ORDER BY rev DESC LIMIT 5"
        );
        $stmt->execute([$vendorId]);
        $results = $stmt->fetchAll();
        
        // Calculate total for percentage
        $totalRev = array_sum(array_column($results, 'rev'));
        if ($totalRev > 0) {
            foreach ($results as &$r) {
                $r['cat'] = $r['cat'] ?: 'Others';
                $r['pct'] = round(($r['rev'] / $totalRev) * 100);
            }
        }
        return $results;
    }
}
