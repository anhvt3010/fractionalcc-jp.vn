import sys
import re
import os
import urllib.request

sys.stdout.reconfigure(encoding='utf-8')

com_file = r'C:\Users\anhvt\.gemini\antigravity\brain\484eed8d-74cc-44c9-ba07-332c4c1b2d1d\.system_generated\steps\6\content.md'
vn_file = r'C:\Users\anhvt\.gemini\antigravity\brain\484eed8d-74cc-44c9-ba07-332c4c1b2d1d\.system_generated\steps\14\content.md'

with open(com_file, 'r', encoding='utf-8') as f:
    com_text = f.read()

with open(vn_file, 'r', encoding='utf-8') as f:
    vn_text = f.read()

com_urls = sorted(set(re.findall(r'(?:https?:)?//[^\s\"\'\<\>]+?\.(?:jpg|png|webp|jpeg|svg)', com_text)))
vn_urls = sorted(set(re.findall(r'(?:https?:)?//[^\s\"\'\<\>]+?\.(?:jpg|png|webp|jpeg|svg)', vn_text)))

print("--- Fractional-cc.com media files ---")
for u in com_urls:
    if 'fractional-cc.com' in u:
        print(u)

print("\n--- Fractional-cc.vn media files ---")
for u in vn_urls:
    if 'ladicdn' in u or 'ladipage' in u:
        print(u)
