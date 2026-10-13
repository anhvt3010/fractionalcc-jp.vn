/* Unit checks for the actual submission script; no browser or network/email calls. */
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const forms = [];
let fetchCalls = 0;
let reply = { success: true, data: { orderCode: 'TEST', redirectUrl: '/cam-on/' } };
const sandbox = {
  document: {
    addEventListener() {}, getElementById() { return null; },
    querySelectorAll(selector) { return selector === 'form' ? forms : []; },
    createElement() { return { setAttribute() {}, textContent: '' }; }
  },
  window: { FCC_CONFIG: { nonce: 'config-nonce', ajaxUrl: '/wp-admin/admin-ajax.php' }, location: {} },
  sessionStorage: { setItem() {} }, setTimeout() {}, console,
  FormData: class {
    constructor(form) { this.values = new Map(Object.entries(form.values)); }
    set(key, value) { this.values.set(key, value); }
    get(key) { return this.values.get(key); }
  },
  fetch: async (url, options) => {
    fetchCalls++;
    assert.equal(url, '/wp-admin/admin-ajax.php');
    assert.equal(options.body.get('action'), 'fcc_submit_order');
    assert.equal(options.body.get('_fcc_nonce'), 'form-nonce');
    if (reply instanceof Error) throw reply;
    return { json: async () => reply };
  }
};
vm.createContext(sandbox);
vm.runInContext(fs.readFileSync('wp-content/themes/project-child/assets/fractional/main.js', 'utf8'), sandbox);
sandbox.showToast = () => {};
function makeForm(agree = true) {
  const controls = {
    customer_name: { value: 'Kiểm tra kỹ thuật', focus() {} },
    customer_phone: { value: '0900000000', focus() {} },
    selected_item: { value: 'prod_28', options: [{ text: 'Sản phẩm kiểm tra' }], selectedIndex: 0, focus() {} },
    agree_terms: { checked: agree, focus() {} }, website_url: { value: '' }
  };
  const form = {
    dataset: {}, values: { _fcc_nonce: 'form-nonce' }, listeners: {},
    button: { textContent: 'Gửi yêu cầu', disabled: false }, status: null, reset() {},
    submit() { throw new Error('Must not automatically submit again'); },
    querySelector(selector) {
      if (selector === '.fcc-order-status') return this.status;
      if (selector === 'button[type="submit"]') return this.button;
      const match = selector.match(/\[name=["']?([^"'\]]+)/);
      return match ? controls[match[1]] || null : null;
    },
    addEventListener(name, handler) { this.listeners[name] = handler; },
    appendChild(node) { this.status = node; }
  };
  form.button.insertAdjacentElement = (where, node) => { form.status = node; };
  forms.push(form);
  return form;
}
(async () => {
  const inline = makeForm(false);
  sandbox.initOrderSystem();
  inline.listeners.invalid({ target: { name: 'agree_terms' } });
  assert.match(inline.status.textContent, /tích ô đồng ý/);
  await sandbox.handleOrderSubmit({ target: inline, preventDefault() {} });
  assert.equal(fetchCalls, 0, 'Unchecked required checkbox must not send');
  const popup = makeForm();
  await sandbox.handleOrderSubmit({ target: popup, preventDefault() {} });
  assert.equal(fetchCalls, 1);
  assert.match(popup.status.textContent, /tiếp nhận/);
  await sandbox.handleOrderSubmit({ target: popup, preventDefault() {} });
  assert.equal(fetchCalls, 1, 'Busy form must not submit twice');
  reply = { success: false, data: { message: 'Phiên làm việc hết hạn' } };
  const rejected = makeForm();
  await sandbox.handleOrderSubmit({ target: rejected, preventDefault() {} });
  assert.equal(rejected.button.disabled, false);
  assert.equal(rejected.status.textContent, 'Phiên làm việc hết hạn');
  reply = new Error('Uncertain network response');
  const network = makeForm();
  await sandbox.handleOrderSubmit({ target: network, preventDefault() {} });
  assert.equal(network.button.disabled, false);
  assert.equal(network.button.textContent, 'Gửi yêu cầu');
  assert.match(network.status.textContent, /Chưa xác nhận/);
  assert.equal(fetchCalls, 3, 'No fallback retry after a failed response');
  console.log('PASS: native-invalid feedback, required agreement, nonce, successful submission, duplicate prevention, server error and uncertain-network handling.');
})().catch(error => { console.error(error); process.exitCode = 1; });
