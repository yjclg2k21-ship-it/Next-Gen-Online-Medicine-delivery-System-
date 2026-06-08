# Mediflow Test Plan: Clinical & Safety Verification

This document provides a systematic approach to verifying the "Live" clinical features implemented in Mediflow.

## 🧪 Test Suite 1: AI Safety Engine (Section 13)

### T1.1: Drug-Drug Interaction Check
- **Setup**: Ensure both "Medicine A" and "Medicine B" exist in the `drug_interactions` table.
- **Action**: Add "Medicine A" to cart, then attempt to add "Medicine B".
- **Expected Result**: System should intercept the `POST /ai/check-interactions` call and display a "Severe Warning" alert in the UI.

### T1.2: AI Prescription Extraction
- **Action**: Trigger `POST /ai/analyze-prescription`.
- **Expected Result**: System should return a JSON array of extracted medicines with high confidence scores (95%+).

## 🧪 Test Suite 2: Clinical Analytics (Section 12)

### T2.1: Health Metric Logging
- **Action**: Use `POST /clinical/metrics` to log a Blood Pressure reading (e.g., Systolic: 155).
- **Expected Result**: Data should be visible in `GET /clinical/metrics`.

### T2.2: Automated Health Insights
- **Action**: Trigger `GET /clinical/insights` after logging a high BP reading.
- **Expected Result**: "Hypertension Risk" warning should appear in the insights list.

## 🧪 Test Suite 3: Global Integrity

### T3.1: Startup Audit
- **Action**: Run `run_mediflow.bat`.
- **Expected Result**: Backend starts on Port 8000, Browser opens to landing page, and `db_check.php` returns "SUCCESS".

---
*Verified by MediMitra QA Agent — April 19, 2026*
