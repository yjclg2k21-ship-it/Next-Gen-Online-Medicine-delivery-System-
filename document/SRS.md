# 📑 Software Requirements Specification (SRS)
## Project: Mediflow Clinical Pharmacy Ecosystem

> [!NOTE]
> This document defines the functional and non-functional requirements for the Mediflow platform, serving as the primary contract between stakeholders and the development team.

---

## 1. Introduction
### 1.1 Purpose
The purpose of this document is to provide a detailed overview of the Mediflow platform, its software requirements, and its intended audience.
### 1.2 Scope
Mediflow is an integrated pharmacy marketplace that connects Patients, Pharmacies (Vendors), and Delivery Partners under a unified administrative pulse. It automates prescription validation, inventory management, and logistics dispatch.

---

## 2. User Roles & Permissions
| Role | Primary Responsibility | Key Permissions |
| :--- | :--- | :--- |
| **Admin** | System Oversight | Global Telemetry, User Vetting (KYC), Financial Reports, Backup Management. |
| **Vendor** | Pharmacy Mgmt | Medicine Catalog, POS Billing, Prescription Review, Inventory Alerts. |
| **User** | Healthcare Consumer | Medicine Search, MediWallet, Order Tracking. |
| **Delivery** | Logistics Execution | Dispatch Queue, Earnings Tracking, Route Synchronization. |

---

## 3. Functional Requirements

### 3.1 Authentication & Security (FR-01)
- The system shall provide secure JWT-based authentication.
- Multi-factor simulation for high-value transactions.
- Role-Based Access Control (RBAC) across all API endpoints.

### 3.2 Clinical Oversight (FR-02)
- Automated AI-driven Drug Interaction Warnings (e.g., Aspirin + Warfarin).
- Digital Prescription Upload and Pharmacy Review interface.
- Clinical adherence monitoring and dosage alerts.

### 3.3 Inventory & POS (FR-03)
- Real-time stock tracking with "Low Stock" alerts (Forensic Level).
- Point of Sale (POS) system for walk-in customers at local pharmacies.
- Bulk CSV operations for catalog updates.

### 3.4 Logistics & Pulse (FR-04)
- Global Pulse Diagnostic tool for administrators to monitor system health.
- Real-time Order Tracking Map for users.
- Automated Delivery Dispatch Queue for logistics partners.

---

## 4. Non-Functional Requirements

### 4.1 Performance
- Dashboard telemetry shall refresh within < 500ms using Vite + React.
- API responses from the PHP backend shall be optimized for sub-100ms latency.

### 4.2 Security
- 100% data encryption at rest (SQL) and in transit (HTTPS/SSL).
- Forensic Audit Logging for every sensitive administrative action.

### 4.3 Scalability
- Modular MVC architecture in PHP to allow horizontal scaling of controllers.
- Component-based frontend architecture for easy feature expansion.

---

## 5. Use Case Summary
1.  **UC-01**: Patient orders chronic medication with automated refill.
2.  **UC-02**: Pharmacy Vendor reviews a clinical prescription upload.
3.  **UC-03**: Administrator triggers a "Global Pulse" to detect system bottlenecks.
4.  **UC-04**: Delivery Partner accepts a batch from the Dispatch Queue.

---
**Document Version: 1.0.0 (Production Ready)**
