# 🧪 Test Plan & Test Cases
## Project: Mediflow Clinical Ecosystem

> [!CAUTION]
> Testing is the final gatekeeper of clinical safety. This document outlines the scenarios required to ensure 100% stable operations.

---

## 1. Test Strategy
Mediflow uses a **Risk-Based Testing** strategy, prioritizing Patient Safety (Prescriptions) and Financial Integrity (Wallets).

- **Unit Testing**: Testing individual PHP Controller methods and React Components.
- **System Integration**: Testing the end-to-end flow from Cart to Order to Delivery.
- **Simulation Stress**: Triggering "Global Pulse" events to check system response under load.

---

## 2. Role-Based Test Cases

### 2.1 User (Patient) Scenarios
| ID | Title | Scenario | Expected Result |
| :--- | :--- | :--- | :--- |
| **TC-U1** | Medicine Discovery | Search for "Paracetamol" in real-time. | Results appear with correct price (₹) and stock status. |
| **TC-U2** | AI Safety | Add two interacting drugs to cart. | System displays "High Severity" drug interaction warning. |
| **TC-U3** | Checkout | Place order via MediPay Sandbox. | Order status moves to 'Placed', Wallet is deducted correctly. |

### 2.2 Vendor (Pharmacy) Scenarios
| ID | Title | Scenario | Expected Result |
| :--- | :--- | :--- | :--- |
| **TC-V1** | Prescription Review | Approve a pending prescription upload. | Order status moves to 'Verified', user received notification. |
| **TC-V2** | Low-Stock Logic | Manually set stock to < 5 units. | "Forensic Low Stock" alert appears on vendor dashboard. |
| **TC-V3** | POS Offline Sale | Record a walk-in cash sale. | Inventory is instantly updated via the POS API. |

### 2.3 Admin (Oversight) Scenarios
| ID | Title | Scenario | Expected Result |
| :--- | :--- | :--- | :--- |
| **TC-A1** | Global Pulse | Trigger a system-wide diagnostic check. | Real-time graph updates with dynamic telemetry bars. |
| **TC-A2** | Backup Integrity | Trigger a manual database backup. | SQL file is generated in the `backups/` directory. |

---

## 3. Bug Reporting Procedure
1.  **Detection**: Bug found during manual walkthrough.
2.  **Logging**: Open a "Support Ticket" via the Admin dashboard.
3.  **Resolution**: Developer patches the PHP model or React component.
4.  **Verification**: Re-run the specific Test Case.

---
**Document Version: 1.0.0 (Pre-Deployment Validated)**
