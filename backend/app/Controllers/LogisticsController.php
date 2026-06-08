<?php
namespace App\Controllers;

/**
 * Logistics Controller
 * Admin interface for managing the delivery network and region coverage.
 */
class LogisticsController extends BaseController {

    public function agentNetwork() {
        $userModel = new \App\Models\User();
        $agents = $userModel->findAll();
        $deliveryAgents = array_filter($agents, fn($u) => $u['role'] === 'delivery');
        
        $onlineCount = count(array_filter($deliveryAgents, fn($a) => $a['status'] === 'active'));
        $offlineCount = count($deliveryAgents) - $onlineCount;

        return $this->json([
            'online_agents' => $onlineCount,
            'offline_agents' => $offlineCount,
            'zones' => ['Clinical Sector A', 'Operational Hub B', 'Dispatch Zone C']
        ]);
    }

    public function traceOrder($orderId) {
        $orderModel = new \App\Models\Order();
        $order = $orderModel->findById($orderId);
        
        if (!$order) return $this->json(['error' => 'Order node missing.'], 404);
        
        if (!$order['delivery_partner_id']) {
            return $this->json(['status' => 'Pending Assignment', 'eta_minutes' => null]);
        }

        $agent = (new \App\Models\User())->findById($order['delivery_partner_id']);
        
        // Fetch real-time coordinates from our new tracking table
        $db = \App\Core\Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT latitude, longitude FROM agent_locations WHERE agent_id = ? ORDER BY updated_at DESC LIMIT 1");
        $stmt->execute([$order['delivery_partner_id']]);
        $loc = $stmt->fetch();

        return $this->json([
            'order_id' => $orderId,
            'assigned_agent' => $agent['name'] ?? 'Authorized Courier',
            'current_location' => $loc ? ['lat' => (float)$loc['latitude'], 'lng' => (float)$loc['longitude']] : ['lat' => 19.0760, 'lng' => 72.8777],
            'eta_minutes' => $order['status'] === 'delivered' ? 0 : 15
        ]);
    }
}
