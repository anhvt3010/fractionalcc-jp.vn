/** Compile the approved product workbook extraction into packaged defaults. */
import fs from 'node:fs';
import path from 'node:path';
import crypto from 'node:crypto';

const [inputPath, root] = process.argv.slice(2);
if (!inputPath || !root) throw new Error('Provide workbook extraction and project root.');
const source = JSON.parse(fs.readFileSync(inputPath, 'utf8'));
const defaultsPath = path.join(root, 'wp-content/plugins/project-core/defaults/fractional.json');
const defaults = JSON.parse(fs.readFileSync(defaultsPath, 'utf8'));
const cell = address => String(source.cells[address] ?? '').trim().replace(/\r\n?/g, '\n');
const lines = text => text.split('\n').map(value => value.trim()).filter(Boolean);
const cleanBullet = text => text.replace(/^[\s\p{So}✅✓✔☑•*\-]+/u, '').trim();
const headers = ['STT', 'Hình ảnh', 'Thương hiệu', 'Tên sản phẩm', 'Phân loại', 'Xuất xứ', 'Công dụng', 'Thành phần chính', 'Đối tượng phù hợp', 'USP / Điểm nổi bật', 'Tình trạng pháp lý', 'Số Công bố mỹ phẩm', 'Ghi chú', 'Giá', 'Giá combo'];
headers.forEach((header, i) => { if (cell(String.fromCharCode(65 + i) + '3') !== header) throw new Error(`Unexpected column ${header}.`); });
if (defaults.products.length !== 10 || source.images.length !== 10) throw new Error('Expected ten products and ten embedded images.');
function price(text) {
  const normalized = text.replace(/\u0336/g, '').replace(/\s/g, '');
  const match = normalized.match(/^([\d.,]+)(?:\(([\d.,]+)\))?$/);
  if (!match) throw new Error(`Unrecognized price: ${text}`);
  const amount = value => /^\d+\.0$/.test(value) ? Number(value) : Number(value.replace(/[.,]/g, ''));
  const salePrice = amount(match[1]);
  const originalPrice = match[2] ? amount(match[2]) : null;
  if (!Number.isSafeInteger(salePrice) || salePrice <= 0 || (originalPrice !== null && (!Number.isSafeInteger(originalPrice) || originalPrice <= salePrice))) throw new Error('Invalid price amounts.');
  return { salePrice, originalPrice };
}
const ingredientNames = {
  1: ['50.000 vi kim tự nhiên (Microneedles)', '10% Mandelic Acid kết hợp 2% Lactic Acid (AHA)', 'Salicylic Acid (BHA)', '5% Niacinamide'],
  8: ['1000ppm PDRN (DNA cá hồi)', '12% Niacinamide và 2% Arbutin', 'Glutathione kết hợp Vitamin C'],
};
const stagedImages = [];
const rows = [];
for (let row = 4; row <= 13; row++) {
  const id = Number(cell('A' + row));
  const product = defaults.products.find(item => item.id === id);
  if (!product || id !== row - 3 || product.name !== cell('D' + row)) throw new Error(`Product identity mismatch at row ${row}.`);
  const embedded = source.images.filter(image => image.row === row && image.column === 2);
  if (embedded.length !== 1) throw new Error(`Image mapping is ambiguous at row ${row}.`);
  const bytes = Buffer.from(embedded[0].data, 'base64');
  if (!bytes.subarray(0, 8).equals(Buffer.from([137,80,78,71,13,10,26,10]))) throw new Error('Embedded image is not PNG.');
  const image = `assets/images/products/excel-20261013/${product.slug}.png`;
  stagedImages.push({ image, bytes });
  const record = Object.fromEntries(headers.map((header, i) => [header, cell(String.fromCharCode(65 + i) + row)]));
  record['Hình ảnh'] = image;
  rows.push({ row, id, values: record, imageSha256: crypto.createHash('sha256').update(bytes).digest('hex') });
  if (!/^\d+\/\d+\/CBMP-QLD$/.test(record['Số Công bố mỹ phẩm'])) throw new Error(`Invalid CBMP at row ${row}.`);
  Object.assign(product, {
    brand: record['Thương hiệu'], category: record['Phân loại'], origin: record['Xuất xứ'],
    tagline: record['Công dụng'], ingredientsText: record['Thành phần chính'],
    targetUsers: lines(record['Đối tượng phù hợp']), uspBullets: lines(record['USP / Điểm nổi bật']),
    legalStatus: record['Tình trạng pháp lý'], cbmp: record['Số Công bố mỹ phẩm'], notes: record['Ghi chú'],
    ...price(record['Giá']), image, gallery: [image],
  });
  product.actives = lines(product.ingredientsText).map((description, index) => {
    const raw = cleanBullet(description);
    const name = ingredientNames[id]?.[index] ?? raw.split(':')[0].trim();
    if (!name || !raw.includes(name)) throw new Error(`Ingredient label must be present in the source at row ${row}.`);
    return { name, tag: 'Thành phần chính', desc: raw };
  });
  product.spotlightTitle = product.name;
  product.spotlightDescription = product.tagline;
  product.spotlightHighlights = product.actives.map(active => `${active.name} | ${active.desc}`).join('\n');
}
const comboRows = rows.filter(item => item.values['Giá combo']);
if (comboRows.length !== 1 || comboRows[0].id !== 6) throw new Error('Expected the approved C–CX4 combo price at O9 only.');
const combo = defaults.combos.find(item => item.code === 'C-CX4');
if (!combo || combo.productIds.join(',') !== '6,7') throw new Error('Existing C–CX4 combo composition mismatch.');
Object.assign(combo, price(comboRows[0].values['Giá combo']));
combo.saveAmount = combo.originalPrice - combo.salePrice;
const fields = ['brand', 'category', 'origin', 'tagline', 'ingredientsText', 'targetUsers', 'uspBullets', 'legalStatus', 'cbmp', 'notes', 'salePrice', 'originalPrice', 'image', 'actives', 'gallery', 'spotlightTitle', 'spotlightDescription', 'spotlightHighlights'];
const brandImage = defaults.pages.home.fields.fcc_ui_brand_image;
if (brandImage?.type === 'image' && /^\d+$/.test(String(brandImage.value))) {
  brandImage.value = 'assets/images/brand/fractional-lineup-full.jpg';
}
defaults.catalogSource = { file: source.file, sheet: source.sheet, range: 'A3:O13', sha256: source.sha256, productFields: fields, comboFields: ['salePrice', 'originalPrice', 'saveAmount'] };
// New installations must use the approved Excel card text, not old homepage snapshots.
const featured = defaults.featured.find(item => item.source === 'product_1');
if (featured) {
  const product = defaults.products.find(item => item.id === 1);
  for (const key of ['spotlightTitle', 'spotlightDescription', 'spotlightHighlights']) featured.fields[key] = { value: product[key] };
}
for (const { image, bytes } of stagedImages) {
  for (const base of ['template/fractional-cc', 'wp-content/themes/project-child/assets/fractional']) {
    const target = path.join(root, base, image);
    fs.mkdirSync(path.dirname(target), { recursive: true });
    fs.writeFileSync(target, bytes);
  }
}
fs.writeFileSync(defaultsPath, JSON.stringify(defaults, null, 4) + '\n');
fs.writeFileSync(path.join(root, 'wp-content/plugins/project-core/defaults/catalog-excel.json'), JSON.stringify({ ...defaults.catalogSource, headers, rows }, null, 2) + '\n');
console.log(`Compiled ${rows.length} product records, ${stagedImages.length} exact embedded images and C–CX4 pricing. Workbook SHA-256: ${source.sha256}`);
