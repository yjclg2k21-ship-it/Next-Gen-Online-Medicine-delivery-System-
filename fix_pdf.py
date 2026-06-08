import os

file_path = "c:/xampp/htdocs/Next Gen Online Medicine Delivery System/frontend/user/order-details.html"

with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

target = """            try {
                await html2pdf().set(opt).from(element).save();
                if (buttons) buttons.style.visibility = 'visible';
            }"""

replacement = """            try {
                await html2pdf().set(opt).from(element).toPdf().get('pdf').then(function(pdf) {
                    const blob = pdf.output('blob');
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    a.download = fileName;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);
                });
                if (buttons) buttons.style.visibility = 'visible';
            }"""

if target in content:
    content = content.replace(target, replacement)
    with open(file_path, "w", encoding="utf-8") as f:
        f.write(content)
    print("PDF export logic fixed.")
else:
    print("Target not found.")
