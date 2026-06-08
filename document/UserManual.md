# 📘 User Manual & Guide
## Project: Mediflow - Clinical Pharmacy Ecosystem

> [!NOTE]
> This guide provides step-by-step instructions for all user roles to successfully operate the Mediflow platform.

---

## 1. System Access
### 1.1 First-Time Startup
1.  Locate the `run_mediflow.bat` file in the project root.
2.  Double-click to initialize the **Ecosystem**.
3.  Open your browser and navigate to `http://localhost:5173`.

### 1.2 Login Credentials
Use the following accounts for the initial walkthrough:
- **Email**: `user@medimitra.com` | `vendor@medimitra.com` | `admin@medimitra.com`
- **Password**: `password123`

---

## 2. User (Patient) Guide
### 2.1 Ordering Medicine
1.  Search for a medicine using the top search bar.
2.  Click **Add to Cart** on the desired medicine.
3.  In the Checkout screen, select your **Delivery Method**.
4.  Proceed to Payment and enter any test card details in the **MediPay Sandbox**.


---

---

## 3. Vendor (Pharmacy) Guide
### 3.1 Reviewing Prescriptions
1.  Check the **Prescription Queue** on your dashboard.
2.  Click **Review** on a pending item.
3.  Verify the medicine name and dosage against the digital image.
4.  Click **Approve** to move the order to the next phase.

### 3.2 Inventory Management
1.  Navigate to **Inventory Management**.
2.  Update stock counts as you receive new shipments.
3.  Look for the **"Forensic Low Stock"** badge for items needing urgent restock.

---

## 4. Admin (Superuser) Guide
### 4.1 System Pulse
1.  Navigate to the **Admin Dashboard**.
2.  Observe the **Global Pulse** graph for real-time order activity.
3.  Use the **"Trigger Pulse"** button during maintenance to clear caches or verify connections.

### 4.2 Security & Backups
1.  Go to **System Settings -> Backups**.
2.  Click **Create Backup** to generate a snapshot of the MySQL database.
3.  Maintain a weekly backup schedule for clinical data integrity.

---
**Mediflow Platform - 100% Operational Readiness.**
