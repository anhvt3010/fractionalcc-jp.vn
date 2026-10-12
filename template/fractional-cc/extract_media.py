import sys
import os
import re
import zipfile
import json
import xml.etree.ElementTree as ET

# 1. Extract product data and images from List_gioi_thieu_san_pham_doc_quyen_updated.xlsx
xlsx_path = 'List_gioi_thieu_san_pham_doc_quyen_updated.xlsx'
os.makedirs('assets/images/products', exist_ok=True)
os.makedirs('assets/images/brand', exist_ok=True)

with zipfile.ZipFile(xlsx_path, 'r') as z:
    for filename in z.namelist():
        if filename.startswith('xl/media/'):
            basename = os.path.basename(filename)
            target = os.path.join('assets/images/products', basename)
            with open(target, 'wb') as f:
                f.write(z.read(filename))
            print(f'Extracted: {filename} -> {target}')

print('Extracted all media files from excel.')
