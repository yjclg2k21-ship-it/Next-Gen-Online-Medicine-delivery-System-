import os
import re

ROOT = r"e:\Projects\Major\New\frontend"
RESULTS = []

html_files = []
for d, _, files in os.walk(ROOT):
    for f in files:
        if f.endswith('.html'):
            html_files.append(os.path.join(d, f))

# Read all valid relative route targets
valid_files = set([os.path.basename(f) for f in html_files])

broken_links = []
empty_catches = []
localhost_hardcodes = []

for filepath in html_files:
    fname = os.path.basename(filepath)
    with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
        content = f.read()
        
        # 1. Broken Hrefs
        hrefs = re.findall(r'href="([^"#]+?(?:\.html))"', content)
        for h in hrefs:
            base_target = os.path.basename(h)
            if base_target not in valid_files and not h.startswith('http'):
                broken_links.append(f"{fname} -> {h} (MISSING)")
                
        # 2. Localhost Hardcodes
        if 'http://localhost' in content or 'http://127.0.0.1' in content:
            localhost_hardcodes.append(fname)
            
        # 3. Empty Catches / Catch Ignorance
        if re.search(r'catch\s*\([^)]*\)\s*\{\s*\}', content):
            empty_catches.append(fname)

print("==== AUDIT RESULTS ====")
print(f"Total HTML Specs Analyzed: {len(html_files)}")
print("\n--- BROKEN INTERNAL HTML LINKS ---")
for b in set(broken_links):
    print(b)
print("\n--- LOCALHOST HARDCODE VULNERABILITIES ---")
for l in set(localhost_hardcodes):
    print(l)
print("\n--- EMPTY CATCH STATE DRIFT ---")
for c in set(empty_catches):
    print(c)
