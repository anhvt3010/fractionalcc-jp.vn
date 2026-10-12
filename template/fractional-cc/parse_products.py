import zipfile
import xml.etree.ElementTree as ET
import json
import re
import sys
import os

sys.stdout.reconfigure(encoding='utf-8')

z = zipfile.ZipFile('List_gioi_thieu_san_pham_doc_quyen_updated.xlsx')

# shared strings
ss_root = ET.fromstring(z.read('xl/sharedStrings.xml'))
strings = []
ns_ss = {'s': 'http://schemas.openxmlformats.org/spreadsheetml/2006/main'}
for si in ss_root.findall('s:si', ns_ss):
    t = si.find('s:t', ns_ss)
    if t is not None and t.text:
        strings.append(t.text)
    else:
        texts = [e.text for e in si.findall('.//s:t', ns_ss) if e.text]
        strings.append(''.join(texts))

# sheet1
sheet_root = ET.fromstring(z.read('xl/worksheets/sheet1.xml'))
ns_sheet = {'s': 'http://schemas.openxmlformats.org/spreadsheetml/2006/main'}

rows_data = {}
for row in sheet_root.findall('.//s:row', ns_sheet):
    r_idx = int(row.attrib['r'])
    cols = {}
    for c in row.findall('s:c', ns_sheet):
        col_ref = c.attrib['r']
        m = re.match(r'([A-Z]+)(\d+)', col_ref)
        col_letter = m.group(1) if m else col_ref
        t_type = c.attrib.get('t')
        v = c.find('s:v', ns_sheet)
        val = ""
        if v is not None and v.text:
            if t_type == 's':
                val = strings[int(v.text)]
            else:
                val = v.text
        cols[col_letter] = val
    rows_data[r_idx] = cols

# Drawings mapping
xml_content = z.read('xl/drawings/drawing1.xml')
rels_content = z.read('xl/drawings/_rels/drawing1.xml.rels')

rels_root = ET.fromstring(rels_content)
rel_map = {}
for r in rels_root:
    rel_map[r.attrib['Id']] = r.attrib['Target']

draw_root = ET.fromstring(xml_content)
ns_draw = {'xdr': 'http://schemas.openxmlformats.org/drawingml/2006/spreadsheetDrawing',
           'a': 'http://schemas.openxmlformats.org/drawingml/2006/main'}

row_to_image = {}
for anchor in draw_root:
    from_el = anchor.find('xdr:from', ns_draw)
    blip = anchor.find('.//a:blip', ns_draw)
    if from_el is not None and blip is not None:
        r_num = int(from_el.find('xdr:row', ns_draw).text)
        embed_id = blip.attrib.get('{http://schemas.openxmlformats.org/officeDocument/2006/relationships}embed')
        target = rel_map.get(embed_id)
        # Note: drawing row in openxml is 0-indexed! So r_num 3 in drawing is row 4 in sheet!
        row_to_image[r_num] = os.path.basename(target)

print("Row to image mapping (0-indexed row in drawing):", row_to_image)

# Headers in row 3 (which is 1-indexed)
headers = rows_data.get(3, {})
print("Headers:", headers)

products = []
# Products are in rows 4 to 13 (1-indexed)
for r in range(4, 14):
    row_info = rows_data.get(r, {})
    # drawing row is r - 1
    img_name = row_to_image.get(r - 1, "")
    
    # Let's map friendly slugs
    stt = row_info.get('A', '')
    name = row_info.get('D', '').strip()
    brand = row_info.get('C', 'Fractional CC').strip()
    category = row_info.get('E', '').strip()
    origin = row_info.get('F', '').strip()
    usage = row_info.get('G', '').strip()
    ingredients = row_info.get('H', '').strip()
    target_users = row_info.get('I', '').strip()
    usp = row_info.get('J', '').strip()
    legal_status = row_info.get('K', '').strip()
    cbmp = row_info.get('L', '').strip()
    notes = row_info.get('M', '').strip()
    
    products.append({
        'stt': stt,
        'name': name,
        'brand': brand,
        'category': category,
        'origin': origin,
        'usage': usage,
        'ingredients': ingredients,
        'target_users': target_users,
        'usp': usp,
        'legal_status': legal_status,
        'cbmp': cbmp,
        'notes': notes,
        'excel_image': img_name,
        'sheet_row': r
    })

with open('products_parsed.json', 'w', encoding='utf-8') as f:
    json.dump(products, f, ensure_ascii=False, indent=2)

print(f"Parsed {len(products)} products into products_parsed.json")
for p in products:
    print(f"#{p['stt']}: {p['name']} -> Image: {p['excel_image']}")
