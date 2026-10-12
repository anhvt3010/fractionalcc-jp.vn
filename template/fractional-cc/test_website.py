import os
import re

html_files = ['index.html', 'san-pham.html', 'chi-tiet-san-pham.html', 'cam-on.html', 'chinh-sach.html']

all_good = True
for h in html_files:
    if not os.path.exists(h):
        print(f"ERROR: {h} does not exist!")
        all_good = False
        continue
    
    with open(h, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # check images
    img_srcs = re.findall(r'<img[^>]+src=[\"\']([^\"\']+)[\"\']', content)
    for src in img_srcs:
        # ignore external urls or data urls
        if src.startswith('http') or src.startswith('data:') or src.startswith('//'):
            continue
        clean_src = src.split('?')[0].split('#')[0]
        if not os.path.exists(clean_src):
            print(f"[{h}] Missing image: {clean_src}")
            all_good = False

    # check stylesheets & scripts
    links = re.findall(r'<link[^>]+href=[\"\']([^\"\']+)[\"\']', content)
    for l in links:
        if l.endswith('.css'):
            if not os.path.exists(l):
                print(f"[{h}] Missing css: {l}")
                all_good = False

    scripts = re.findall(r'<script[^>]+src=[\"\']([^\"\']+)[\"\']', content)
    for s in scripts:
        if not s.startswith('http') and not os.path.exists(s):
            print(f"[{h}] Missing script: {s}")
            all_good = False

# Also check data.js product images
with open('assets/js/data.js', 'r', encoding='utf-8') as f:
    js_content = f.read()

data_imgs = re.findall(r'[\"\'](assets/images/[^\"\']+)[\"\']', js_content)
for di in data_imgs:
    if not os.path.exists(di):
        print(f"[data.js] Missing image: {di}")
        all_good = False

if all_good:
    print("ALL TESTS PASSED! All pages, images, scripts, and stylesheets exist and are verified.")
else:
    print("Some assets were missing, please check output above.")
