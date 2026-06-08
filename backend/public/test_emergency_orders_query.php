<?php
require 'C:/xampp/htdocs/Next Gen Online Medicine Delivery System/backend/app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();
try {
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
    ");
    print_r($stmt->fetchAll());
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
} catch (\Error $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
