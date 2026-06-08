
import os
import re

# Paths
ROOT = r'e:\Projects\Major\New'
BACKEND = os.path.join(ROOT, 'backend')
FRONTEND = os.path.join(ROOT, 'frontend')
CONTROLLERS = os.path.join(BACKEND, 'app', 'Controllers')
MODELS = os.path.join(BACKEND, 'app', 'Models')
ROUTES = os.path.join(BACKEND, 'routes', 'api.php')
SCHEMA = os.path.join(ROOT, 'database', 'schema.sql')

def audit():
    print("=== STARTING GLOBAL ARCHITECTURAL AUDIT ===")
    
    with open(ROUTES, 'r') as f:
        routes_content = f.read()
    routes = re.findall(r"router->add\('([^']+)',\s*'([^']+)',\s*'([^@]+)@([^']+)'\)", routes_content)
    
    controller_methods = {}
    for f in os.listdir(CONTROLLERS):
        if f.endswith('.php'):
            name = f[:-4]
            with open(os.path.join(CONTROLLERS, f), 'r') as cf:
                content = cf.read()
                methods = re.findall(r'public\s+function\s+([a-zA-Z0-9_]+)', content)
                controller_methods[name] = set(methods)

    models = [f[:-4] for f in os.listdir(MODELS) if f.endswith('.php')]

    with open(SCHEMA, 'r') as f:
        schema_content = f.read()
    tables = re.findall(r"CREATE TABLE IF NOT EXISTS\s+`([^`]+)`", schema_content)

    frontend_api_calls = set()
    for root, dirs, files in os.walk(FRONTEND):
        for f in files:
            if f.endswith('.html') or f.endswith('.js'):
                with open(os.path.join(root, f), 'r', encoding='utf-8', errors='ignore') as ff:
                    content = ff.read()
                    calls = re.findall(r"api\.(get|post|put|delete)\(['\"]([^'\"]+)['\"]", content)
                    for method, path in calls:
                        clean_path = re.sub(r'\$\{.*?\}', '{id}', path)
                        clean_path = re.sub(r'\?.*', '', clean_path)
                        frontend_api_calls.add((method.upper(), clean_path))

    errors = []

    for method, path, ctrl, meth in routes:
        if ctrl not in controller_methods:
            errors.append(f"CRITICAL: Route {path} maps to non-existent Controller {ctrl}")
        elif meth not in controller_methods[ctrl]:
            errors.append(f"CRITICAL: Route {path} maps to non-existent Method {ctrl}@{meth}")

    for ctrl, meths in controller_methods.items():
        with open(os.path.join(CONTROLLERS, ctrl + '.php'), 'r') as f:
            content = f.read()
            instantiations = re.findall(r"new\s+([A-Z][a-zA-Z0-9]+)", content)
            for m in instantiations:
                if m in ['Exception', 'DateTime', 'AuditLogger', 'ResponseHandler', 'AuthGuard', 'RoleCheck', 'Database', 'NotificationService', 'PaymentGateway', 'MedicineIntelligence', 'AuditTrailService', 'InventoryManager', 'DeliveryRouterService', 'SLATracker', 'FileStorageService', 'PrescriptionProcessor', 'PDO', 'BaseController', 'Model']:
                    continue
                if m not in models:
                    errors.append(f"MISSING MODEL: {ctrl} instantiates non-existent model {m}")

    route_patterns = []
    for r_method, r_path, r_ctrl, r_meth in routes:
        pattern = re.sub(r'\{.*?\}', '[^/]+', r_path)
        route_patterns.append((r_method.upper(), '^' + pattern + '$'))

    for f_method, f_path in frontend_api_calls:
        match_found = False
        for r_method, r_pattern in route_patterns:
            if f_method == r_method and re.match(r_pattern, f_path):
                match_found = True
                break
        if not match_found:
            if not f_path.startswith('http') and not f_path.startswith('//') and not f_path.startswith('/api/v1'):
                 f_path_clean = f_path.replace('/api/v1', '')
                 if f_path_clean == '': f_path_clean = '/'
                 for r_method, r_pattern in route_patterns:
                    if f_method == r_method and re.match(r_pattern, f_path_clean):
                        match_found = True
                        break
                 if not match_found:
                    errors.append(f"FRONTEND GAP: {f_method} {f_path} has no corresponding route in api.php")

    if not errors:
        print("\nSUCCESS: ZERO GAPS DETECTED. SYSTEM IS 100% SYNCED.")
    else:
        print(f"\nFAILURE: FOUND {len(errors)} GAPS:")
        for e in errors:
            print(f"  - {e}")

audit()
