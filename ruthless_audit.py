
import os
import re
import json

ROOT = r'e:\Projects\Major\New'
RESULTS = {
    "empty_files": [],
    "placeholders": [],
    "broken_imports": [],
    "mismatched_api_calls": [],
    "missing_models": [],
    "missing_tables": [],
    "hardcoded_secrets": []
}

def scan_files():
    for root, dirs, files in os.walk(ROOT):
        if '.git' in root or 'node_modules' in root or '.planning' in root: continue
        for f in files:
            path = os.path.join(root, f)
            try:
                # Empty check
                if os.path.getsize(path) == 0:
                    RESULTS["empty_files"].append(path)
                
                # Content scan
                if f.endswith(('.php', '.html', '.js', '.css')):
                    with open(path, 'r', encoding='utf-8', errors='ignore') as file:
                        content = file.read()
                        
                        # Placeholder scan
                        if re.search(r'TODO|FIXME|placeholder|DUMMY_DATA|mock_data', content, re.I):
                            RESULTS["placeholders"].append(path)
                        
                        # Hardcoded secrets
                        if re.search(r'(api_key|secret|password|token)\s*=\s*[\'"][a-zA-Z0-9_\-]{10,}[\'"]', content, re.I):
                             # Exclude common config strings
                             if not any(x in content for x in ['DB_PASSWORD', 'PASSWORD_BCRYPT']):
                                RESULTS["hardcoded_secrets"].append(path)

            except Exception as e:
                pass

def audit_connectivity():
    # 1. Routes from api.php
    routes_file = os.path.join(ROOT, 'backend', 'routes', 'api.php')
    if not os.path.exists(routes_file): return
    
    with open(routes_file, 'r') as f:
        routes_content = f.read()
    routes = re.findall(r"router->add\('([^']+)',\s*'([^']+)',\s*'([^@]+)@([^']+)'\)", routes_content)
    backend_endpoints = set([(r[0].upper(), r[1]) for r in routes])

    # 2. API calls from Frontend
    frontend_dir = os.path.join(ROOT, 'frontend')
    for root, dirs, files in os.walk(frontend_dir):
        for f in files:
            if f.endswith(('.html', '.js')):
                path = os.path.join(root, f)
                with open(path, 'r', encoding='utf-8', errors='ignore') as file:
                    content = file.read()
                    calls = re.findall(r"api\.(get|post|put|delete)\(['\"]([^'\"]+)['\"]", content)
                    for method, endpoint in calls:
                        clean_endpoint = re.sub(r'\$\{.*?\}', '{id}', endpoint).replace('/api/v1', '')
                        if clean_endpoint == '': clean_endpoint = '/'
                        
                        match = False
                        for b_method, b_path in backend_endpoints:
                            # Simple regex match for placeholders
                            pattern = '^' + re.sub(r'\{.*?\}', '[^/]+', b_path) + '$'
                            if method.upper() == b_method and re.match(pattern, clean_endpoint):
                                match = True
                                break
                        if not match and not endpoint.startswith('http'):
                            RESULTS["mismatched_api_calls"].append(f"{f}: {method.upper()} {endpoint}")

def audit_database():
    schema_file = os.path.join(ROOT, 'database', 'schema.sql')
    models_dir = os.path.join(ROOT, 'backend', 'app', 'Models')
    
    if os.path.exists(schema_file):
        with open(schema_file, 'r') as f:
            schema = f.read()
        tables = re.findall(r"CREATE TABLE IF NOT EXISTS\s+`([^`]+)`", schema)
    else:
        tables = []

    if os.path.exists(models_dir):
        models = [f[:-4] for f in os.listdir(models_dir) if f.endswith('.php')]
        # Map table names to model names (snake_case to CamelCase)
        table_to_model = {t.lower().replace('_', ''): t for t in tables}
        model_to_table = {m.lower(): m for m in models}
        
        for t in tables:
            m_guess = t.lower().replace('_', '')
            # Special cases or singular/plural
            m_guess_singular = m_guess[:-1] if m_guess.endswith('s') else m_guess
            if m_guess not in model_to_table and m_guess_singular not in model_to_table:
                RESULTS["missing_models"].append(t)
        
        for m in models:
            t_guess = re.sub(r'(?<!^)(?=[A-Z])', '_', m).lower()
            t_guess_plural = t_guess + 's'
            if t_guess not in [t.lower() for t in tables] and t_guess_plural not in [t.lower() for t in tables]:
                 # Check table property inside file
                 with open(os.path.join(models_dir, m + '.php'), 'r') as mf:
                     m_content = mf.read()
                     table_prop = re.search(r"table\s*=\s*['\"]([^'\"]+)['\"]", m_content)
                     if table_prop:
                         if table_prop.group(1) not in tables:
                             RESULTS["missing_tables"].append(f"{m} -> {table_prop.group(1)}")
                     else:
                         RESULTS["missing_tables"].append(m)

scan_files()
audit_connectivity()
audit_database()

print(json.dumps(RESULTS, indent=2))
