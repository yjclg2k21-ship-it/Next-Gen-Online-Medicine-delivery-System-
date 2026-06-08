# Mediflow: AI-Powered Clinical Pharmacy Ecosystem

Mediflow is a full-stack pharmaceutical marketplace designed for the Indian healthcare market. It features a robust clinical oversight system, automated delivery dispatch, and a comprehensive vendor POS system.

## 🚀 Quick Start (Automated)

1.  **Launch Ecosystem**: Double-click `run_mediflow.bat` in the root directory.
    - This will start the **PHP Backend** at `http://localhost:8000`
    - You can then access the **Frontend** by opening `frontend/index.html`.

## 🛠️ Manual Installation & Setup

### 1. Prerequisites
- **PHP 8.x** (with PDO and OpenSSL extensions)
- **MySQL** (MariaDB)

### 2. Database Migration
1.  Open your MySQL manager (e.g., phpMyAdmin).
2.  Create a new database named `medicine_delivery`.
3.  Import `database/schema.sql` into the new database.

### 3. Backend Setup
```bash
cd backend
php -S localhost:8000 -t public
```

### 4. Frontend Access
The frontend is built with **Vanilla HTML5 and Liquid CSS**. No `npm install` or build step is required.
- Simply open `frontend/index.html` in your browser.
- Ensure the Backend is running on port 8000.

## 🧪 Testing Accounts (Demo Mode)

| Role | Email | Password |
| :--- | :--- | :--- |
| **Admin** | `admin@medimitra.com` | `password123` |
| **Vendor** | `vendor@medimitra.com` | `password123` |
| **User** | `user@medimitra.com` | `password123` |
| **Delivery** | `delivery@medimitra.com` | `password123` |

## 🧬 Key Features
- **Clinical Dashboard**: Real-time telemetry and clinical oversight.
- **MediPay Sandbox**: Secure simulation of digital payments.
- **AI Safety Warnings**: Automated drug interaction and dosage alerts.

---
**Mediflow Platform - 100% Integrated & Production Ready.**
