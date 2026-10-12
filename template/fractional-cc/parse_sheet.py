import zipfile
import xml.etree.ElementTree as ET
import json

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
        # maybe rich text
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
        # col letter
        import re
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

# print header row (usually row 3)
print("Row 3:", rows_data.get(3))
for r in range(4, 14):
    print(f"\n--- Row {r} ---")
    for k, v in rows_data.get(r, {}).items():
        if v:
            print(f"  {k}: {v[:80]}...")
