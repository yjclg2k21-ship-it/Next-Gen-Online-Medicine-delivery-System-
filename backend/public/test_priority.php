<?php
require 'C:/xampp/htdocs/Next Gen Online Medicine Delivery System/backend/app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();
try {
    $stmt = $db->prepare(
        "SELECT o.id, o.user_id, o.vendor_id, o.delivery_partner_id,
                o.status, o.created_at, o.sla_deadline, o.sla_breach,
                o.emergency_acknowledged_at, o.emergency_ready_at,
                o.emergency_picked_up_at, o.emergency_delivered_at,
                o.address_id,
                u.name AS rider_name, u.phone AS rider_phone,
                u.latitude AS rider_lat, u.longitude AS rider_lng
         FROM orders o
         LEFT JOIN users u ON o.delivery_partner_id = u.id
         WHERE o.id = 2 AND o.is_emergency = 1"
    );
    $stmt->execute();
    print_r($stmt->fetch(PDO::FETCH_ASSOC));
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
