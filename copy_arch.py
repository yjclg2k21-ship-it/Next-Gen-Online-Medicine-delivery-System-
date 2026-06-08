import shutil
import os

src = r"C:\Users\jaydi\.gemini\antigravity\brain\c57c16b6-3bc3-4fbd-a518-6fe99ef64a6d\plain_architecture_v2_1775578135305.png"
dest = r"e:\Projects\Major\New\document\system_architecture_plain.png"

try:
    shutil.copy2(src, dest)
    print(f"SUCCESS: Copied to {dest}")
except Exception as e:
    print(f"ERROR: {str(e)}")
