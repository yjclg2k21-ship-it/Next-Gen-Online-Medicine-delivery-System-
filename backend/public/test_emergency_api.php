<?php
// Quick test: simulate vendorEmergencyOrders for vendor_id=2
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Services\PriorityEngine;

header('Content-Type: application/json');

try {
    $db = Database::getInstance()->getConnection();
    $vendorId = 2;

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
           AND o.status IN ('placed', 'confirmed', 'dispatched', 'processing', 'accepted')
         ORDER BY o.created_at DESC"
    );
    $stmt->execute([$vendorId]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'raw_orders' => $orders,
        'count' => count($orders),
        'now_mysql' => $db->query("SELECT NOW() as now")->fetch()['now']
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
