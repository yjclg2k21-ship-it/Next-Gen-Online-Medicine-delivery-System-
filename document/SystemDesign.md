# 🏛️ System Design & Architecture Document
## Project: Mediflow Clinical Pharmacy Ecosystem

> [!IMPORTANT]
> This document outlines the technical blueprint of the Mediflow platform, including its architectural patterns, data models, and API specifications.

---

## 1. Architectural Overview
Mediflow follows a **Decoupled N-Tier Architecture** to ensure separation of concerns and ease of maintenance.

### 1.1 Presentation Layer (Frontend)
- **Framework**: React 18 with Vite.
- **State Management**: React Hooks (useState, useEffect, Context API).
- **Styling**: Vanilla CSS with CSS Variables for theme consistency.
- **Animations**: Framer Motion for premium micro-interactions.

### 1.2 Application Layer (Backend)
- **Framework**: Custom lightweight PHP MVC (Model-View-Controller).
- **Routing**: Regex-based Router for high-performance dispatch.
- **Controllers**: Thin controllers calling specialized Service logic.

### 1.3 Data Layer (Database)
- **Engine**: MySQL 8.0+.
- **Storage**: InnoDB with foreign key constraints for referential integrity.

---

## 2. High-Level Data Flow (DFD)
1.  **User Action**: User places an order via the React frontend.
2.  **API Call**: Axios sends a signed JWT request to the PHP Backend.
3.  **Controller Logic**: `OrderController` validates stock and prescription status.
4.  **Database Update**: SQL transaction deducts stock and creates the order record.
5.  **Telemetry Pulse**: `AdminController` detects the new activity and updates the "Global Pulse" graph.

---

## 3. Entity Relationship (ER) Summary
The database consists of **16 core tables**. Key relationships include:
- **Users**: One-to-Many with `orders`, `addresses`, and `wallets`.
- **Medicines**: One-to-Many with `order_items` and `reviews`.
- **Orders**: Linked to `users`, `vendors`, `delivery_methods`, and `prescriptions`.
- **Wallets**: Linked to `users` with a transaction ledger (`wallet_transactions`).

---

## 4. API Structure Design
The API follows RESTful principles with standard HTTP verbs:

| Endpoint | Method | Controller | Description |
| :--- | :--- | :--- | :--- |
| `/auth/login` | POST | AuthController | User authentication and token发放. |
| `/user/profile` | GET | UserController | Retrieve authenticated user metadata. |
| `/medicines` | GET | MedicineController | Filterable medicine catalog. |
| `/orders` | POST | OrderController | Final checkout and order placement. |
| `/admin/pulse` | GET | AdminController | Real-time system health telemetry. |

---

## 5. Security Architecture
- **Bearer Token Auth**: All sensitive requests require an `Authorization` header.
- **SQL Sanitization**: All database queries are prepared to prevent SQL Injection.
- **XCSRF Protection**: Implemented via custom middleware in the PHP bootstrap.

---
**Document Version: 1.0.0 (Technical Baseline)**
