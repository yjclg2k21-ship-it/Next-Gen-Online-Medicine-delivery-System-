import os

file_path = "c:/xampp/htdocs/Next Gen Online Medicine Delivery System/frontend/user/orders-page.html"

with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

target = """<button class="pill-button" onclick="initiateReturn('${order.id}')" style="background: rgba(255,149,0,0.1); color: #ff9500; border: 1px solid rgba(255,149,0,0.2);">Return Request</button>"""
replacement = """<button class="pill-button" onclick="initiateReturn('${order.id}')" style="background: rgba(255,149,0,0.1); color: #ff9500; border: 1px solid rgba(255,149,0,0.2); display: ${status === 'delivered' ? 'inline-flex' : 'none'};">Return Request</button>"""

if target in content:
    content = content.replace(target, replacement)
    with open(file_path, "w", encoding="utf-8") as f:
        f.write(content)
    print("CSS/Visibility fixed successfully.")
else:
    print("Target not found.")
