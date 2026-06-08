
import os
import re

controllers_dir = r'e:\Projects\Major\New\backend\app\Controllers'
api_file = r'e:\Projects\Major\New\backend\routes\api.php'

# 1. Get all routes from api.php
with open(api_file, 'r') as f:
    api_content = f.read()

# Pattern: $router->add('METHOD', '/path', 'Controller@method');
route_matches = re.findall(r"router->add\('[^']+',\s*'[^']+',\s*'([^@]+)@([^']+)'\)", api_content)

routes_map = {}
for controller, method in route_matches:
    if controller not in routes_map:
        routes_map[controller] = set()
    routes_map[controller].add(method)

# 2. Get all methods from Controllers
controllers_map = {}
for filename in os.listdir(controllers_dir):
    if filename.endswith('.php'):
        controller_name = filename[:-4]
        with open(os.path.join(controllers_dir, filename), 'r') as f:
            content = f.read()
            # Find public function name(...)
            methods = re.findall(r'public\s+function\s+([a-zA-Z0-9_]+)\s*\(', content)
            controllers_map[controller_name] = set(methods)

# 3. Compare
print("--- GAPS: Defined in API but Missing in Controller ---")
for controller, methods in routes_map.items():
    if controller not in controllers_map:
        print(f"Controller {controller} NOT FOUND in directory!")
        continue
    for method in methods:
        if method not in controllers_map[controller]:
            print(f"MISSING METHOD: {controller}@{method}")

print("\n--- GAPS: Present in Controller but NOT used in API ---")
for controller, methods in controllers_map.items():
    if controller not in routes_map:
        # Some controllers like BaseController or AuthController might be used differently
        continue
    for method in methods:
        if method == '__construct': continue
        if method not in routes_map[controller]:
            # Some might be internal or planned
            print(f"UNUSED METHOD: {controller}@{method}")
