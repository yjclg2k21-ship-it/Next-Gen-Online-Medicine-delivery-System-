<?php
namespace App\Services;

use App\Core\Database;

/**
 * SLATracker Service
 * Monitors delivery performance and flags SLA breaches.
 */
class SLATracker {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Calculate the SLA deadline for an order based on delivery method.
     */
    public function calculateDeadline(int $orderId): ?string {
        $stmt = $this->db->prepare(
            "SELECT o.created_at, dm.sla_hours 
             FROM orders o 
             JOIN delivery_methods dm ON o.delivery_method_id = dm.id 
             WHERE o.id = ?"
        );
        $stmt->execute([$orderId]);
        $data = $stmt->fetch();
        if (!$data) return null;

        $deadline = date('Y-m-d H:i:s', strtotime($data['created_at']) + ($data['sla_hours'] * 3600));
        return $deadline;
    }

    /**
     * Get SLA performance metrics — system wide or per delivery agent.
     */
    public function getMetrics(int $agentId = 0): array {
        $where = $agentId ? " AND agent_id = $agentId" : '';
        $total = (int)($this->db->query("SELECT COUNT(*) FROM orders WHERE status IN ('delivered','cancelled'){$where}")->fetchColumn() ?? 0);
        $onTime = (int)($this->db->query("SELECT COUNT(*) FROM orders WHERE status = 'delivered'{$where}")->fetchColumn() ?? 0);
        return [
            'total_orders' => $total,
            'on_time'      => $onTime,
            'sla_rate'     => $total > 0 ? round(($onTime / $total) * 100, 1) : 100,
        ];
    }

    /**
     * Monitor all placed/confirmed orders for SLA breaches.
     */
    public function checkBreaches(): int {
        $stmt = $this->db->query(
            "SELECT o.id, o.created_at, o.is_emergency, dm.sla_hours 
             FROM orders o 
             LEFT JOIN delivery_methods dm ON o.delivery_method_id = dm.id 
             WHERE o.status IN ('placed', 'confirmed', 'processing', 'assigned', 'picked_up', 'dispatched') AND o.sla_breach = 0"
        );
        $orders = $stmt->fetchAll();
        $breachCount = 0;

        foreach ($orders as $order) {
            $slaHours = $order['is_emergency'] ? 1 : ($order['sla_hours'] ?? 24);
            $deadline = strtotime($order['created_at']) + ($slaHours * 3600);
            
            if (time() > $deadline) {
                $this->db->prepare("UPDATE orders SET sla_breach = 1 WHERE id = ?")->execute([$order['id']]);
                
                $logStmt = $this->db->prepare("INSERT INTO order_status_logs (order_id, status, comment, created_at) VALUES (?, ?, ?, NOW())");
                $logStmt->execute([$order['id'], 'sla_breached', 'Order exceeded expected delivery timeframe.']);
                
                $breachCount++;
            }
        }
        return $breachCount;
    }
}
