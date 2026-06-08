# Requirements Document

## Introduction

The Emergency Order Priority System introduces differentiated handling for emergency medicine orders across all four system panels (Admin, Vendor, Delivery, Customer). Currently, despite customers paying a premium for emergency delivery (under 40 minutes), no priority logic distinguishes emergency orders from standard orders during assignment, preparation, or delivery. This feature implements a comprehensive priority pipeline ensuring emergency orders receive dedicated treatment at every stage: admin auto-assignment with nearest-rider selection, vendor preparation with enforced 5-minute deadlines, delivery partner priority queues with faster pickup notifications, and customer-facing real-time ETA tracking.

## Glossary

- **Priority_Engine**: The backend service responsible for identifying, scoring, and routing emergency orders with higher precedence than standard orders throughout the system pipeline.
- **Auto_Assign_Algorithm**: The algorithm within the DeliveryController that assigns delivery partners to confirmed orders, prioritizing emergency orders and selecting the nearest available rider within a 5km radius.
- **Vendor_Alert_System**: The notification mechanism that delivers time-critical alerts to vendors when an emergency order is placed, enforcing a 5-minute preparation deadline.
- **Delivery_Priority_Queue**: The ordered list of pending deliveries shown to delivery partners, where emergency orders appear at the top with visual distinction and faster notification intervals.
- **ETA_Tracker**: The customer-facing component that calculates and displays real-time estimated time of arrival for emergency deliveries based on rider location and order status.
- **SLA_Deadline**: The maximum allowed time (40 minutes from order placement) for an emergency order to be delivered to the customer.
- **Preparation_Deadline**: The 5-minute window from order receipt within which a vendor must acknowledge and begin preparing an emergency order.
- **Emergency_Order**: An order where `is_emergency = 1` or `delivery_method_id = 3`, indicating the customer selected and paid for emergency delivery service.
- **Admin_Panel**: The administrative interface at `admin/admin-order-pulse.html` used for order monitoring and dispatch management.
- **Vendor_Panel**: The vendor interface at `vendor/vendor-orders.html` used for order management and preparation tracking.
- **Delivery_Panel**: The delivery partner interface at `delivery/delivery-orders.html` used for viewing and managing assigned deliveries.
- **Customer_Panel**: The customer interface at `user/orders-page.html` used for order tracking and delivery status.

## Requirements

### Requirement 1: Emergency Order Priority Auto-Assignment

**User Story:** As an admin, I want the auto-assign algorithm to prioritize emergency orders over standard orders and assign the nearest available rider, so that emergency deliveries meet the 40-minute SLA.

#### Acceptance Criteria

1. WHEN the Auto_Assign_Algorithm executes, THE Priority_Engine SHALL process all Emergency_Order instances before any standard orders.
2. WHEN assigning an Emergency_Order, THE Auto_Assign_Algorithm SHALL select the delivery partner with the shortest straight-line geographic distance to the vendor location who is currently online, within active shift hours, and has no active emergency assignments. IF two or more eligible partners are equidistant, THEN THE Auto_Assign_Algorithm SHALL select the partner who has been idle the longest.
3. WHEN an Emergency_Order is assigned, THE Auto_Assign_Algorithm SHALL set the SLA_Deadline to 40 minutes from the order creation timestamp.
4. WHEN an Emergency_Order is assigned, THE Auto_Assign_Algorithm SHALL lock the assigned delivery partner from receiving additional orders until the delivery status is marked as "delivered" in the system.
5. IF no delivery partner that is online, within active shift hours, and without an active emergency assignment is located within 5km of the vendor for an Emergency_Order, THEN THE Auto_Assign_Algorithm SHALL assign the nearest eligible partner regardless of distance and log a warning entry to the order status history.
6. WHEN the Auto_Assign_Algorithm assigns an Emergency_Order, THE Priority_Engine SHALL record the assignment timestamp, rider distance in kilometers, and SLA_Deadline in the order metadata.
7. IF no eligible delivery partner is available system-wide for an Emergency_Order, THEN THE Auto_Assign_Algorithm SHALL place the order in a priority retry queue and re-attempt assignment every 60 seconds for a maximum of 5 attempts, recording each failed attempt in the order status history.
8. IF the assigned delivery partner does not accept the Emergency_Order within 90 seconds of assignment, THEN THE Auto_Assign_Algorithm SHALL revoke the assignment, mark the partner as non-responsive, and re-run the assignment process for that order selecting the next nearest eligible partner.

### Requirement 2: Admin Emergency Order Dashboard

**User Story:** As an admin, I want a separate emergency order view with real-time status indicators, so that I can monitor all emergency deliveries and intervene when SLA breaches are imminent.

#### Acceptance Criteria

1. THE Admin_Panel SHALL display a dedicated emergency orders section that lists all active Emergency_Order instances (status: placed, confirmed, dispatched, or in-transit) separately from standard orders in a distinct panel or tab.
2. THE Admin_Panel SHALL refresh the emergency orders section data automatically at intervals no longer than 30 seconds without requiring manual page reload.
3. WHEN an Emergency_Order has less than 10 minutes remaining before SLA_Deadline, THE Admin_Panel SHALL display a visually distinct warning indicator (differentiated from the breach alert by color or icon) on that order.
4. WHEN an Emergency_Order exceeds the SLA_Deadline without delivery confirmation, THE Admin_Panel SHALL display a breach alert on that order showing the elapsed time beyond the deadline in minutes and seconds (MM:SS format), visually distinct from the warning indicator.
5. THE Admin_Panel SHALL display the assigned rider name (or an "Unassigned" label if no rider is assigned), current distance to destination in kilometers, and remaining SLA time in HH:MM:SS format for each active Emergency_Order.
6. WHEN an admin views the emergency orders section, THE Admin_Panel SHALL sort orders with breached orders appearing first (sorted by elapsed breach time descending), followed by non-breached orders sorted by remaining SLA time in ascending order.
7. IF an Emergency_Order has no assigned rider and less than 10 minutes remaining before SLA_Deadline, THEN THE Admin_Panel SHALL display both the warning indicator and the "Unassigned" rider label to signal that intervention is needed.

### Requirement 3: Vendor Emergency Order Alerts and Preparation Deadline

**User Story:** As a vendor, I want to receive immediate alerts for emergency orders with a clear preparation deadline, so that I can prioritize preparation and ensure timely handoff to the delivery partner.

#### Acceptance Criteria

1. WHEN an Emergency_Order is placed with a vendor, THE Vendor_Alert_System SHALL deliver an alert notification to the Vendor_Panel within 3 seconds of order confirmation.
2. WHEN an Emergency_Order alert is delivered, THE Vendor_Alert_System SHALL display the Preparation_Deadline as a countdown timer starting at 5 minutes, updating the displayed remaining time every 1 second.
3. IF a vendor has not acknowledged an Emergency_Order within the 5-minute Preparation_Deadline, THEN THE Vendor_Alert_System SHALL escalate the order by sending a notification to the Admin_Panel indicating the vendor and order details.
4. THE Vendor_Panel SHALL display Emergency_Order instances at the top of the orders list with a dedicated visual indicator that is distinct from standard order entries.
5. WHEN a vendor marks an Emergency_Order as "ready for pickup", THE Vendor_Alert_System SHALL notify the assigned delivery partner within 3 seconds.
6. WHILE an Emergency_Order is in preparation, THE Vendor_Panel SHALL display the remaining Preparation_Deadline time updated every 1 second and the assigned rider ETA to the vendor location updated every 30 seconds.
7. WHEN a vendor selects an Emergency_Order from the orders list, THE Vendor_Alert_System SHALL record the selection as acknowledgment and stop the escalation countdown for that order.
8. IF the Preparation_Deadline countdown reaches zero and the vendor has not marked the Emergency_Order as "ready for pickup", THEN THE Vendor_Alert_System SHALL send an escalation notification to the Admin_Panel and display an overdue indicator on the order in the Vendor_Panel.
9. IF no delivery partner is assigned to an Emergency_Order when the vendor marks it as "ready for pickup", THEN THE Vendor_Alert_System SHALL display a message to the vendor indicating that rider assignment is pending and SHALL notify the Admin_Panel.

### Requirement 4: Delivery Partner Priority Queue

**User Story:** As a delivery partner, I want emergency orders to appear at the top of my queue with priority notifications, so that I can pick them up and deliver them within the guaranteed timeframe.

#### Acceptance Criteria

1. THE Delivery_Priority_Queue SHALL display all assigned Emergency_Order instances above standard orders in the delivery partner order list, sorted by remaining SLA time in ascending order.
2. WHEN an Emergency_Order is assigned to a delivery partner, THE Delivery_Priority_Queue SHALL send a high-priority push notification with an audible alert distinct from standard order notifications.
3. WHILE a delivery partner has an active Emergency_Order, THE Delivery_Panel SHALL display the remaining SLA time as a countdown updated every 1 second and the optimal route to the vendor pickup location.
4. IF a delivery partner does not accept an Emergency_Order within 2 minutes of assignment, THEN THE Priority_Engine SHALL reassign the order to the next nearest available delivery partner, limited to a maximum of 3 reassignment attempts per order.
5. WHILE a delivery partner is carrying an Emergency_Order, THE Delivery_Panel SHALL suppress new standard order notifications to avoid distraction.
6. WHEN a delivery partner picks up an Emergency_Order from the vendor, THE Delivery_Panel SHALL display the remaining SLA time and optimal route to the customer delivery address.
7. WHEN a delivery partner taps "Accept" on an Emergency_Order notification, THE Delivery_Panel SHALL record the acceptance timestamp and transition the order status to "accepted".

### Requirement 5: Customer Emergency Delivery Tracking

**User Story:** As a customer, I want real-time tracking of my emergency delivery with live ETA updates, so that I know exactly when my medicine will arrive.

#### Acceptance Criteria

1. WHEN a customer views an active Emergency_Order, THE ETA_Tracker SHALL display the current estimated delivery time updated every 30 seconds based on the delivery partner's last known GPS location.
2. WHILE an Emergency_Order is active, THE Customer_Panel SHALL display the current order stage (Confirmed, Preparing, Ready for Pickup, Rider En Route to Vendor, Picked Up, Rider En Route to You, Delivered) with the timestamp of the most recent stage transition.
3. WHEN the ETA_Tracker performs its 30-second update cycle and the delivery partner location has changed since the last update, THE ETA_Tracker SHALL recalculate and display the revised estimated arrival time on the Customer_Panel.
4. WHEN an Emergency_Order transitions between stages, THE Customer_Panel SHALL send a push notification to the customer with the updated stage name and the current estimated delivery time if a rider has been assigned.
5. WHILE an Emergency_Order is active, THE Customer_Panel SHALL display a visual progress indicator showing the percentage of the 40-minute SLA time elapsed since order placement.
6. IF the ETA_Tracker estimates that delivery will exceed the SLA_Deadline, THEN THE Customer_Panel SHALL display a delay notification with the revised estimated delivery time within 30 seconds of detecting the breach risk.
7. IF the delivery partner's GPS location has not been received for more than 60 seconds, THEN THE ETA_Tracker SHALL display the last known ETA with a label indicating the location data is stale.
8. WHILE an Emergency_Order is in the Confirmed or Preparing stage and no delivery partner location is available, THE ETA_Tracker SHALL display the remaining SLA time as a countdown instead of a route-based ETA.

### Requirement 6: Emergency Order SLA Monitoring and Escalation

**User Story:** As an admin, I want automated SLA monitoring with escalation triggers, so that emergency orders at risk of breach are flagged and reassigned before the deadline passes.

#### Acceptance Criteria

1. THE Priority_Engine SHALL evaluate all active Emergency_Order SLA_Deadline values every 60 seconds.
2. WHEN an Emergency_Order has less than 15 minutes remaining before SLA_Deadline and the delivery partner has not picked up from the vendor, THE Priority_Engine SHALL trigger an escalation alert to the Admin_Panel containing the order ID, assigned delivery partner ID, vendor location, remaining time, and current order status.
3. WHEN an Emergency_Order has less than 10 minutes remaining before SLA_Deadline and the order status is not "picked_up" or "in_transit", THE Priority_Engine SHALL attempt automatic reassignment to an available delivery partner with a shorter estimated delivery time to the vendor than the currently assigned partner, limited to a maximum of 2 reassignment attempts per order.
4. WHEN an Emergency_Order SLA_Deadline is breached, THE Priority_Engine SHALL log the breach with order ID, assigned partner ID, elapsed time, and breach reason to the audit trail.
5. IF an Emergency_Order is reassigned due to SLA risk, THEN THE Priority_Engine SHALL notify the original delivery partner, the new delivery partner, and the admin of the reassignment with the reason.
6. IF automatic reassignment is attempted and no available delivery partner with a shorter estimated delivery time exists, THEN THE Priority_Engine SHALL escalate the order to the Admin_Panel as requiring manual intervention and retain the current delivery partner assignment.

### Requirement 7: Emergency Order Audio and Visual Alerts

**User Story:** As a vendor and delivery partner, I want distinct audio and visual alerts for emergency orders, so that I can immediately distinguish them from standard orders without reading details.

#### Acceptance Criteria

1. WHEN an Emergency_Order notification is received, THE Vendor_Panel SHALL play an audio alert that is at least 3 seconds in duration and uses a different tone pattern than the standard order notification sound, repeating every 10 seconds until the vendor acknowledges the order.
2. WHEN an Emergency_Order notification is received, THE Delivery_Panel SHALL play an audio alert that is at least 3 seconds in duration and uses a different tone pattern than the standard order notification sound, repeating every 10 seconds until the delivery partner acknowledges the order.
3. IF audio playback fails or is blocked by the device, THEN THE Vendor_Panel and Delivery_Panel SHALL display a full-screen visual overlay indicating an incoming Emergency_Order that persists until acknowledged.
4. WHEN an Emergency_Order notification is received, THE Vendor_Panel SHALL display the Emergency_Order card with a red border and a pulsing animation at a rate of 1 pulse per second to visually distinguish it from standard order cards.
5. WHEN an Emergency_Order notification is received, THE Delivery_Panel SHALL display the Emergency_Order entry with a red background highlight and an "EMERGENCY" badge to visually distinguish it from standard entries.
6. WHILE an Emergency_Order Preparation_Deadline countdown is below 2 minutes, THE Vendor_Panel SHALL increase the pulsing animation frequency of the order card to 3 pulses per second.
7. WHEN the vendor or delivery partner acknowledges an Emergency_Order, THE respective panel SHALL stop the repeating audio alert and cease the pulsing animation, while retaining the static visual indicators (red border, badge, or highlight) on the order card.
