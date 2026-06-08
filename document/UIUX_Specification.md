# 🎨 UI/UX Specification Document
## Project: Mediflow - Premium Clinical Aesthetics

> [!TIP]
> Mediflow's design philosophy is centered around **Trust, Clarity, and Speed**. We utilize a "Glassmorphic Clinical" theme to bridge the gap between traditional healthcare and modern technology.

---

## 1. Design System
### 1.1 Color Palette
- **Primary**: `#2d6a4f` (Clinical Green) - Represents health, safety, and growth.
- **Secondary**: `#52b788` (Light Mint) - Used for accents and success states.
- **Surface**: `rgba(255, 255, 255, 0.7)` - Glassmorphic panels with `backdrop-filter: blur(10px)`.
- **Text**: `#1b4332` (Deep Forest) - For high readability.

### 1.2 Typography
- **Primary Font**: 'Inter', sans-serif (via Google Fonts).
- **Scale**:
  - `H1`: 32px (Bold) - Page Headers.
  - `Body`: 14px (Regular) - Content.
  - `Caption`: 11px (Bold / Uppercase) - Metrics & Tags.

---

## 2. Key User Flows
### 2.1 The "Forensic" Search & Buy Flow
1. **Discover**: User types "Paracetamol" in the smart-search bar.
2. **Interact**: Real-time suggestions appear with price and stock indicators.
3. **Validate**: Medicine card shows "Requires Prescription" badge if applicable.
4. **Acquire**: Smooth cart transition and secure checkout via MediPay Sandbox.

### 2.2 The "Vendor" Fulfillment Flow
1. **Alert**: Real-time notification of a new order or low-stock item.
2. **Review**: High-fidelity modal for digital prescription inspection.
3. **Fulfill**: One-click status update (Packed -> Shipped) with backend synchronization.

---

## 3. High-Fidelity UI Layouts (Wireframe Descriptions)

### 3.1 Admin "Global Pulse" Dashboard
- **Header**: Sticky glass header with system-wide stats (Total Sales, Active Users).
- **Center**: Large-scale telemetry graph with data-driven bars representing real-time activity.
- **Sidebar**: Neumorphic navigation with icons for Audit Logs, Backups, and Settings.

### 3.2 User "Medicine Detail" Page
- **Visuals**: Primary medicine image with a soft drop shadow.
- **Metadata**: Clear section for salt composition, alternatives, and AI safety warnings.
- **Action**: Floating "Quick Bar" for adding to cart or viewing details.

---

## 4. Micro-interactions
- **Hover States**: Subtle `scale(1.02)` and `box-shadow` increase on product cards.
- **Transitions**: 300ms cubic-bezier for sidebars and modal pop-ins.
- **Loading**: Pulse animation on skeletal loaders during API fetch.

---
**Document Version: 1.0.0 (Design Specs Locked)**
