import re
import urllib.request
import os

with open(r'C:\Users\anhvt\.gemini\antigravity\brain\484eed8d-74cc-44c9-ba07-332c4c1b2d1d\.system_generated\steps\6\content.md', 'r', encoding='utf-8') as f:
    text = f.read()

# find cdn.shopify.com or fractional-cc.com images
matches = sorted(set(re.findall(r'(?:https?:)?//(?:cdn\.shopify\.com|fractional-cc\.com)/[^\s\"\'\<\>]+?\.(?:jpg|png|webp|jpeg|svg)', text)))
print("Shopify images found:", len(matches))
for m in matches:
    print(m)
