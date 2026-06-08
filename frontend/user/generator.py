import os

files = [
    'order-details.html',
    'user-addresses.html',
    'user-address-wallet.html',
    'user-payment-history.html',
    'user-prescription-flow.html',
    'user-recommendations.html',
    'user-returns.html',
    'user-returns.html',
    'user-wishlist.html'
]

template = """<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{title} | MediMitra</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
            <p style="color: var(--grey); margin-bottom: 2rem;">This page is currently under development.</p>
            
            <div class="glass-card" style="padding: 4rem 2rem; text-align: center; border: 2px dashed var(--border);">
                <div style="font-size: 4rem; margin-bottom: 1rem;">🚧</div>
                <h3>Coming Soon</h3>
                <p style="color: var(--grey);">The UI for {title} is being integrated into the new Vanilla ecosystem.</p>
                <a href="user-dashboard.html" class="btn btn-primary" style="margin-top: 1rem;">Back to Dashboard</a>
            </div>
        </div>
        <div id="footer-root"></div>
    </main>
    <script src="../assets/js/components.js"></script>
    <script src="../assets/js/ui-loader.js"></script>
</body>
</html>
"""

base_path = "e:/Projects/Major/New/frontend/user"

for f in files:
    file_path = os.path.join(base_path, f)
    if not os.path.exists(file_path):
        title = f.split('.')[0].replace('-', ' ').title()
        with open(file_path, "w", encoding="utf-8") as file:
            file.write(template.format(title=title))
        print(f"Generated {f}")
