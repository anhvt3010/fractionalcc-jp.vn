import re

with open(r'C:\Users\anhvt\.gemini\antigravity\brain\484eed8d-74cc-44c9-ba07-332c4c1b2d1d\.system_generated\steps\54\content.md', 'r', encoding='utf-8') as f:
    text = f.read()

matches = sorted(set(re.findall(r'(?:https?:)?//fractional-cc\.com/cdn/shop/[^\s\"\'\<\>]+?\.(?:jpg|png|webp|jpeg)', text)))
print(f"Found {len(matches)} images in collections/all:")
for m in matches[:30]:
    print(m)
