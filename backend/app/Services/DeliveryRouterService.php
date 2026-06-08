<?php
namespace App\Services;

use App\Core\Database;

/**
 * Delivery Router Service
 * Calculates optimal dispatching logic: 
 * 1. Prioritizes Emergency (SLA=1hr) over Standard (SLA=24hr).
 * 2. Uses basic distance/time algorithms to suggest assignments.
 */
class DeliveryRouterService {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Optimizes a batch of assignments by sorting them by urgency (SLA) 
     * and created time.
     */
    public function optimizeBatch(array $assignments): array {
        usort($assignments, function($a, $b) {
            // Priority 1: Emergency (SLA <= 1 hour)
            $isEmergencyA = ($a['sla_hours'] ?? 24) <= 1;
            $isEmergencyB = ($b['sla_hours'] ?? 24) <= 1;

            if ($isEmergencyA && !$isEmergencyB) return -1;
            if (!$isEmergencyA && $isEmergencyB) return 1;

            // Priority 2: Oldest first within same SLA category
            return strtotime($a['created_at']) <=> strtotime($b['created_at']);
        });

        return $assignments;
    }

    public function findNearestAgent($pickupLat, $pickupLng) {
        // Simulated Haversine search
        return [
            'agent_id' => 104,
            'name' => 'Agent X',
            'distance_km' => 1.2,
            'vehicle' => 'Honda Activa (MH-12-PQ-9021)'
        ];
    }

    public function calculateEta($distanceKm, $priority = 'standard') {
        $baseSpeed = 30; // km/h
        if ($priority === 'emergency') $baseSpeed = 45; // faster response

        $timeHours = $distanceKm / $baseSpeed;
        $prepTime = 10; // 10 mins pickup/pack buffer
        
        return round($timeHours * 60) + $prepTime;
    }
}
