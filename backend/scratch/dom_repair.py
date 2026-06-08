import os
import re

ROOT = r"e:\Projects\Major\New\frontend"

html_files = []
for d, _, files in os.walk(ROOT):
    for f in files:
        if f.endswith('.html'):
            html_files.append(os.path.join(d, f))

# The targeted fix string
proper_catch = r'catch(e) { console.error(e); if(typeof components !== "undefined") components.showError("Network exception or protocol verification failed."); }'

fixes = 0

for filepath in html_files:
    fname = os.path.basename(filepath)
    with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
        content = f.read()

    original_content = content
    # Repair silent catches
    content = re.sub(r'catch\s*\([^)]*\)\s*\{\s*\}', proper_catch, content)

    # Repair localhost hardcodes to dynamic API wrapper
    content = re.sub(r'http://localhost:\d+', '', content)
    
    if content != original_content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        fixes += 1
        print(f"[REPAIRED] System stabilized -> {fname}")

print(f"\nTotal Components Hardened: {fixes}")
