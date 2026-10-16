const fs = require('node:fs');
const vm = require('node:vm');
const assert = require('node:assert/strict');
const source = fs.readFileSync('wp-content/themes/project-child/assets/fractional/main.min.js', 'utf8');
let checks = 0;
for (const item of [{ id: 128, prefix: 'combo_' }, { id: 999, prefix: 'combo_' }, { id: 106, prefix: 'prod_' }]) {
  const buttons = Array.from({ length: 4 }, () => ({ dataset: {} }));
  const select = {};
  const context = vm.createContext({
    window: { FCC_CONFIG: { productId: item.id }, FCC_DATA: { products: [{ id: 106 }], combos: [{ id: 128 }, { id: 999 }] } },
    document: { addEventListener() {}, querySelectorAll: () => buttons, getElementById: () => select },
  });
  vm.runInContext(source, context);
  vm.runInContext('var galleryCalls = 0; initProductImageGallery = () => { galleryCalls++; }; initProductDetailView();', context);
  for (const button of buttons) { assert.equal(button.dataset.productSelect, item.prefix + item.id); checks++; }
  assert.equal(select.value, item.prefix + item.id); checks++;
  assert.equal(context.galleryCalls, 1); checks++;
}
console.log(`PASS: ${checks} minified client checks for queried combo/product order selection and gallery initialization.`);
