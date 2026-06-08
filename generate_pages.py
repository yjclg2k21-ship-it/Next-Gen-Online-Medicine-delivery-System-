import os

# Configuration
BASE_DIR = r"e:\Projects\Major\New\frontend"
STRUCTURE_FILE = r"e:\Projects\Major\New\document\file structure.txt"

# HTML Template
SKELETON_TEMPLATE = """<!DOCTYPE html>
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
</head>
<body>
    <div id="loader" class="loader-overlay"><div class="loader-spinner"></div></div>
    <div id="sidebar-root"></div>
    <main class="main-content" style="flex: 1; margin-left: 260px; padding: 2.5rem;">
        <div id="header-root"></div>
        <div class="content-area animate-fade-in">
            <div class="glass-card">
                <h1>{display_title}</h1>
                <p style="color: var(--grey);">Clinical module skeleton ready. Advanced logic coming soon.</p>
                <div style="margin-top: 2rem; padding: 40px; border: 2px dashed var(--border); border-radius: 12px; text-align: center; opacity: 0.5;">
                    <span style="font-size: 3rem;">🏗️</span>
                </div>
            </div>
        </div>
        <div id="footer-root"></div>
    </main>
    <script src="../assets/js/api.js"></script>
    <script src="../assets/js/auth.js"></script>
    <script src="../assets/js/components.js"></script>
    <script src="../assets/js/ui-loader.js"></script>
    <style>
        body {{ display: flex; min-height: 100vh; background: #f8fafc; }}
        @media (max-width: 992px) {{ .main-content {{ margin-left: 0 !important; }} }}
    </style>
</body>
</html>
"""

def generate():
    with open(STRUCTURE_FILE, 'r', encoding='utf-8') as f:
        lines = f.readlines()
    
    current_module = ""
    for line in lines:
        line = line.strip()
        if not line: continue
        
        # Module
        if '├── /' in line or '└── /' in line:
            parts = line.split('/')
            if len(parts) > 1:
                module = parts[1].split()[0].replace('│', '').strip()
                if module and module != 'assets':
                    current_module = module
            continue
            
        # File
        if '.html' in line:
            filename = line.replace('│', '').replace('├──', '').replace('└──', '').strip().split()[0]
            if not filename.endswith('.html'): continue
            
            # Module path
            if not current_module: continue
            module_dir = os.path.join(BASE_DIR, current_module)
            file_path = os.path.join(module_dir, filename)
            
            # DON'T overwrite my premium pages!
            if os.path.exists(file_path):
                print(f"Skipping existing: {file_path}")
                continue
                
            os.makedirs(module_dir, exist_ok=True)
            
            title = filename.replace('.html', '').replace('-', ' ').title()
            content = SKELETON_TEMPLATE.format(title=title, display_title=title)
            
            with open(file_path, 'w', encoding='utf-8') as hf:
                hf.write(content)
            print(f"Skeletal: {file_path}")

if __name__ == "__main__":
    generate()
