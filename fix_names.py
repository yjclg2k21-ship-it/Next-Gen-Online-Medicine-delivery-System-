
import os
import re

ROOT = r'e:\Projects\Major\New\frontend'

replacements = [
    (r"\$\{user\.firstName\}\s+\$\{user\.lastName\}", "${user.name}"),
    (r"user\.firstName\.charAt\(0\)\s*\+\s*user\.lastName\.charAt\(0\)", "user.name.charAt(0)"),
    (r"user\.firstName\s*\+\s*['\"]\s*['\"]\s*\+\s*user\.lastName", "user.name"),
    (r"user\.firstName", "user.name"),
    (r"user\.lastName", "''")
]

def fix_names():
    for root, dirs, files in os.walk(ROOT):
        for f in files:
            if f.endswith(('.html', '.js')):
                path = os.path.join(root, f)
                with open(path, 'r', encoding='utf-8', errors='ignore') as file:
                    content = file.read()
                
                new_content = content
                for pattern, subst in replacements:
                    new_content = re.sub(pattern, subst, new_content)
                
                if new_content != content:
                    with open(path, 'w', encoding='utf-8') as file:
                        file.write(new_content)
                    print(f"Fixed: {f}")

fix_names()
