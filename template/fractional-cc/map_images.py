import zipfile
import xml.etree.ElementTree as ET

z = zipfile.ZipFile('List_gioi_thieu_san_pham_doc_quyen_updated.xlsx')
for name in z.namelist():
    if 'drawing' in name:
        print('Drawing file:', name)

if 'xl/drawings/drawing1.xml' in z.namelist():
    xml_content = z.read('xl/drawings/drawing1.xml')
    rels_content = z.read('xl/drawings/_rels/drawing1.xml.rels')
    
    # parse rels
    rels_root = ET.fromstring(rels_content)
    rel_map = {}
    for r in rels_root:
        rel_map[r.attrib['Id']] = r.attrib['Target']
    
    # parse drawing
    draw_root = ET.fromstring(xml_content)
    # namespaces
    ns = {'xdr': 'http://schemas.openxmlformats.org/drawingml/2006/spreadsheetDrawing',
          'a': 'http://schemas.openxmlformats.org/drawingml/2006/main',
          'r': 'http://schemas.openxmlformats.org/officeDocument/2006/relationships'}
    
    for anchor in draw_root:
        from_el = anchor.find('xdr:from', ns)
        blip = anchor.find('.//a:blip', ns)
        if from_el is not None and blip is not None:
            row = from_el.find('xdr:row', ns).text
            col = from_el.find('xdr:col', ns).text
            embed_id = blip.attrib.get('{http://schemas.openxmlformats.org/officeDocument/2006/relationships}embed')
            target = rel_map.get(embed_id)
            print(f"Row {row}, Col {col} -> {embed_id} -> {target}")
