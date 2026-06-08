import os

# The exact target structure from file structure.txt
structure = {
    "admin": [
        "admin-activity-logs.html", "admin-audit-detail.html", "admin-backup.html",
        "admin-backup-recovery.html", "admin-banner-manager.html", "admin-brand-management.html",
        "admin-bulk-operations.html", "admin-dashboard.html", "admin-financials.html",
        "admin-kyc-verification.html", "admin-medicine-categorization.html", "admin-newsletter.html",
        "admin-order-pulse.html", "admin-pharmacy-approvals.html", "admin-roles-permissions.html",
        "admin-security-settings.html", "admin-support-tickets.html", "admin-system-logs.html",
        "admin-system-pulse.html", "admin-system-settings.html", "admin-users.html",
        "admin-vendor-form.html", "admin-vendors.html"
    ],
    "auth": [
        "login.html", "register.html", "forgot-password.html",
        "reset-password.html", "auth-recovery.html", "auth-pages-layout.html"
    ],
    "delivery": [
        "delivery-dashboard.html", "delivery-earnings.html", "delivery-history.html",
        "delivery-order-details.html", "delivery-orders.html", "delivery-profile.html",
        "delivery-queue.html", "delivery-route-map.html", "delivery-settings.html",
        "optimized-dispatch.html"
    ],
    "guest": [
        "browse-medicines.html", "faq-page.html",
        "medicine-check-tool.html", "medicine-safety-sheet.html", "sitemap.html"
    ],
    "public": [
        "about-page.html", "contact-page.html", "faq-page.html",
        "home-page.html", "legal-page.html"
    ],
    "vendor": [
        "vendor-dashboard.html", "vendor-analytical-reports.html", "vendor-approval-tracker.html",
        "vendor-brand-category.html", "vendor-catalog-config.html", "vendor-commission-summary.html",
        "vendor-customer-detail.html", "vendor-customers.html", "vendor-earnings.html",
        "vendor-inventory.html", "vendor-inventory-reports.html", "vendor-medicine-form.html",
        "vendor-payout-manager.html", "vendor-pos.html", "vendor-prescription-review.html", 
        "vendor-prescriptions.html", "vendor-reports.html", "vendor-returns.html", "vendor-settings.html"
    ],
    "user": [
        "user-dashboard.html", "cart-page.html", "checkout-ui.html", "medicine-details.html",
        "medicines-page.html", "order-details.html", "orders-page.html", "order-tracking-map.html",
        "user-addresses.html", "user-address-wallet.html", "user-notifications.html",
        "user-payment-history.html", "user-prescription-flow.html", "user-prescriptions.html",
        "user-profile.html", "user-recommendations.html", "user-returns.html", "user-wishlist.html"
    ]
}

template = """<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{title} | MediMitra</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Styles -->
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/glass.css">
    <style>
        body {{ display: flex; min-height: 100vh; background: #f1f5f9; }}
        .main-content {{ flex: 1; margin-left: 260px; transition: margin 0.3s; display: flex; flex-direction: column; }}
        .content-area {{ padding: 2.5rem; flex: 1; }}
        @media (max-width: 992px) {{ .main-content {{ margin-left: 0; }} }}
    </style>
</head>
<body>
    <div id="loader" class="loader-overlay"><div class="loader-spinner"></div></div>
    <div id="sidebar-root"></div>
    <main class="main-content">
        <div id="header-root"></div>
        <div class="content-area animate-fade-in">
            <h1 style="font-size: 2.25rem; font-weight: 800; margin-bottom: 0.5rem;">{title}</h1>
            <p style="color: var(--grey); margin-bottom: 2rem;">This module is part of the new Vanilla architecture.</p>
            
            <div class="glass-card" style="padding: 4rem 2rem; border: 2px dashed var(--border); text-align: center;">
                <div style="font-size: 4rem; margin-bottom: 1rem;">🚧</div>
                <h3 style="font-size: 1.5rem; margin-bottom: 1rem;">UI Under Construction</h3>
                <p style="color: var(--grey); margin-bottom: 2rem;">The specific layout and APIs for "{title}" are currently being mapped to the PHP backend.</p>
                <div style="display: flex; gap: 1rem; justify-content: center;">
                    <a href="javascript:history.back()" class="btn btn-outline">Go Back</a>
                </div>
            </div>
        </div>
        <div id="footer-root"></div>
    </main>
    <script src="../assets/js/components.js"></script>
    <script src="../assets/js/ui-loader.js"></script>
</body>
</html>
"""

base_path = "e:/Projects/Major/New/frontend"

# Ensure all files and folders are present
for folder, files in structure.items():
    folder_path = os.path.join(base_path, folder)
    if not os.path.exists(folder_path):
        os.makedirs(folder_path)
    
    for f in files:
        file_path = os.path.join(folder_path, f)
        if not os.path.exists(file_path):
            title = f.split('.')[0].replace('-', ' ').title()
            with open(file_path, "w", encoding="utf-8") as file:
                file.write(template.format(title=title))
            print(f"Generated: {folder}/{f}")

print("✅ All missing files have been generated!")
