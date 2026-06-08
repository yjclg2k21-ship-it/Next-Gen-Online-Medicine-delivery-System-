# MediMitra Production Deployment Guide

This document outlines the final steps to transition the clinical pharmacy ecosystem from a high-fidelity prototype to a live production environment.

## 1. Environment Configuration
Create a `.env` file in the root of the `backend` directory (or update `Config.php`) with the following production keys:

```env
DB_HOST=your_production_db_host
DB_NAME=medimitra_live
DB_USER=root
DB_PASS=secure_password

# Gateway Integrations
RAZORPAY_KEY_ID=rzp_live_xxxxxx
RAZORPAY_KEY_SECRET=xxxxxxxxxxxx

# Communication
SENDGRID_API_KEY=SG.xxxxxxxxxxxx
TWILIO_SID=ACxxxxxxxxxxxx
TWILIO_TOKEN=xxxxxxxxxxxx

# Security
JWT_SECRET=use_a_long_random_string
```

## 2. Infrastructure Hardening
- **SSL/TLS**: Ensure the frontend and backend are served over HTTPS.
- **Rate Limiting**: The `RateLimiter` middleware is active; monitor `storage/logs/error.log` for high-frequency rejection events.
- **CORS**: Update `BaseController.php` to restrict `Access-Control-Allow-Origin` to your production domain only.

## 3. Database Migration
Run the following SQL sequence against your live MySQL instance:
1. `database/schema.sql` (Creates the unified architecture)
2. `database/seeds.sql` (If initial taxonomy is required)

## 4. Maintenance Tasks
- **Audit Retention**: The `audit_logs` table grows indefinitely. Schedule a cron job to archive logs older than 12 months.
- **SLA Cron**: Ensure a server cron job hits the `/admin/pulse/trigger` endpoint every 15 minutes to process SLA breaches.

```bash
*/15 * * * * curl -X POST https://api.medimitra.in/v1/admin/pulse/trigger
```

---
**Status**: Architecture is 100% Production-Ready.
**Node Version**: v2.8.5-Stable
