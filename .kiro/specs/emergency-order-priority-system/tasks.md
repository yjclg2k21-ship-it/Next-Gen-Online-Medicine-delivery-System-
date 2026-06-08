# Implementation Plan: Emergency Order Priority System

## Overview

This plan implements a comprehensive priority pipeline for emergency medicine orders across the PHP/MySQL XAMPP stack. Tasks are ordered to build incrementally: database schema first, then the core PriorityEngine service, backend API endpoints for each panel, the shared frontend emergency alerts module, and finally frontend updates for each panel. Each task produces working, integrated code.

## Tasks

- [x] 1. Database schema migrations
  - [x] 1.1 Alter `orders` table with emergency priority columns
    - Add columns: `sla_deadline`, `emergency_locked`, `sla_breach`, `emergency_acknowledged_at`, `emergency_ready_at`, `emergency_picked_up_at`, `emergency_delivered_at`, `assignment_retry_count`, `reassignment_count`
    - Add indexes: `idx_emergency_active (is_emergency, status, sla_deadline)`, `idx_emergency_partner (delivery_partner_id, emergency_locked)`
    - Create migration SQL file at `backend/database/migrations/add_emergency_priority_to_orders.sql`
    - _Requirements: 1.3, 1.6_

  - [x] 1.2 Create `emergency_assignment_log` table
    - Create table with columns: `id`, `order_id`, `partner_id`, `assignment_timestamp`, `rider_distance_km`, `sla_deadline`, `vendor_lat`, `vendor_lng`, `partner_lat`, `partner_lng`, `assignment_type`, `status`, `accepted_at`, `revoked_at`, `revoke_reason`, `created_at`
    - Add indexes on `order_id`, `partner_id`, `status`
    - Add foreign keys to `orders` and `users` tables
    - Create migration SQL file at `backend/database/migrations/create_emergency_assignment_log.sql`
    - _Requirements: 1.6, 1.8_

  - [x] 1.3 Create `emergency_escalations` table
    - Create table with columns: `id`, `order_id`, `escalation_level`, `reason`, `triggered_at`, `resolved_at`, `resolved_by`, `partner_id`, `vendor_id`, `remaining_minutes`, `action_taken`, `created_at`
    - Add indexes on `order_id`, `escalation_level`, `resolved_at`
    - Add foreign key to `orders` table
    - Create migration SQL file at `backend/database/migrations/create_emergency_escalations.sql`
    - _Requirements: 3.3, 6.2, 6.4_

  - [x] 1.4 Create `emergency_retry_queue` table
    - Create table with columns: `id`, `order_id` (unique), `attempt_count`, `max_attempts`, `last_attempt_at`, `next_attempt_at`, `status`, `created_at`
    - Add index on `(status, next_attempt_at)`
    - Add foreign key to `orders` table
    - Create migration SQL file at `backend/database/migrations/create_emergency_retry_queue.sql`
    - _Requirements: 1.7_

  - [x] 1.5 Alter `users` table with delivery partner emergency fields
    - Add columns: `emergency_locked_order_id`, `last_idle_since`, `is_non_responsive`
    - Add index: `idx_delivery_available (role, is_online, emergency_locked_order_id)`
    - Create migration SQL file at `backend/database/migrations/add_emergency_fields_to_users.sql`
    - _Requirements: 1.2, 1.4_

- [x] 2. Backend PriorityEngine service
  - [x] 2.1 Create PriorityEngine core class with Haversine and partner selection
    - Create `backend/app/Services/PriorityEngine.php`
    - Implement `calculateHaversineDistance(lat1, lng1, lat2, lng2)` returning distance in km
    - Implement `findNearestEligiblePartner(vendorLat, vendorLng, excludePartnerId)` that queries online partners within shift hours without active emergency locks, sorted by distance, with idle-time tiebreaker
    - Implement `getOnlinePartnersInRadius(lat, lng, radiusKm)` for 5km radius filtering with fallback to nearest regardless of distance
    - Implement `lockPartner(partnerId, orderId)` and `unlockPartner(partnerId)` for partner exclusivity
    - _Requirements: 1.2, 1.4, 1.5_

  - [x] 2.2 Implement emergency order assignment logic in PriorityEngine
    - Implement `processEmergencyAssignment(orderId)` that sets `sla_deadline = created_at + 40 minutes`, finds nearest partner, locks them, and logs to `emergency_assignment_log`
    - Implement 90-second timeout check: if partner hasn't accepted, revoke assignment, mark non-responsive, re-run assignment excluding that partner
    - Implement `addToRetryQueue(orderId)` and `processRetryQueue()` for when no partner is available (retry every 60s, max 5 attempts)
    - Record assignment metadata: timestamp, rider distance km, SLA deadline
    - _Requirements: 1.1, 1.3, 1.6, 1.7, 1.8_

  - [x] 2.3 Implement SLA monitoring and escalation logic in PriorityEngine
    - Implement `evaluateActiveSLAs()` that checks all active emergency orders
    - Implement warning escalation: <15 min remaining AND not picked up → alert admin with order details
    - Implement auto-reassignment: <10 min remaining AND not picked_up/in_transit → reassign to closer partner (max 2 attempts)
    - Implement breach logging: SLA exceeded → log to audit trail with order_id, partner_id, elapsed_time, reason
    - Implement `triggerEscalation(orderId, reason, level)` creating records in `emergency_escalations`
    - Implement three-party notification on reassignment (original partner, new partner, admin)
    - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5, 6.6_

  - [x] 2.4 Implement ETA calculation and SLA progress helpers
    - Implement `calculateETA(orderId)` using Haversine distance / average speed + buffer
    - Implement `getSLAProgress(orderId)` returning percentage elapsed of 40-minute window (0-100)
    - Implement `getEmergencyOrderStatus(orderId)` returning current stage, timestamps, rider info, and SLA status classification (normal/warning/breached)
    - Handle stale GPS data (>60s) by flagging ETA as stale
    - _Requirements: 5.1, 5.3, 5.5, 5.6, 5.7_

- [ ] 3. Checkpoint - Verify database and service layer
  - Ensure all migration SQL files are syntactically correct and can be executed against the database.
  - Ensure PriorityEngine.php has no syntax errors. Ask the user if questions arise.

- [ ] 4. Backend API endpoints - Admin panel
  - [-] 4.1 Implement `GET /admin/emergency-orders` endpoint
    - Add `emergencyOrders()` method to `AdminController.php`
    - Query all active emergency orders (status: placed, confirmed, dispatched, in-transit) with `is_emergency = 1`
    - Include for each order: assigned rider name (or "Unassigned"), rider distance to destination, remaining SLA time in HH:MM:SS, SLA status (normal/warning/breached), elapsed breach time in MM:SS if breached
    - Sort: breached orders first (by elapsed breach time desc), then by remaining SLA time asc
    - _Requirements: 2.1, 2.3, 2.4, 2.5, 2.6, 2.7_

  - [-] 4.2 Implement `POST /admin/emergency-orders/{id}/reassign` endpoint
    - Add `manualReassign()` method to `AdminController.php`
    - Accept order ID, unlock current partner, find next nearest eligible partner, assign and lock
    - Create escalation record with `manual_intervention` level
    - Notify original partner, new partner via NotificationService
    - _Requirements: 6.5, 6.6_

  - [-] 4.3 Register admin emergency routes in Router
    - Add route definitions for `GET /admin/emergency-orders` and `POST /admin/emergency-orders/{id}/reassign` in the router configuration
    - Apply admin auth middleware to both routes
    - _Requirements: 2.1_

- [ ] 5. Backend API endpoints - Vendor panel
  - [-] 5.1 Implement `GET /vendor/emergency-orders` endpoint
    - Add `vendorEmergencyOrders()` method to `OrderController.php`
    - Query emergency orders for the authenticated vendor with preparation deadline countdown
    - Include: order details, preparation deadline remaining (seconds), acknowledgment status, assigned rider ETA to vendor
    - Sort emergency orders at top of list
    - _Requirements: 3.1, 3.4, 3.6_

  - [-] 5.2 Implement `POST /orders/{id}/acknowledge-emergency` endpoint
    - Add `acknowledgeEmergency()` method to `OrderController.php`
    - Record acknowledgment timestamp in `emergency_acknowledged_at`
    - Stop escalation countdown for that order (mark any pending escalation as resolved)
    - _Requirements: 3.7_

  - [-] 5.3 Implement `POST /orders/{id}/ready-for-pickup` endpoint
    - Add `markReadyForPickup()` method to `OrderController.php`
    - Set `emergency_ready_at` timestamp, update order status
    - Notify assigned delivery partner within 3 seconds via NotificationService
    - If no partner assigned, notify admin and return message to vendor indicating rider assignment pending
    - _Requirements: 3.5, 3.9_

  - [-] 5.4 Implement vendor escalation logic for preparation deadline
    - In PriorityEngine, add method to check vendor preparation deadlines
    - If 5-minute deadline expires without acknowledgment → escalate to admin
    - If deadline expires without "ready for pickup" → escalate to admin and flag order as overdue
    - Integrate with SLA cron check
    - _Requirements: 3.3, 3.8_

  - [-] 5.5 Register vendor emergency routes in Router
    - Add route definitions for `GET /vendor/emergency-orders`, `POST /orders/{id}/acknowledge-emergency`, `POST /orders/{id}/ready-for-pickup`
    - Apply vendor auth middleware
    - _Requirements: 3.1_

- [ ] 6. Backend API endpoints - Delivery partner panel
  - [-] 6.1 Implement `GET /delivery/priority-queue` endpoint
    - Add `priorityQueue()` method to `DeliveryController.php`
    - Return all assigned orders for the authenticated delivery partner
    - Emergency orders sorted first by remaining SLA time ascending, then standard orders
    - Include: SLA countdown, vendor pickup location, route info
    - _Requirements: 4.1, 4.3_

  - [-] 6.2 Implement `POST /delivery/emergency/{id}/accept` endpoint
    - Add `acceptEmergency()` method to `DeliveryController.php`
    - Record acceptance timestamp in assignment log, transition order status to "accepted"
    - Validate acceptance is within timeout window (90 seconds)
    - _Requirements: 4.7_

  - [-] 6.3 Implement `POST /delivery/emergency/{id}/pickup` endpoint
    - Add `pickupEmergency()` method to `DeliveryController.php`
    - Set `emergency_picked_up_at` timestamp, update order status to "picked_up"
    - Update delivery panel to show route to customer address
    - _Requirements: 4.6_

  - [-] 6.4 Implement `POST /delivery/emergency/{id}/deliver` endpoint
    - Add `deliverEmergency()` method to `DeliveryController.php`
    - Set `emergency_delivered_at` timestamp, update order status to "delivered"
    - Unlock delivery partner (`unlockPartner`)
    - Log SLA breach if delivery time exceeded 40-minute deadline
    - _Requirements: 1.4, 6.4_

  - [-] 6.5 Register delivery emergency routes in Router
    - Add route definitions for `GET /delivery/priority-queue`, `POST /delivery/emergency/{id}/accept`, `POST /delivery/emergency/{id}/pickup`, `POST /delivery/emergency/{id}/deliver`
    - Apply delivery partner auth middleware
    - _Requirements: 4.1_

- [ ] 7. Backend API endpoints - Customer tracking
  - [-] 7.1 Implement `GET /orders/{id}/emergency-tracking` endpoint
    - Add `emergencyTracking()` method to `OrderController.php`
    - Return: current stage (Confirmed, Preparing, Ready for Pickup, Rider En Route to Vendor, Picked Up, Rider En Route to You, Delivered), stage transition timestamps, ETA (updated based on rider GPS), SLA progress percentage, delay flag if ETA exceeds deadline
    - Handle stale GPS (>60s): return last known ETA with stale flag
    - Handle no rider assigned: return SLA countdown instead of route-based ETA
    - _Requirements: 5.1, 5.2, 5.3, 5.5, 5.6, 5.7, 5.8_

  - [-] 7.2 Register customer emergency tracking route in Router
    - Add route definition for `GET /orders/{id}/emergency-tracking`
    - Apply customer auth middleware
    - _Requirements: 5.1_

- [ ] 8. Backend SLA cron monitoring endpoint
  - [ ] 8.1 Implement `POST /cron/emergency-sla-check` endpoint
    - Add `emergencySlaCheck()` method to `AdminController.php`
    - Call `PriorityEngine::evaluateActiveSLAs()` to process all active emergency orders
    - Call `PriorityEngine::processRetryQueue()` to retry unassigned orders
    - Check vendor preparation deadlines and trigger escalations
    - Check partner acceptance timeouts (90s) and trigger reassignment
    - Implement execution lock to prevent overlapping cron runs
    - Return summary of actions taken (escalations triggered, reassignments made, retries processed)
    - _Requirements: 6.1, 6.2, 6.3, 1.7, 1.8, 3.3_

  - [ ] 8.2 Register cron route in Router
    - Add route definition for `POST /cron/emergency-sla-check`
    - Apply appropriate security (API key or IP whitelist for cron access)
    - _Requirements: 6.1_

- [ ] 9. Checkpoint - Verify all backend endpoints
  - Ensure all new controller methods have no syntax errors.
  - Ensure all routes are registered and accessible.
  - Ensure PriorityEngine integrates correctly with controllers. Ask the user if questions arise.

- [ ] 10. Frontend shared emergency alerts module
  - [ ] 10.1 Create `emergency-alerts.js` shared module
    - Create `frontend/assets/js/emergency-alerts.js`
    - Implement `EmergencyAlerts.playEmergencyTone(options)` using Web Audio API to generate a distinct 3-second tone pattern
    - Implement `EmergencyAlerts.stopAudio()` to stop current playback
    - Implement `EmergencyAlerts.startRepeatingAlert(intervalMs = 10000)` to repeat tone every 10 seconds until acknowledged
    - Implement audio failure detection: if Web Audio API promise rejects, trigger visual overlay fallback
    - Implement `EmergencyAlerts.showFullScreenOverlay(orderData)` displaying emergency order details until acknowledged
    - Implement `EmergencyAlerts.hideOverlay()` to dismiss overlay on acknowledgment
    - Implement `EmergencyAlerts.applyPulseAnimation(element, pulsesPerSecond = 1)` for CSS pulsing at configurable rate
    - Implement `EmergencyAlerts.startPolling(endpoint, intervalMs, callback)` returning a poll ID
    - Implement `EmergencyAlerts.stopPolling(pollId)` to clear interval
    - Implement `EmergencyAlerts.formatCountdown(remainingSeconds)` returning HH:MM:SS string
    - Implement `EmergencyAlerts.formatElapsedBreach(elapsedSeconds)` returning MM:SS string
    - Implement `EmergencyAlerts.calculateSLAProgress(createdAt, slaMinutes = 40)` returning 0-100 percentage
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5_

  - [ ] 10.2 Create emergency alert CSS styles
    - Add emergency-specific styles to a new file `frontend/assets/css/emergency-alerts.css`
    - Style: red border for emergency cards, pulsing animation keyframes (1 pulse/sec default, 3 pulses/sec for critical), red background highlight, "EMERGENCY" badge, full-screen overlay, countdown timer display
    - Ensure styles work across admin, vendor, delivery, and customer panels
    - _Requirements: 7.4, 7.5, 7.6, 7.7_

- [ ] 11. Frontend Admin panel updates
  - [ ] 11.1 Add emergency orders section to admin order pulse page
    - Modify `frontend/admin/admin-order-pulse.html`
    - Add a dedicated emergency orders panel/tab showing active emergency orders separately from standard orders
    - Include for each order: rider name or "Unassigned" label, distance to destination (km), remaining SLA time (HH:MM:SS countdown), warning indicator (yellow) for <10 min remaining, breach alert (red) with elapsed time (MM:SS) for exceeded SLA
    - Sort: breached first (elapsed desc), then by remaining time asc
    - Include `emergency-alerts.js` and `emergency-alerts.css`
    - Implement 30-second polling to `GET /admin/emergency-orders`
    - Add manual reassign button triggering `POST /admin/emergency-orders/{id}/reassign`
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.7_

- [ ] 12. Frontend Vendor panel updates
  - [ ] 12.1 Add emergency order handling to vendor orders page
    - Modify `frontend/vendor/vendor-orders.html`
    - Display emergency orders at top of orders list with red border and pulsing animation (1 pulse/sec)
    - Show preparation deadline countdown (5 min) updating every 1 second
    - Show assigned rider ETA to vendor location (updated every 30 seconds)
    - Increase pulse rate to 3 pulses/sec when preparation deadline < 2 minutes
    - Add acknowledge button that calls `POST /orders/{id}/acknowledge-emergency` (stops audio and pulsing, retains red border)
    - Add "Ready for Pickup" button that calls `POST /orders/{id}/ready-for-pickup`
    - Display "Rider assignment pending" message if no partner assigned when marked ready
    - Include `emergency-alerts.js` and `emergency-alerts.css`
    - Implement 10-second polling to `GET /vendor/emergency-orders`
    - Play repeating emergency audio tone on new emergency order (every 10s until acknowledged)
    - Show full-screen overlay if audio playback fails
    - _Requirements: 3.1, 3.2, 3.4, 3.5, 3.6, 3.7, 3.9, 7.1, 7.3, 7.4, 7.6, 7.7_

- [ ] 13. Frontend Delivery panel updates
  - [ ] 13.1 Add emergency priority queue to delivery orders page
    - Modify `frontend/delivery/delivery-orders.html`
    - Display emergency orders above standard orders with red background highlight and "EMERGENCY" badge
    - Show SLA countdown timer updating every 1 second
    - Show optimal route to vendor pickup location (display address and distance)
    - Add "Accept" button calling `POST /delivery/emergency/{id}/accept` (records acceptance, transitions status)
    - Add "Picked Up" button calling `POST /delivery/emergency/{id}/pickup` (then show route to customer)
    - Add "Delivered" button calling `POST /delivery/emergency/{id}/deliver` (unlocks partner)
    - Suppress standard order notifications while carrying an active emergency order
    - Include `emergency-alerts.js` and `emergency-alerts.css`
    - Implement 10-second polling to `GET /delivery/priority-queue`
    - Play repeating emergency audio tone on new emergency assignment (every 10s until accepted)
    - Show full-screen overlay if audio playback fails
    - _Requirements: 4.1, 4.2, 4.3, 4.5, 4.6, 4.7, 7.2, 7.3, 7.5, 7.7_

- [ ] 14. Frontend Customer panel updates
  - [ ] 14.1 Add emergency delivery tracking to customer orders page
    - Modify `frontend/user/orders-page.html`
    - For active emergency orders, display: current stage with transition timestamps, ETA updated every 30 seconds, SLA progress bar (percentage of 40 min elapsed), delay notification if ETA exceeds SLA deadline, stale GPS indicator if location data >60s old, SLA countdown when no rider location available
    - Implement 30-second polling to `GET /orders/{id}/emergency-tracking`
    - Show push notification on stage transitions with updated stage name and ETA
    - Include `emergency-alerts.js` for countdown formatting and progress calculation
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 5.6, 5.7, 5.8_

- [ ] 15. Final checkpoint - Full integration verification
  - Ensure all backend endpoints return correct JSON responses.
  - Ensure all frontend panels load without JavaScript errors.
  - Ensure polling intervals are correctly configured (30s admin/customer, 10s vendor/delivery).
  - Ensure emergency-alerts.js is properly included in all four panels.
  - Ask the user if questions arise.

## Notes

- No property-based testing tasks included as no test framework is currently set up
- All real-time updates use polling (no WebSockets) via the existing `api.js` infrastructure
- Audio alerts use Web Audio API (no external audio files needed)
- The SLA cron endpoint should be triggered externally every 60 seconds (XAMPP scheduled task or browser-based cron)
- All emergency state is database-driven, making the system stateless between requests
- Frontend polling intervals: 30s for admin/customer panels, 10s for vendor/delivery panels
- Each task references specific requirements for traceability
- Checkpoints ensure incremental validation

## Task Dependency Graph

```json
{
  "waves": [
    { "id": 0, "tasks": ["1.1", "1.2", "1.3", "1.4", "1.5"] },
    { "id": 1, "tasks": ["2.1"] },
    { "id": 2, "tasks": ["2.2", "2.3", "2.4"] },
    { "id": 3, "tasks": ["4.1", "4.2", "4.3", "5.1", "5.2", "5.3", "5.4", "5.5", "6.1", "6.2", "6.3", "6.4", "6.5", "7.1", "7.2"] },
    { "id": 4, "tasks": ["8.1", "8.2"] },
    { "id": 5, "tasks": ["10.1", "10.2"] },
    { "id": 6, "tasks": ["11.1", "12.1", "13.1", "14.1"] }
  ]
}
```
