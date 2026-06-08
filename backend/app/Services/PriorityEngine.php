<?php
namespace App\Services;

use App\Core\Database;
use PDO;

/**
 * Priority Engine Service
 * Central orchestrator for emergency order priority logic including
 * nearest-partner selection via Haversine distance, partner locking,
 * and radius-based filtering with fallback.
 */
class PriorityEngine
{
    private $db;
    private $notificationService;

    /** Earth's radius in kilometers */
    private const EARTH_RADIUS_KM = 6371.0;

    /** Default search radius for nearby partners (km) */
    private const DEFAULT_RADIUS_KM = 5.0;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->notificationService = new NotificationService();
    }

    /**
     * Calculate the Haversine distance between two GPS coordinate pairs.
     *
     * @param float $lat1 Latitude of point 1 (degrees)
     * @param float $lng1 Longitude of point 1 (degrees)
     * @param float $lat2 Latitude of point 2 (degrees)
     * @param float $lng2 Longitude of point 2 (degrees)
     * @return float Distance in kilometers
     */
    public function calculateHaversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        // Convert degrees to radians
        $lat1Rad = deg2rad($lat1);
        $lng1Rad = deg2rad($lng1);
        $lat2Rad = deg2rad($lat2);
        $lng2Rad = deg2rad($lng2);

        // Differences
        $dLat = $lat2Rad - $lat1Rad;
        $dLng = $lng2Rad - $lng1Rad;

        // Haversine formula
        $a = sin($dLat / 2) * sin($dLat / 2)
           + cos($lat1Rad) * cos($lat2Rad) * sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return self::EARTH_RADIUS_KM * $c;
    }

    /**
     * Find the nearest eligible delivery partner to a vendor location.
     *
     * Eligibility criteria:
     * - Role is 'delivery'
     * - Currently online (is_online = 1)
     * - No active emergency lock (emergency_locked_order_id IS NULL)
     * - Not marked as non-responsive (is_non_responsive = 0)
     * - Has valid latitude/longitude coordinates
     *
     * Sorting:
     * - Primary: shortest Haversine distance to vendor
     * - Tiebreaker (within 0.01km): longest idle time (earliest last_idle_since)
     *
     * @param float $vendorLat Vendor latitude
     * @param float $vendorLng Vendor longitude
     * @param int $excludePartnerId Partner ID to exclude (e.g., timed-out partner)
     * @return array|null Partner data with distance, or null if none available
     */
    public function findNearestEligiblePartner(float $vendorLat, float $vendorLng, int $excludePartnerId = 0): ?array
    {
        // First try within the default radius
        $partner = $this->findPartnerInRadius($vendorLat, $vendorLng, self::DEFAULT_RADIUS_KM, $excludePartnerId);

        if ($partner !== null) {
            return $partner;
        }

        // Fallback: find nearest eligible partner regardless of distance
        $partner = $this->findPartnerInRadius($vendorLat, $vendorLng, null, $excludePartnerId);

        if ($partner !== null) {
            $partner['beyond_radius'] = true;
            $partner['warning'] = 'Partner distance exceeds preferred ' . self::DEFAULT_RADIUS_KM . 'km radius';
        }

        return $partner;
    }

    /**
     * Get all online delivery partners within a given radius of a location.
     * If no partners are found within the radius, falls back to the nearest
     * eligible partner regardless of distance.
     *
     * @param float $lat Center latitude
     * @param float $lng Center longitude
     * @param float $radiusKm Search radius in kilometers (default 5km)
     * @return array List of eligible partners with distance info
     */
    public function getOnlinePartnersInRadius(float $lat, float $lng, float $radiusKm = 5.0): array
    {
        $partners = $this->queryEligiblePartners();

        $inRadius = [];
        foreach ($partners as $partner) {
            if ($partner['latitude'] === null || $partner['longitude'] === null) {
                continue;
            }

            $distance = $this->calculateHaversineDistance(
                $lat, $lng,
                (float) $partner['latitude'],
                (float) $partner['longitude']
            );

            if ($distance <= $radiusKm) {
                $partner['distance_km'] = round($distance, 2);
                $inRadius[] = $partner;
            }
        }

        // Sort by distance ascending
        usort($inRadius, function ($a, $b) {
            return $a['distance_km'] <=> $b['distance_km'];
        });

        if (!empty($inRadius)) {
            return $inRadius;
        }

        // Fallback: return the nearest partner regardless of distance
        $nearest = $this->findPartnerInRadius($lat, $lng, null, 0);
        if ($nearest !== null) {
            $nearest['beyond_radius'] = true;
            $nearest['warning'] = 'No partners within ' . $radiusKm . 'km radius; returning nearest available';
            return [$nearest];
        }

        return [];
    }

    /**
     * Lock a delivery partner to an emergency order, preventing them
     * from receiving additional orders until unlocked.
     *
     * @param int $partnerId The delivery partner's user ID
     * @param int $orderId The emergency order ID
     * @return bool True if lock was successful
     */
    public function lockPartner(int $partnerId, int $orderId): bool
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE users 
                 SET emergency_locked_order_id = ? 
                 WHERE id = ? 
                   AND role = 'delivery' 
                   AND emergency_locked_order_id IS NULL"
            );
            $stmt->execute([$orderId, $partnerId]);

            if ($stmt->rowCount() > 0) {
                // Also mark the order as emergency locked
                $orderStmt = $this->db->prepare(
                    "UPDATE orders SET emergency_locked = 1 WHERE id = ?"
                );
                $orderStmt->execute([$orderId]);
                return true;
            }

            return false;
        } catch (\Exception $e) {
            error_log("PriorityEngine::lockPartner error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Unlock a delivery partner, allowing them to receive new orders.
     * Called when an emergency delivery is completed or assignment is revoked.
     *
     * @param int $partnerId The delivery partner's user ID
     * @return bool True if unlock was successful
     */
    public function unlockPartner(int $partnerId): bool
    {
        try {
            // Get the order ID before unlocking so we can update the order too
            $stmt = $this->db->prepare(
                "SELECT emergency_locked_order_id FROM users WHERE id = ? AND role = 'delivery'"
            );
            $stmt->execute([$partnerId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                return false;
            }

            $orderId = $user['emergency_locked_order_id'];

            // Unlock the partner
            $unlockStmt = $this->db->prepare(
                "UPDATE users 
                 SET emergency_locked_order_id = NULL 
                 WHERE id = ? AND role = 'delivery'"
            );
            $unlockStmt->execute([$partnerId]);

            // Remove emergency lock from the order if applicable
            if ($orderId) {
                $orderStmt = $this->db->prepare(
                    "UPDATE orders SET emergency_locked = 0 WHERE id = ?"
                );
                $orderStmt->execute([$orderId]);
            }

            return $unlockStmt->rowCount() > 0;
        } catch (\Exception $e) {
            error_log("PriorityEngine::unlockPartner error: " . $e->getMessage());
            return false;
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ETA, SLA Progress, and Emergency Order Status Helpers
    // ─────────────────────────────────────────────────────────────────────────

    /** Average delivery speed in km/h (urban area) */
    private const AVERAGE_SPEED_KMH = 25.0;

    /** Buffer time in minutes (parking, stairs, etc.) */
    private const BUFFER_MINUTES = 3;

    /** SLA window in minutes */
    private const SLA_WINDOW_MINUTES = 40;

    /** Stale GPS threshold in seconds */
    private const STALE_GPS_THRESHOLD_SECONDS = 60;

    /**
     * Calculate the estimated time of arrival (ETA) for an emergency order.
     *
     * Uses Haversine distance between the rider's current GPS position and the
     * delivery destination, divided by average speed, plus a buffer for parking/stairs.
     *
     * If the rider's GPS data is older than 60 seconds, the ETA is flagged as stale.
     * If no rider is assigned or no GPS data is available, returns null.
     *
     * @param int $orderId The emergency order ID
     * @return array|null Array with 'eta_minutes', 'is_stale', 'distance_km', or null if unavailable
     */
    public function calculateETA(int $orderId): ?array
    {
        try {
            // Fetch order details including delivery partner and destination
            $stmt = $this->db->prepare(
                "SELECT o.id, o.delivery_partner_id, o.vendor_id, o.address_id,
                        o.emergency_picked_up_at, o.sla_deadline,
                        a.latitude AS dest_lat, a.longitude AS dest_lng,
                        vp.latitude AS vendor_lat, vp.longitude AS vendor_lng
                 FROM orders o
                 LEFT JOIN addresses a ON o.address_id = a.id
                 LEFT JOIN vendor_profiles vp ON o.vendor_id = vp.user_id
                 WHERE o.id = ? AND o.is_emergency = 1"
            );
            $stmt->execute([$orderId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order || !$order['delivery_partner_id']) {
                return null;
            }

            // Get rider's latest GPS position from agent_locations
            $gpsStmt = $this->db->prepare(
                "SELECT latitude, longitude, updated_at
                 FROM agent_locations
                 WHERE agent_id = ?
                 ORDER BY updated_at DESC
                 LIMIT 1"
            );
            $gpsStmt->execute([$order['delivery_partner_id']]);
            $gps = $gpsStmt->fetch(PDO::FETCH_ASSOC);

            if (!$gps || $gps['latitude'] === null || $gps['longitude'] === null) {
                return null;
            }

            // Determine destination based on order stage
            // If picked up → destination is customer address
            // If not picked up → destination is vendor location
            if ($order['emergency_picked_up_at'] !== null) {
                // Rider is en route to customer
                $destLat = (float) $order['dest_lat'];
                $destLng = (float) $order['dest_lng'];
            } else {
                // Rider is en route to vendor
                $destLat = (float) $order['vendor_lat'];
                $destLng = (float) $order['vendor_lng'];
            }

            if (!$destLat || !$destLng) {
                return null;
            }

            // Calculate distance using Haversine
            $distanceKm = $this->calculateHaversineDistance(
                (float) $gps['latitude'],
                (float) $gps['longitude'],
                $destLat,
                $destLng
            );

            // Calculate ETA: (distance / speed) * 60 + buffer
            $travelMinutes = ($distanceKm / self::AVERAGE_SPEED_KMH) * 60;
            $etaMinutes = $travelMinutes + self::BUFFER_MINUTES;

            // Check GPS staleness
            $gpsUpdatedAt = strtotime($gps['updated_at']);
            $now = time();
            $isStale = ($now - $gpsUpdatedAt) > self::STALE_GPS_THRESHOLD_SECONDS;

            // Check if ETA exceeds SLA deadline (delay detection)
            $isDelayed = false;
            if ($order['sla_deadline']) {
                $slaDeadlineTimestamp = strtotime($order['sla_deadline']);
                $estimatedDeliveryTime = $now + ($etaMinutes * 60);
                $isDelayed = $estimatedDeliveryTime > $slaDeadlineTimestamp;
            }

            return [
                'eta_minutes' => round($etaMinutes, 1),
                'distance_km' => round($distanceKm, 2),
                'is_stale' => $isStale,
                'is_delayed' => $isDelayed,
                'gps_updated_at' => $gps['updated_at'],
            ];
        } catch (\Exception $e) {
            error_log("PriorityEngine::calculateETA error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get the SLA progress for an emergency order.
     *
     * Returns the percentage of the 40-minute SLA window that has elapsed (0-100).
     * Also returns remaining time in seconds and SLA status classification.
     *
     * @param int $orderId The emergency order ID
     * @return array Array with 'progress_percentage', 'elapsed_seconds', 'remaining_seconds', 'sla_status'
     */
    public function getSLAProgress(int $orderId): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT created_at, sla_deadline
                 FROM orders
                 WHERE id = ? AND is_emergency = 1"
            );
            $stmt->execute([$orderId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                return [
                    'progress_percentage' => 0,
                    'elapsed_seconds' => 0,
                    'remaining_seconds' => 0,
                    'sla_status' => 'unknown',
                ];
            }

            $createdAt = strtotime($order['created_at']);
            $now = time();
            $slaWindowSeconds = self::SLA_WINDOW_MINUTES * 60; // 2400 seconds

            $elapsedSeconds = $now - $createdAt;
            $remainingSeconds = $slaWindowSeconds - $elapsedSeconds;

            // Progress percentage: min(100, (elapsed / 2400) * 100)
            $progressPercentage = min(100, ($elapsedSeconds / $slaWindowSeconds) * 100);
            $progressPercentage = max(0, $progressPercentage);

            // SLA status classification
            if ($remainingSeconds < 0) {
                $slaStatus = 'breached';
            } elseif ($remainingSeconds < 600) { // less than 10 minutes
                $slaStatus = 'warning';
            } else {
                $slaStatus = 'normal';
            }

            return [
                'progress_percentage' => round($progressPercentage, 1),
                'elapsed_seconds' => max(0, $elapsedSeconds),
                'remaining_seconds' => $remainingSeconds,
                'sla_status' => $slaStatus,
                'sla_deadline' => $order['sla_deadline'],
            ];
        } catch (\Exception $e) {
            error_log("PriorityEngine::getSLAProgress error: " . $e->getMessage());
            return [
                'progress_percentage' => 0,
                'elapsed_seconds' => 0,
                'remaining_seconds' => 0,
                'sla_status' => 'unknown',
            ];
        }
    }

    /**
     * Get the full emergency order status including current stage, timestamps,
     * rider info, ETA, and SLA status classification.
     *
     * Order stages (based on timestamps):
     * - Confirmed: order placed, no acknowledgment yet
     * - Preparing: emergency_acknowledged_at is set
     * - Ready for Pickup: emergency_ready_at is set
     * - Rider En Route to Vendor: delivery_partner_id assigned, not picked up
     * - Picked Up: emergency_picked_up_at is set
     * - Rider En Route to You: picked up, not delivered
     * - Delivered: emergency_delivered_at is set
     *
     * @param int $orderId The emergency order ID
     * @return array Full status array with stage, timestamps, rider info, SLA data
     */
    public function getEmergencyOrderStatus(int $orderId): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT o.id, o.user_id, o.vendor_id, o.delivery_partner_id,
                        o.status, o.created_at, o.sla_deadline, o.sla_breach,
                        o.emergency_acknowledged_at, o.emergency_ready_at,
                        o.emergency_picked_up_at, o.emergency_delivered_at,
                        o.address_id,
                        u.name AS rider_name, u.phone AS rider_phone,
                        u.latitude AS rider_lat, u.longitude AS rider_lng
                 FROM orders o
                 LEFT JOIN users u ON o.delivery_partner_id = u.id
                 WHERE o.id = ? AND o.is_emergency = 1"
            );
            $stmt->execute([$orderId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                return ['error' => 'Emergency order not found'];
            }

            // Determine current stage
            $stage = $this->determineOrderStage($order);

            // Build timestamps array
            $timestamps = [
                'order_placed' => $order['created_at'],
                'acknowledged' => $order['emergency_acknowledged_at'],
                'ready_for_pickup' => $order['emergency_ready_at'],
                'picked_up' => $order['emergency_picked_up_at'],
                'delivered' => $order['emergency_delivered_at'],
            ];

            // Get rider info
            $riderInfo = null;
            if ($order['delivery_partner_id']) {
                $riderInfo = [
                    'id' => (int) $order['delivery_partner_id'],
                    'name' => $order['rider_name'],
                    'phone' => $order['rider_phone'],
                ];
            }

            // Get ETA
            $eta = $this->calculateETA($orderId);

            // Get SLA progress
            $slaProgress = $this->getSLAProgress($orderId);

            // Build response
            return [
                'order_id' => (int) $order['id'],
                'current_stage' => $stage,
                'timestamps' => $timestamps,
                'rider' => $riderInfo,
                'eta' => $eta,
                'sla' => [
                    'deadline' => $order['sla_deadline'],
                    'progress_percentage' => $slaProgress['progress_percentage'],
                    'elapsed_seconds' => $slaProgress['elapsed_seconds'],
                    'remaining_seconds' => $slaProgress['remaining_seconds'],
                    'status' => $slaProgress['sla_status'],
                    'is_breached' => $order['sla_breach'] == 1,
                ],
                'is_delayed' => $eta ? $eta['is_delayed'] : false,
                'gps_stale' => $eta ? $eta['is_stale'] : null,
            ];
        } catch (\Exception $e) {
            error_log("PriorityEngine::getEmergencyOrderStatus error: " . $e->getMessage());
            return ['error' => 'Failed to retrieve emergency order status'];
        }
    }

    /**
     * Determine the current stage of an emergency order based on its timestamps.
     *
     * @param array $order Order data with timestamp fields
     * @return string Current stage name
     */
    private function determineOrderStage(array $order): string
    {
        // Delivered
        if ($order['emergency_delivered_at'] !== null) {
            return 'Delivered';
        }

        // Picked Up / Rider En Route to You
        if ($order['emergency_picked_up_at'] !== null) {
            return 'Rider En Route to You';
        }

        // Ready for Pickup / Rider En Route to Vendor
        if ($order['emergency_ready_at'] !== null) {
            if ($order['delivery_partner_id'] !== null) {
                return 'Rider En Route to Vendor';
            }
            return 'Ready for Pickup';
        }

        // Preparing (acknowledged)
        if ($order['emergency_acknowledged_at'] !== null) {
            return 'Preparing';
        }

        // Rider assigned but not acknowledged by vendor yet
        if ($order['delivery_partner_id'] !== null) {
            return 'Confirmed';
        }

        // Order placed, nothing else happened
        return 'Confirmed';
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private helper methods
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Find the best eligible partner within an optional radius constraint.
     *
     * @param float $lat Center latitude
     * @param float $lng Center longitude
     * @param float|null $radiusKm Maximum distance (null = no limit)
     * @param int $excludePartnerId Partner ID to exclude from results
     * @return array|null Best matching partner or null
     */
    private function findPartnerInRadius(float $lat, float $lng, ?float $radiusKm, int $excludePartnerId): ?array
    {
        $partners = $this->queryEligiblePartners($excludePartnerId);

        $candidates = [];
        foreach ($partners as $partner) {
            if ($partner['latitude'] === null || $partner['longitude'] === null) {
                continue;
            }

            $distance = $this->calculateHaversineDistance(
                $lat, $lng,
                (float) $partner['latitude'],
                (float) $partner['longitude']
            );

            // Apply radius filter if specified
            if ($radiusKm !== null && $distance > $radiusKm) {
                continue;
            }

            $partner['distance_km'] = round($distance, 2);
            $candidates[] = $partner;
        }

        if (empty($candidates)) {
            return null;
        }

        // Sort by distance, with idle-time tiebreaker for equal distances (within 0.01km)
        usort($candidates, function ($a, $b) {
            $distDiff = $a['distance_km'] - $b['distance_km'];

            // If distances are within 0.01km tolerance, use idle time as tiebreaker
            if (abs($distDiff) <= 0.01) {
                // Earlier last_idle_since = longer idle = higher priority
                $idleA = $a['last_idle_since'] ?? '9999-12-31 23:59:59';
                $idleB = $b['last_idle_since'] ?? '9999-12-31 23:59:59';
                return strcmp($idleA, $idleB);
            }

            return $distDiff <=> 0;
        });

        return $candidates[0];
    }

    /**
     * Query all eligible delivery partners from the database.
     *
     * Eligible means:
     * - role = 'delivery'
     * - is_online = 1
     * - emergency_locked_order_id IS NULL
     * - is_non_responsive = 0
     * - Has valid coordinates (latitude and longitude are not null)
     *
     * @param int $excludePartnerId Partner ID to exclude
     * @return array List of eligible partner records
     */
    private function queryEligiblePartners(int $excludePartnerId = 0): array
    {
        $sql = "SELECT id, name, latitude, longitude, last_idle_since
                FROM users
                WHERE role = 'delivery'
                  AND is_online = 1
                  AND emergency_locked_order_id IS NULL
                  AND is_non_responsive = 0
                  AND latitude IS NOT NULL
                  AND longitude IS NOT NULL";

        $params = [];

        if ($excludePartnerId > 0) {
            $sql .= " AND id != ?";
            $params[] = $excludePartnerId;
        }

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("PriorityEngine::queryEligiblePartners error: " . $e->getMessage());
            return [];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Emergency Order Assignment Logic (Task 2.2)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Process emergency order assignment.
     *
     * Sets SLA deadline to created_at + 40 minutes, finds the nearest eligible
     * delivery partner, locks them to the order, and logs the assignment to
     * the emergency_assignment_log table.
     *
     * If no partner is available, the order is added to the retry queue.
     *
     * @param int $orderId The emergency order ID
     * @return array Result with success status and assignment details
     */
    public function processEmergencyAssignment(int $orderId): array
    {
        try {
            // Fetch the order details
            $stmt = $this->db->prepare(
                "SELECT id, vendor_id, created_at, sla_deadline, delivery_partner_id, status
                 FROM orders
                 WHERE id = ? AND is_emergency = 1"
            );
            $stmt->execute([$orderId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                return [
                    'success' => false,
                    'error' => 'Order not found or not an emergency order',
                    'code' => 'EMERGENCY_ORDER_NOT_FOUND'
                ];
            }

            // Check if order already has an active assignment
            if (!empty($order['delivery_partner_id'])) {
                return [
                    'success' => false,
                    'error' => 'Order already has an active assignment',
                    'code' => 'EMERGENCY_ALREADY_ASSIGNED'
                ];
            }

            // Set SLA deadline = created_at + 40 minutes
            $createdAt = new \DateTime($order['created_at']);
            $slaDeadline = clone $createdAt;
            $slaDeadline->modify('+40 minutes');
            $slaDeadlineStr = $slaDeadline->format('Y-m-d H:i:s');

            $updateSla = $this->db->prepare(
                "UPDATE orders SET sla_deadline = ? WHERE id = ?"
            );
            $updateSla->execute([$slaDeadlineStr, $orderId]);

            // Get vendor location
            $vendorStmt = $this->db->prepare(
                "SELECT latitude, longitude FROM users WHERE id = ?"
            );
            $vendorStmt->execute([$order['vendor_id']]);
            $vendor = $vendorStmt->fetch(PDO::FETCH_ASSOC);

            if (!$vendor || $vendor['latitude'] === null || $vendor['longitude'] === null) {
                // Cannot assign without vendor location, add to retry queue
                $this->addToRetryQueue($orderId);
                return [
                    'success' => false,
                    'error' => 'Vendor location not available',
                    'code' => 'EMERGENCY_NO_PARTNER_AVAILABLE'
                ];
            }

            $vendorLat = (float) $vendor['latitude'];
            $vendorLng = (float) $vendor['longitude'];

            // Find nearest eligible partner (excludePartnerId = 0 for initial assignment)
            $partner = $this->findNearestEligiblePartner($vendorLat, $vendorLng, 0);

            if ($partner === null) {
                // No partner available, add to retry queue
                $this->addToRetryQueue($orderId);

                // Notify admin about unassigned emergency order
                $this->notificationService->send(
                    1, // Admin user ID
                    'Emergency Order Unassigned',
                    "Emergency order #{$orderId} has no available delivery partner. Added to retry queue."
                );

                return [
                    'success' => false,
                    'error' => 'No eligible delivery partner available',
                    'code' => 'EMERGENCY_NO_PARTNER_AVAILABLE',
                    'retry_queued' => true
                ];
            }

            // Lock the partner to this order
            $locked = $this->lockPartner((int) $partner['id'], $orderId);

            if (!$locked) {
                // Partner was locked by another process (race condition), retry with exclusion
                $partner = $this->findNearestEligiblePartner($vendorLat, $vendorLng, (int) $partner['id']);

                if ($partner === null) {
                    $this->addToRetryQueue($orderId);
                    return [
                        'success' => false,
                        'error' => 'No eligible delivery partner available after lock conflict',
                        'code' => 'EMERGENCY_NO_PARTNER_AVAILABLE',
                        'retry_queued' => true
                    ];
                }

                $locked = $this->lockPartner((int) $partner['id'], $orderId);
                if (!$locked) {
                    $this->addToRetryQueue($orderId);
                    return [
                        'success' => false,
                        'error' => 'Unable to lock delivery partner',
                        'code' => 'EMERGENCY_PARTNER_LOCKED',
                        'retry_queued' => true
                    ];
                }
            }

            // Assign partner to the order
            $assignStmt = $this->db->prepare(
                "UPDATE orders 
                 SET delivery_partner_id = ?, status = 'confirmed'
                 WHERE id = ?"
            );
            $assignStmt->execute([$partner['id'], $orderId]);

            // Record assignment metadata in emergency_assignment_log
            $assignmentTimestamp = date('Y-m-d H:i:s');
            $riderDistanceKm = $partner['distance_km'];
            $partnerLat = (float) $partner['latitude'];
            $partnerLng = (float) $partner['longitude'];

            $logStmt = $this->db->prepare(
                "INSERT INTO emergency_assignment_log 
                 (order_id, partner_id, assignment_timestamp, rider_distance_km, sla_deadline,
                  vendor_lat, vendor_lng, partner_lat, partner_lng, assignment_type, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'initial', 'assigned')"
            );
            $logStmt->execute([
                $orderId,
                $partner['id'],
                $assignmentTimestamp,
                $riderDistanceKm,
                $slaDeadlineStr,
                $vendorLat,
                $vendorLng,
                $partnerLat,
                $partnerLng
            ]);

            // Notify the delivery partner
            $this->notificationService->send(
                (int) $partner['id'],
                'Emergency Order Assigned',
                "Emergency order #{$orderId} assigned to you. Please accept within 90 seconds. SLA deadline: {$slaDeadlineStr}"
            );

            // Notify admin of successful assignment
            $this->notificationService->send(
                1,
                'Emergency Order Assigned',
                "Emergency order #{$orderId} assigned to partner {$partner['name']} ({$riderDistanceKm}km away). SLA: {$slaDeadlineStr}"
            );

            // Log warning if partner is beyond preferred radius
            if (!empty($partner['beyond_radius'])) {
                error_log("PriorityEngine: Emergency order #{$orderId} assigned to partner beyond 5km radius ({$riderDistanceKm}km)");
            }

            return [
                'success' => true,
                'order_id' => $orderId,
                'partner_id' => (int) $partner['id'],
                'partner_name' => $partner['name'],
                'distance_km' => $riderDistanceKm,
                'sla_deadline' => $slaDeadlineStr,
                'assignment_timestamp' => $assignmentTimestamp,
                'beyond_radius' => !empty($partner['beyond_radius'])
            ];

        } catch (\Exception $e) {
            error_log("PriorityEngine::processEmergencyAssignment error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Internal error during assignment',
                'code' => 'INTERNAL_ERROR'
            ];
        }
    }

    /**
     * Check for partner acceptance timeout (90 seconds).
     *
     * If a partner hasn't accepted within 90 seconds of assignment, revoke
     * the assignment, mark the partner as non-responsive, and re-run
     * assignment excluding that partner.
     *
     * @param int $orderId The emergency order ID
     * @return array Result with action taken
     */
    public function checkPartnerAcceptanceTimeout(int $orderId): array
    {
        try {
            // Find the current active assignment for this order
            $stmt = $this->db->prepare(
                "SELECT eal.id, eal.partner_id, eal.assignment_timestamp, eal.vendor_lat, eal.vendor_lng
                 FROM emergency_assignment_log eal
                 WHERE eal.order_id = ? AND eal.status = 'assigned'
                 ORDER BY eal.assignment_timestamp DESC
                 LIMIT 1"
            );
            $stmt->execute([$orderId]);
            $assignment = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$assignment) {
                return [
                    'success' => false,
                    'error' => 'No active assignment found for this order',
                    'action' => 'none'
                ];
            }

            // Check if 90 seconds have elapsed since assignment
            $assignedAt = new \DateTime($assignment['assignment_timestamp']);
            $now = new \DateTime();
            $elapsed = $now->getTimestamp() - $assignedAt->getTimestamp();

            if ($elapsed < 90) {
                return [
                    'success' => true,
                    'action' => 'none',
                    'elapsed_seconds' => $elapsed,
                    'remaining_seconds' => 90 - $elapsed
                ];
            }

            // Timeout exceeded: revoke assignment
            $partnerId = (int) $assignment['partner_id'];

            // Update assignment log to 'timeout'
            $revokeStmt = $this->db->prepare(
                "UPDATE emergency_assignment_log 
                 SET status = 'timeout', revoked_at = NOW(), revoke_reason = 'Partner did not accept within 90 seconds'
                 WHERE id = ?"
            );
            $revokeStmt->execute([$assignment['id']]);

            // Unlock the partner from this order
            $this->unlockPartner($partnerId);

            // Mark partner as non-responsive
            $nonResponsiveStmt = $this->db->prepare(
                "UPDATE users SET is_non_responsive = 1 WHERE id = ?"
            );
            $nonResponsiveStmt->execute([$partnerId]);

            // Remove partner assignment from order
            $orderStmt = $this->db->prepare(
                "UPDATE orders 
                 SET delivery_partner_id = NULL, reassignment_count = reassignment_count + 1
                 WHERE id = ?"
            );
            $orderStmt->execute([$orderId]);

            // Notify the timed-out partner
            $this->notificationService->send(
                $partnerId,
                'Emergency Assignment Revoked',
                "Your assignment for emergency order #{$orderId} was revoked due to non-acceptance within 90 seconds."
            );

            // Check reassignment limit (max 3 attempts per Requirement 4.4)
            $countStmt = $this->db->prepare(
                "SELECT reassignment_count FROM orders WHERE id = ?"
            );
            $countStmt->execute([$orderId]);
            $orderData = $countStmt->fetch(PDO::FETCH_ASSOC);

            if ($orderData && (int) $orderData['reassignment_count'] >= 3) {
                // Max reassignments reached, add to retry queue
                $this->addToRetryQueue($orderId);

                $this->notificationService->send(
                    1,
                    'Emergency Order Max Reassignments',
                    "Emergency order #{$orderId} has reached maximum reassignment attempts (3). Added to retry queue."
                );

                return [
                    'success' => true,
                    'action' => 'max_reassignments_reached',
                    'timed_out_partner_id' => $partnerId,
                    'retry_queued' => true
                ];
            }

            // Re-run assignment excluding the timed-out partner
            $vendorLat = (float) $assignment['vendor_lat'];
            $vendorLng = (float) $assignment['vendor_lng'];

            $newPartner = $this->findNearestEligiblePartner($vendorLat, $vendorLng, $partnerId);

            if ($newPartner === null) {
                // No other partner available, add to retry queue
                $this->addToRetryQueue($orderId);

                $this->notificationService->send(
                    1,
                    'Emergency Order Reassignment Failed',
                    "Emergency order #{$orderId}: No available partner after timeout of partner #{$partnerId}. Added to retry queue."
                );

                return [
                    'success' => true,
                    'action' => 'reassignment_failed_no_partner',
                    'timed_out_partner_id' => $partnerId,
                    'retry_queued' => true
                ];
            }

            // Lock and assign the new partner
            $locked = $this->lockPartner((int) $newPartner['id'], $orderId);

            if (!$locked) {
                $this->addToRetryQueue($orderId);
                return [
                    'success' => true,
                    'action' => 'reassignment_failed_lock',
                    'timed_out_partner_id' => $partnerId,
                    'retry_queued' => true
                ];
            }

            // Update order with new partner
            $reassignStmt = $this->db->prepare(
                "UPDATE orders SET delivery_partner_id = ? WHERE id = ?"
            );
            $reassignStmt->execute([$newPartner['id'], $orderId]);

            // Get SLA deadline for the log entry
            $slaStmt = $this->db->prepare("SELECT sla_deadline FROM orders WHERE id = ?");
            $slaStmt->execute([$orderId]);
            $slaData = $slaStmt->fetch(PDO::FETCH_ASSOC);
            $slaDeadline = $slaData['sla_deadline'] ?? date('Y-m-d H:i:s', strtotime('+40 minutes'));

            // Log the reassignment
            $logStmt = $this->db->prepare(
                "INSERT INTO emergency_assignment_log 
                 (order_id, partner_id, assignment_timestamp, rider_distance_km, sla_deadline,
                  vendor_lat, vendor_lng, partner_lat, partner_lng, assignment_type, status)
                 VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, ?, 'reassignment', 'assigned')"
            );
            $logStmt->execute([
                $orderId,
                $newPartner['id'],
                $newPartner['distance_km'],
                $slaDeadline,
                $vendorLat,
                $vendorLng,
                (float) $newPartner['latitude'],
                (float) $newPartner['longitude']
            ]);

            // Notify the new partner
            $this->notificationService->send(
                (int) $newPartner['id'],
                'Emergency Order Assigned',
                "Emergency order #{$orderId} assigned to you (reassignment). Please accept within 90 seconds."
            );

            // Notify admin of reassignment
            $this->notificationService->send(
                1,
                'Emergency Order Reassigned',
                "Emergency order #{$orderId} reassigned from partner #{$partnerId} to partner {$newPartner['name']} due to acceptance timeout."
            );

            return [
                'success' => true,
                'action' => 'reassigned',
                'timed_out_partner_id' => $partnerId,
                'new_partner_id' => (int) $newPartner['id'],
                'new_partner_name' => $newPartner['name'],
                'new_distance_km' => $newPartner['distance_km']
            ];

        } catch (\Exception $e) {
            error_log("PriorityEngine::checkPartnerAcceptanceTimeout error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Internal error during timeout check',
                'action' => 'error'
            ];
        }
    }

    /**
     * Add an emergency order to the retry queue.
     *
     * Orders in the retry queue are retried every 60 seconds for a maximum
     * of 5 attempts. If the order is already in the queue, the existing
     * entry is preserved.
     *
     * @param int $orderId The emergency order ID
     * @return bool True if successfully added or already exists
     */
    public function addToRetryQueue(int $orderId): bool
    {
        try {
            // Check if already in queue
            $checkStmt = $this->db->prepare(
                "SELECT id, status, attempt_count FROM emergency_retry_queue WHERE order_id = ?"
            );
            $checkStmt->execute([$orderId]);
            $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                // If already exhausted, don't re-add
                if ($existing['status'] === 'exhausted') {
                    return false;
                }
                // Already in queue and pending, no action needed
                return true;
            }

            // Insert into retry queue with next attempt in 60 seconds
            $nextAttempt = date('Y-m-d H:i:s', strtotime('+60 seconds'));
            $stmt = $this->db->prepare(
                "INSERT INTO emergency_retry_queue 
                 (order_id, attempt_count, max_attempts, last_attempt_at, next_attempt_at, status)
                 VALUES (?, 0, 5, NOW(), ?, 'pending')"
            );
            $stmt->execute([$orderId, $nextAttempt]);

            // Update order retry count
            $orderStmt = $this->db->prepare(
                "UPDATE orders SET assignment_retry_count = assignment_retry_count + 1 WHERE id = ?"
            );
            $orderStmt->execute([$orderId]);

            return true;

        } catch (\Exception $e) {
            error_log("PriorityEngine::addToRetryQueue error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Process the emergency retry queue.
     *
     * Finds all pending retry entries whose next_attempt_at has passed,
     * and attempts to assign a delivery partner. Each entry is retried
     * every 60 seconds for a maximum of 5 attempts.
     *
     * @return array Summary of retry processing results
     */
    public function processRetryQueue(): array
    {
        $results = [
            'processed' => 0,
            'assigned' => 0,
            'retried' => 0,
            'exhausted' => 0,
            'details' => []
        ];

        try {
            // Get all pending retry entries that are due
            $stmt = $this->db->prepare(
                "SELECT erq.id, erq.order_id, erq.attempt_count, erq.max_attempts
                 FROM emergency_retry_queue erq
                 WHERE erq.status = 'pending'
                   AND erq.next_attempt_at <= NOW()
                 ORDER BY erq.created_at ASC"
            );
            $stmt->execute();
            $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($entries as $entry) {
                $results['processed']++;
                $orderId = (int) $entry['order_id'];
                $attemptCount = (int) $entry['attempt_count'] + 1;
                $maxAttempts = (int) $entry['max_attempts'];

                // Check if max attempts reached
                if ($attemptCount > $maxAttempts) {
                    // Mark as exhausted
                    $exhaustStmt = $this->db->prepare(
                        "UPDATE emergency_retry_queue 
                         SET status = 'exhausted', last_attempt_at = NOW()
                         WHERE id = ?"
                    );
                    $exhaustStmt->execute([$entry['id']]);

                    // Notify admin of exhausted retries
                    $this->notificationService->send(
                        1,
                        'Emergency Order Retry Exhausted',
                        "Emergency order #{$orderId} has exhausted all {$maxAttempts} retry attempts. Manual intervention required."
                    );

                    $results['exhausted']++;
                    $results['details'][] = [
                        'order_id' => $orderId,
                        'action' => 'exhausted',
                        'attempts' => $attemptCount - 1
                    ];
                    continue;
                }

                // Attempt assignment
                // First, clear any existing partner assignment to allow fresh assignment
                $clearStmt = $this->db->prepare(
                    "UPDATE orders SET delivery_partner_id = NULL WHERE id = ? AND delivery_partner_id IS NULL"
                );
                $clearStmt->execute([$orderId]);

                // Get vendor location for this order
                $orderStmt = $this->db->prepare(
                    "SELECT o.vendor_id, o.sla_deadline, u.latitude, u.longitude
                     FROM orders o
                     JOIN users u ON u.id = o.vendor_id
                     WHERE o.id = ? AND o.is_emergency = 1"
                );
                $orderStmt->execute([$orderId]);
                $orderData = $orderStmt->fetch(PDO::FETCH_ASSOC);

                if (!$orderData || $orderData['latitude'] === null || $orderData['longitude'] === null) {
                    // Update attempt count and schedule next retry
                    $this->updateRetryAttempt($entry['id'], $attemptCount);
                    $results['retried']++;
                    $results['details'][] = [
                        'order_id' => $orderId,
                        'action' => 'retry_scheduled',
                        'reason' => 'vendor_location_unavailable',
                        'attempt' => $attemptCount
                    ];
                    continue;
                }

                $vendorLat = (float) $orderData['latitude'];
                $vendorLng = (float) $orderData['longitude'];

                // Find nearest eligible partner
                $partner = $this->findNearestEligiblePartner($vendorLat, $vendorLng, 0);

                if ($partner === null) {
                    // No partner available, update attempt and schedule next retry
                    $this->updateRetryAttempt($entry['id'], $attemptCount);

                    // Update order retry count
                    $retryCountStmt = $this->db->prepare(
                        "UPDATE orders SET assignment_retry_count = ? WHERE id = ?"
                    );
                    $retryCountStmt->execute([$attemptCount, $orderId]);

                    $results['retried']++;
                    $results['details'][] = [
                        'order_id' => $orderId,
                        'action' => 'retry_scheduled',
                        'reason' => 'no_partner_available',
                        'attempt' => $attemptCount
                    ];
                    continue;
                }

                // Partner found! Lock and assign
                $locked = $this->lockPartner((int) $partner['id'], $orderId);

                if (!$locked) {
                    // Lock failed, schedule retry
                    $this->updateRetryAttempt($entry['id'], $attemptCount);
                    $results['retried']++;
                    $results['details'][] = [
                        'order_id' => $orderId,
                        'action' => 'retry_scheduled',
                        'reason' => 'lock_failed',
                        'attempt' => $attemptCount
                    ];
                    continue;
                }

                // Assign partner to order
                $assignStmt = $this->db->prepare(
                    "UPDATE orders SET delivery_partner_id = ?, status = 'confirmed' WHERE id = ?"
                );
                $assignStmt->execute([$partner['id'], $orderId]);

                // Get SLA deadline
                $slaDeadline = $orderData['sla_deadline'] ?? date('Y-m-d H:i:s', strtotime('+40 minutes'));

                // Log assignment
                $logStmt = $this->db->prepare(
                    "INSERT INTO emergency_assignment_log 
                     (order_id, partner_id, assignment_timestamp, rider_distance_km, sla_deadline,
                      vendor_lat, vendor_lng, partner_lat, partner_lng, assignment_type, status)
                     VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, ?, 'retry', 'assigned')"
                );
                $logStmt->execute([
                    $orderId,
                    $partner['id'],
                    $partner['distance_km'],
                    $slaDeadline,
                    $vendorLat,
                    $vendorLng,
                    (float) $partner['latitude'],
                    (float) $partner['longitude']
                ]);

                // Mark retry queue entry as assigned
                $assignedStmt = $this->db->prepare(
                    "UPDATE emergency_retry_queue 
                     SET status = 'assigned', last_attempt_at = NOW(), attempt_count = ?
                     WHERE id = ?"
                );
                $assignedStmt->execute([$attemptCount, $entry['id']]);

                // Notify the delivery partner
                $this->notificationService->send(
                    (int) $partner['id'],
                    'Emergency Order Assigned',
                    "Emergency order #{$orderId} assigned to you (retry attempt #{$attemptCount}). Please accept within 90 seconds."
                );

                // Notify admin
                $this->notificationService->send(
                    1,
                    'Emergency Order Assigned (Retry)',
                    "Emergency order #{$orderId} assigned to partner {$partner['name']} on retry attempt #{$attemptCount}."
                );

                $results['assigned']++;
                $results['details'][] = [
                    'order_id' => $orderId,
                    'action' => 'assigned',
                    'partner_id' => (int) $partner['id'],
                    'partner_name' => $partner['name'],
                    'distance_km' => $partner['distance_km'],
                    'attempt' => $attemptCount
                ];
            }

        } catch (\Exception $e) {
            error_log("PriorityEngine::processRetryQueue error: " . $e->getMessage());
            $results['error'] = $e->getMessage();
        }

        return $results;
    }

    /**
     * Update a retry queue entry with the new attempt count and schedule
     * the next retry attempt in 60 seconds.
     *
     * @param int $retryId The retry queue entry ID
     * @param int $attemptCount The current attempt number
     * @return void
     */
    private function updateRetryAttempt(int $retryId, int $attemptCount): void
    {
        $nextAttempt = date('Y-m-d H:i:s', strtotime('+60 seconds'));
        $stmt = $this->db->prepare(
            "UPDATE emergency_retry_queue 
             SET attempt_count = ?, last_attempt_at = NOW(), next_attempt_at = ?
             WHERE id = ?"
        );
        $stmt->execute([$attemptCount, $nextAttempt, $retryId]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SLA Monitoring and Escalation Logic (Task 2.3)
    // Requirements: 6.1, 6.2, 6.3, 6.4, 6.5, 6.6
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Evaluate all active emergency order SLAs.
     *
     * Checks every active emergency order and triggers appropriate escalation:
     * - Warning: <15 min remaining AND not picked up → alert admin
     * - Critical/Auto-reassignment: <10 min remaining AND not picked_up/in_transit → reassign (max 2 attempts)
     * - Breach: SLA exceeded → log to audit trail
     *
     * Called every 60 seconds by the cron endpoint.
     *
     * @return array Summary of actions taken
     */
    public function evaluateActiveSLAs(): array
    {
        $summary = [
            'orders_checked' => 0,
            'warnings_triggered' => 0,
            'reassignments_attempted' => 0,
            'breaches_logged' => 0,
            'errors' => []
        ];

        try {
            // Query all active emergency orders (Requirement 6.1)
            $stmt = $this->db->prepare(
                "SELECT o.id, o.sla_deadline, o.status, o.delivery_partner_id, 
                        o.vendor_id, o.reassignment_count, o.created_at
                 FROM orders o
                 WHERE o.is_emergency = 1
                   AND o.status IN ('placed', 'confirmed', 'dispatched', 'in_transit')
                   AND o.sla_deadline IS NOT NULL
                 ORDER BY o.sla_deadline ASC"
            );
            $stmt->execute();
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $summary['orders_checked'] = count($orders);
            $now = new \DateTime();

            foreach ($orders as $order) {
                $slaDeadline = new \DateTime($order['sla_deadline']);
                $remainingSeconds = $slaDeadline->getTimestamp() - $now->getTimestamp();
                $remainingMinutes = $remainingSeconds / 60.0;

                $orderId = (int) $order['id'];
                $status = $order['status'];
                $partnerId = $order['delivery_partner_id'] ? (int) $order['delivery_partner_id'] : null;
                $reassignmentCount = (int) $order['reassignment_count'];

                // Statuses that indicate the order has progressed past the pickup stage
                $pickedUpOrBeyond = in_array($status, ['picked_up', 'in_transit']);

                // BREACH: SLA deadline exceeded (Requirement 6.4)
                if ($remainingSeconds < 0) {
                    $elapsedSeconds = abs($remainingSeconds);
                    $this->logSLABreach($orderId, $partnerId, $elapsedSeconds, $order['vendor_id']);
                    $summary['breaches_logged']++;
                    continue;
                }

                // AUTO-REASSIGNMENT: <10 min remaining AND not picked_up/in_transit (Requirement 6.3)
                if ($remainingMinutes < 10 && !$pickedUpOrBeyond) {
                    if ($reassignmentCount < 2 && $partnerId !== null) {
                        $reassigned = $this->attemptReassignment(
                            $orderId,
                            "SLA at risk: less than 10 minutes remaining ({$this->formatMinutes($remainingMinutes)} min left)"
                        );
                        if ($reassigned) {
                            $summary['reassignments_attempted']++;
                        }
                    } elseif ($reassignmentCount >= 2) {
                        // Max reassignment attempts reached - escalate for manual intervention (Requirement 6.6)
                        if (!$this->hasUnresolvedEscalation($orderId, 'manual_intervention')) {
                            $this->triggerEscalation(
                                $orderId,
                                "Auto-reassignment limit reached (2 attempts). Less than 10 min remaining. Manual intervention required.",
                                'manual_intervention'
                            );
                            $this->notifyAdminManualIntervention(
                                $orderId,
                                "Reassignment limit reached with less than 10 minutes remaining"
                            );
                        }
                    }
                }

                // WARNING: <15 min remaining AND not picked up (Requirement 6.2)
                if ($remainingMinutes < 15 && !$pickedUpOrBeyond) {
                    if (!$this->hasUnresolvedEscalation($orderId, 'warning')) {
                        $this->triggerEscalation(
                            $orderId,
                            "SLA warning: less than 15 minutes remaining ({$this->formatMinutes($remainingMinutes)} min left). Order not yet picked up.",
                            'warning'
                        );
                        $this->notifyAdminSLAWarning($orderId, $partnerId, $remainingMinutes, $status);
                        $summary['warnings_triggered']++;
                    }
                }
            }
        } catch (\Exception $e) {
            error_log("PriorityEngine::evaluateActiveSLAs error: " . $e->getMessage());
            $summary['errors'][] = $e->getMessage();
        }

        return $summary;
    }

    /**
     * Trigger an escalation event for an emergency order.
     *
     * Creates a record in the `emergency_escalations` table with the specified
     * escalation level and reason.
     *
     * @param int $orderId The emergency order ID
     * @param string $reason Human-readable reason for the escalation
     * @param string $level Escalation level: 'warning', 'critical', 'breach', 'manual_intervention'
     * @return bool True if escalation record was created successfully
     */
    public function triggerEscalation(int $orderId, string $reason, string $level): bool
    {
        try {
            // Get order details for context
            $stmt = $this->db->prepare(
                "SELECT delivery_partner_id, vendor_id, sla_deadline
                 FROM orders WHERE id = ?"
            );
            $stmt->execute([$orderId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                error_log("PriorityEngine::triggerEscalation - Order not found: {$orderId}");
                return false;
            }

            $now = new \DateTime();
            $remainingMinutes = null;

            if ($order['sla_deadline']) {
                $slaDeadline = new \DateTime($order['sla_deadline']);
                $remainingSeconds = $slaDeadline->getTimestamp() - $now->getTimestamp();
                $remainingMinutes = round($remainingSeconds / 60.0, 1);
            }

            $insertStmt = $this->db->prepare(
                "INSERT INTO emergency_escalations 
                    (order_id, escalation_level, reason, triggered_at, partner_id, vendor_id, remaining_minutes)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $insertStmt->execute([
                $orderId,
                $level,
                $reason,
                $now->format('Y-m-d H:i:s'),
                $order['delivery_partner_id'],
                $order['vendor_id'],
                $remainingMinutes
            ]);

            return true;
        } catch (\Exception $e) {
            error_log("PriorityEngine::triggerEscalation error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Attempt automatic reassignment of an emergency order to a closer delivery partner.
     *
     * This method:
     * 1. Revokes the current assignment (unlocks current partner)
     * 2. Finds the next nearest eligible partner (excluding current)
     * 3. Assigns and locks the new partner
     * 4. Logs the reassignment in emergency_assignment_log
     * 5. Sends three-party notification (original partner, new partner, admin) (Requirement 6.5)
     * 6. Increments the order's reassignment_count
     *
     * If no closer partner is available, escalates to admin for manual intervention (Requirement 6.6).
     *
     * @param int $orderId The emergency order ID
     * @param string $reason Reason for the reassignment
     * @return bool True if reassignment was successful
     */
    public function attemptReassignment(int $orderId, string $reason): bool
    {
        try {
            // Get current order and partner details
            $stmt = $this->db->prepare(
                "SELECT o.id, o.delivery_partner_id, o.vendor_id, o.sla_deadline, o.reassignment_count
                 FROM orders o
                 WHERE o.id = ? AND o.is_emergency = 1"
            );
            $stmt->execute([$orderId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                error_log("PriorityEngine::attemptReassignment - Order not found: {$orderId}");
                return false;
            }

            $currentPartnerId = $order['delivery_partner_id'] ? (int) $order['delivery_partner_id'] : null;
            $reassignmentCount = (int) $order['reassignment_count'];

            // Check max reassignment limit (2 attempts for SLA-risk reassignment)
            if ($reassignmentCount >= 2) {
                $this->triggerEscalation(
                    $orderId,
                    "Reassignment limit reached (2 attempts). Reason: {$reason}",
                    'manual_intervention'
                );
                return false;
            }

            // Get vendor location
            $vendorStmt = $this->db->prepare(
                "SELECT latitude, longitude FROM users WHERE id = ?"
            );
            $vendorStmt->execute([$order['vendor_id']]);
            $vendor = $vendorStmt->fetch(PDO::FETCH_ASSOC);

            if (!$vendor || $vendor['latitude'] === null || $vendor['longitude'] === null) {
                error_log("PriorityEngine::attemptReassignment - Vendor location unavailable for order {$orderId}");
                return false;
            }

            $vendorLat = (float) $vendor['latitude'];
            $vendorLng = (float) $vendor['longitude'];

            // Find next nearest eligible partner excluding current
            $excludeId = $currentPartnerId ?? 0;
            $newPartner = $this->findNearestEligiblePartner($vendorLat, $vendorLng, $excludeId);

            if ($newPartner === null) {
                // No available partner - escalate to admin for manual intervention (Requirement 6.6)
                $this->triggerEscalation(
                    $orderId,
                    "No available delivery partner for reassignment. Reason: {$reason}. Retaining current partner.",
                    'manual_intervention'
                );
                $this->notifyAdminManualIntervention($orderId, $reason);
                return false;
            }

            $newPartnerId = (int) $newPartner['id'];

            // Revoke current assignment in assignment log
            if ($currentPartnerId) {
                $this->revokeCurrentAssignment($orderId, $currentPartnerId, $reason);
                // Unlock current partner
                $this->unlockPartner($currentPartnerId);
            }

            // Lock new partner to this order
            $locked = $this->lockPartner($newPartnerId, $orderId);
            if (!$locked) {
                error_log("PriorityEngine::attemptReassignment - Failed to lock new partner {$newPartnerId} for order {$orderId}");
                return false;
            }

            // Update order with new partner and increment reassignment count
            $updateStmt = $this->db->prepare(
                "UPDATE orders 
                 SET delivery_partner_id = ?, reassignment_count = reassignment_count + 1
                 WHERE id = ?"
            );
            $updateStmt->execute([$newPartnerId, $orderId]);

            // Log the new assignment in emergency_assignment_log
            $this->logReassignment(
                $orderId,
                $newPartnerId,
                $newPartner['distance_km'],
                $order['sla_deadline'],
                $vendorLat,
                $vendorLng,
                $newPartner
            );

            // Trigger critical escalation record
            $this->triggerEscalation(
                $orderId,
                "Auto-reassigned from partner #{$currentPartnerId} to partner #{$newPartnerId}. Reason: {$reason}",
                'critical'
            );

            // Send three-party notification (Requirement 6.5)
            if ($currentPartnerId) {
                $this->sendReassignmentNotifications($orderId, $currentPartnerId, $newPartnerId, $reason);
            } else {
                // No original partner, just notify new partner and admin
                $this->notificationService->send(
                    $newPartnerId,
                    "🚨 New Emergency Order #{$orderId} Assigned",
                    "You have been assigned emergency order #{$orderId}. This is a priority delivery with an active SLA deadline. Please accept immediately."
                );
                $adminId = $this->getAdminUserId();
                if ($adminId) {
                    $this->notificationService->send(
                        $adminId,
                        "Emergency Order #{$orderId} - Assigned",
                        "Emergency order #{$orderId} assigned to partner #{$newPartnerId}. Reason: {$reason}"
                    );
                }
            }

            return true;
        } catch (\Exception $e) {
            error_log("PriorityEngine::attemptReassignment error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Log an SLA breach to the audit trail.
     *
     * Creates a breach-level escalation record with order_id, partner_id,
     * elapsed_time, and reason. Also marks the order as SLA breached.
     *
     * @param int $orderId The emergency order ID
     * @param int|null $partnerId The assigned delivery partner ID (or null if unassigned)
     * @param int $elapsedSeconds Seconds elapsed beyond the SLA deadline
     * @param int|null $vendorId The vendor ID
     * @return bool True if breach was logged successfully
     */
    private function logSLABreach(int $orderId, ?int $partnerId, int $elapsedSeconds, ?int $vendorId): bool
    {
        try {
            // Check if breach already logged for this order (avoid duplicates)
            $checkStmt = $this->db->prepare(
                "SELECT id FROM emergency_escalations 
                 WHERE order_id = ? AND escalation_level = 'breach' LIMIT 1"
            );
            $checkStmt->execute([$orderId]);

            if ($checkStmt->fetch()) {
                // Breach already logged, skip duplicate
                return true;
            }

            $elapsedMinutes = round($elapsedSeconds / 60.0, 1);
            $reason = "SLA breached. Elapsed time beyond deadline: {$elapsedMinutes} minutes "
                    . "({$elapsedSeconds} seconds). "
                    . ($partnerId ? "Assigned partner ID: {$partnerId}." : "No partner assigned.");

            $now = new \DateTime();
            $insertStmt = $this->db->prepare(
                "INSERT INTO emergency_escalations 
                    (order_id, escalation_level, reason, triggered_at, partner_id, vendor_id, remaining_minutes, action_taken)
                 VALUES (?, 'breach', ?, ?, ?, ?, ?, 'breach_logged')"
            );
            $insertStmt->execute([
                $orderId,
                $reason,
                $now->format('Y-m-d H:i:s'),
                $partnerId,
                $vendorId,
                -$elapsedMinutes // Negative indicates time past deadline
            ]);

            // Mark order as SLA breached
            $updateStmt = $this->db->prepare(
                "UPDATE orders SET sla_breach = 1 WHERE id = ?"
            );
            $updateStmt->execute([$orderId]);

            // Notify admin of breach
            $adminId = $this->getAdminUserId();
            if ($adminId) {
                $this->notificationService->send(
                    $adminId,
                    "SLA BREACH - Order #{$orderId}",
                    "Emergency order #{$orderId} has breached its SLA deadline by {$elapsedMinutes} minutes. "
                    . ($partnerId ? "Partner #{$partnerId} assigned." : "No partner assigned. Immediate intervention required.")
                );
            }

            return true;
        } catch (\Exception $e) {
            error_log("PriorityEngine::logSLABreach error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send three-party notifications on reassignment (Requirement 6.5).
     *
     * Notifies:
     * 1. Original delivery partner (assignment revoked)
     * 2. New delivery partner (new emergency assignment)
     * 3. Admin (reassignment occurred with reason)
     *
     * @param int $orderId The emergency order ID
     * @param int $originalPartnerId The original delivery partner's user ID
     * @param int $newPartnerId The new delivery partner's user ID
     * @param string $reason Reason for the reassignment
     */
    private function sendReassignmentNotifications(int $orderId, int $originalPartnerId, int $newPartnerId, string $reason): void
    {
        // Notify original partner - assignment revoked
        $this->notificationService->send(
            $originalPartnerId,
            "Emergency Order #{$orderId} - Reassigned",
            "Your emergency order #{$orderId} has been reassigned to another partner. Reason: {$reason}"
        );

        // Notify new partner - new emergency assignment
        $this->notificationService->send(
            $newPartnerId,
            "New Emergency Order #{$orderId} Assigned",
            "You have been assigned emergency order #{$orderId}. This is a priority delivery with an active SLA deadline. Please accept immediately."
        );

        // Notify admin - reassignment occurred
        $adminId = $this->getAdminUserId();
        if ($adminId) {
            $this->notificationService->send(
                $adminId,
                "Emergency Order #{$orderId} - Reassigned",
                "Emergency order #{$orderId} reassigned from partner #{$originalPartnerId} to partner #{$newPartnerId}. Reason: {$reason}"
            );
        }
    }

    /**
     * Notify admin of an SLA warning for an emergency order (Requirement 6.2).
     *
     * @param int $orderId The emergency order ID
     * @param int|null $partnerId The assigned partner ID
     * @param float $remainingMinutes Minutes remaining before SLA deadline
     * @param string $status Current order status
     */
    private function notifyAdminSLAWarning(int $orderId, ?int $partnerId, float $remainingMinutes, string $status): void
    {
        $adminId = $this->getAdminUserId();
        if (!$adminId) {
            return;
        }

        $formattedMinutes = $this->formatMinutes($remainingMinutes);
        $partnerInfo = $partnerId ? "Partner #{$partnerId}" : "Unassigned";

        $this->notificationService->send(
            $adminId,
            "SLA Warning - Order #{$orderId}",
            "Emergency order #{$orderId} has {$formattedMinutes} min remaining. "
            . "Status: {$status}. Rider: {$partnerInfo}. Order not yet picked up."
        );
    }

    /**
     * Notify admin that manual intervention is required for an emergency order (Requirement 6.6).
     *
     * @param int $orderId The emergency order ID
     * @param string $reason Reason manual intervention is needed
     */
    private function notifyAdminManualIntervention(int $orderId, string $reason): void
    {
        $adminId = $this->getAdminUserId();
        if (!$adminId) {
            return;
        }

        $this->notificationService->send(
            $adminId,
            "Manual Intervention Required - Order #{$orderId}",
            "Emergency order #{$orderId} requires manual intervention. Reason: {$reason}. "
            . "No suitable partner available for automatic reassignment."
        );
    }

    /**
     * Revoke the current assignment for an order in the assignment log.
     *
     * @param int $orderId The emergency order ID
     * @param int $partnerId The partner whose assignment is being revoked
     * @param string $reason Reason for revocation
     */
    private function revokeCurrentAssignment(int $orderId, int $partnerId, string $reason): void
    {
        try {
            $now = new \DateTime();
            $stmt = $this->db->prepare(
                "UPDATE emergency_assignment_log 
                 SET status = 'revoked', revoked_at = ?, revoke_reason = ?
                 WHERE order_id = ? AND partner_id = ? AND status IN ('assigned', 'accepted')
                 ORDER BY assignment_timestamp DESC LIMIT 1"
            );
            $stmt->execute([
                $now->format('Y-m-d H:i:s'),
                $reason,
                $orderId,
                $partnerId
            ]);
        } catch (\Exception $e) {
            error_log("PriorityEngine::revokeCurrentAssignment error: " . $e->getMessage());
        }
    }

    /**
     * Log a reassignment event in the emergency_assignment_log table.
     *
     * @param int $orderId The emergency order ID
     * @param int $newPartnerId The new partner's user ID
     * @param float $distanceKm Distance from partner to vendor in km
     * @param string|null $slaDeadline The SLA deadline datetime string
     * @param float $vendorLat Vendor latitude
     * @param float $vendorLng Vendor longitude
     * @param array $partnerData Partner data including latitude/longitude
     */
    private function logReassignment(int $orderId, int $newPartnerId, float $distanceKm, ?string $slaDeadline, float $vendorLat, float $vendorLng, array $partnerData): void
    {
        try {
            $now = new \DateTime();
            $stmt = $this->db->prepare(
                "INSERT INTO emergency_assignment_log 
                    (order_id, partner_id, assignment_timestamp, rider_distance_km, sla_deadline, 
                     vendor_lat, vendor_lng, partner_lat, partner_lng, assignment_type, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'reassignment', 'assigned')"
            );
            $stmt->execute([
                $orderId,
                $newPartnerId,
                $now->format('Y-m-d H:i:s'),
                $distanceKm,
                $slaDeadline ?? date('Y-m-d H:i:s', strtotime('+40 minutes')),
                $vendorLat,
                $vendorLng,
                $partnerData['latitude'] ?? null,
                $partnerData['longitude'] ?? null
            ]);
        } catch (\Exception $e) {
            error_log("PriorityEngine::logReassignment error: " . $e->getMessage());
        }
    }

    /**
     * Check if an unresolved escalation of a given level already exists for an order.
     * Prevents duplicate escalation records for the same condition.
     *
     * @param int $orderId The emergency order ID
     * @param string $level The escalation level to check
     * @return bool True if an unresolved escalation exists
     */
    private function hasUnresolvedEscalation(int $orderId, string $level): bool
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id FROM emergency_escalations 
                 WHERE order_id = ? AND escalation_level = ? AND resolved_at IS NULL
                 LIMIT 1"
            );
            $stmt->execute([$orderId, $level]);
            return $stmt->fetch() !== false;
        } catch (\Exception $e) {
            error_log("PriorityEngine::hasUnresolvedEscalation error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the admin user ID for notifications.
     *
     * @return int|null Admin user ID or null if not found
     */
    private function getAdminUserId(): ?int
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id FROM users WHERE role = 'admin' LIMIT 1"
            );
            $stmt->execute();
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            return $admin ? (int) $admin['id'] : null;
        } catch (\Exception $e) {
            error_log("PriorityEngine::getAdminUserId error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Format remaining minutes to a readable string.
     *
     * @param float $minutes Minutes value
     * @return string Formatted string (e.g., "8.5")
     */
    private function formatMinutes(float $minutes): string
    {
        return number_format(max(0, $minutes), 1);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Vendor Preparation Deadline Escalation Logic (Task 5.4)
    // Requirements: 3.3, 3.8
    // ─────────────────────────────────────────────────────────────────────────

    /** Vendor preparation deadline in minutes */
    private const VENDOR_PREPARATION_DEADLINE_MINUTES = 5;

    /**
     * Check vendor preparation deadlines for all active emergency orders.
     *
     * Evaluates each active emergency order to determine if the vendor has:
     * 1. Failed to acknowledge within the 5-minute preparation deadline → escalate to admin
     * 2. Acknowledged but not marked "ready for pickup" after deadline → escalate and flag as overdue
     *
     * This method is designed to be called by the SLA cron check every 60 seconds.
     *
     * @return array Summary of vendor deadline checks and escalations triggered
     */
    public function checkVendorPreparationDeadlines(): array
    {
        $summary = [
            'orders_checked' => 0,
            'acknowledgment_escalations' => 0,
            'ready_escalations' => 0,
            'errors' => []
        ];

        try {
            // Query all active emergency orders that haven't been picked up yet
            $stmt = $this->db->prepare(
                "SELECT o.id, o.vendor_id, o.created_at, o.status,
                        o.emergency_acknowledged_at, o.emergency_ready_at,
                        o.delivery_partner_id,
                        v.name AS vendor_name
                 FROM orders o
                 LEFT JOIN users v ON o.vendor_id = v.id
                 WHERE o.is_emergency = 1
                   AND o.status IN ('placed', 'confirmed', 'dispatched', 'processing', 'accepted')
                   AND o.emergency_picked_up_at IS NULL
                 ORDER BY o.created_at ASC"
            );
            $stmt->execute();
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $summary['orders_checked'] = count($orders);
            $now = time();

            foreach ($orders as $order) {
                $orderId = (int) $order['id'];
                $vendorId = (int) $order['vendor_id'];
                $createdAt = strtotime($order['created_at']);
                $prepDeadline = $createdAt + (self::VENDOR_PREPARATION_DEADLINE_MINUTES * 60);
                $vendorName = $order['vendor_name'] ?? 'Unknown Vendor';

                // Only check orders where the preparation deadline has expired
                if ($now < $prepDeadline) {
                    continue;
                }

                // Case 1: Deadline expired WITHOUT acknowledgment (Requirement 3.3)
                if ($order['emergency_acknowledged_at'] === null) {
                    // Check if we already have an unresolved escalation for this
                    if (!$this->hasUnresolvedEscalation($orderId, 'warning')) {
                        $this->triggerEscalation(
                            $orderId,
                            "Vendor preparation deadline expired: Vendor '{$vendorName}' (ID: {$vendorId}) has not acknowledged emergency order #{$orderId} within 5 minutes.",
                            'warning'
                        );

                        // Notify admin
                        $adminId = $this->getAdminUserId();
                        if ($adminId) {
                            $this->notificationService->send(
                                $adminId,
                                "Vendor Deadline - No Acknowledgment",
                                "Emergency order #{$orderId}: Vendor '{$vendorName}' has not acknowledged within 5-minute deadline. Immediate attention required."
                            );
                        }

                        $summary['acknowledgment_escalations']++;
                    }
                }

                // Case 2: Deadline expired WITHOUT "ready for pickup" (Requirement 3.8)
                // This applies whether or not the order was acknowledged
                if ($order['emergency_ready_at'] === null) {
                    // Use 'critical' level for the overdue escalation to differentiate from acknowledgment warning
                    if (!$this->hasUnresolvedEscalation($orderId, 'critical')) {
                        $elapsedMinutes = round(($now - $prepDeadline) / 60, 1);

                        $this->triggerEscalation(
                            $orderId,
                            "Vendor preparation overdue: Emergency order #{$orderId} not marked 'ready for pickup' by vendor '{$vendorName}' (ID: {$vendorId}). Overdue by {$elapsedMinutes} minutes.",
                            'critical'
                        );

                        // Notify admin of overdue order
                        $adminId = $this->getAdminUserId();
                        if ($adminId) {
                            $this->notificationService->send(
                                $adminId,
                                "Vendor Overdue - Order #{$orderId}",
                                "Emergency order #{$orderId}: Vendor '{$vendorName}' has not marked order as ready for pickup. Preparation deadline exceeded by {$elapsedMinutes} minutes. Order flagged as overdue."
                            );
                        }

                        $summary['ready_escalations']++;
                    }
                }
            }
        } catch (\Exception $e) {
            error_log("PriorityEngine::checkVendorPreparationDeadlines error: " . $e->getMessage());
            $summary['errors'][] = $e->getMessage();
        }

        return $summary;
    }
}
