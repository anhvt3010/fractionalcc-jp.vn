/* Unit checks for the actual submission script; no browser or network/email calls. */
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const defaults = JSON.parse(fs.readFileSync('wp-content/plugins/project-core/defaults/fractional.json', 'utf8'));
const labels = Object.fromEntries(Object.entries(defaults.contentManagement.clientLabels).map(([key, name]) => [key, defaults.pages.shared.fields[name].value]));
const forms = [];
const resetTimers = [];
let fetchCalls = 0;
let reply = { success: true, data: { orderCode: 'TEST', redirectUrl: '/cam-on/' } };
const sandbox = {
  document: {
    addEventListener() {}, getElementById() { return null; },
    querySelectorAll(selector) { return selector === 'form' ? forms : []; },
    createElement() { return { setAttribute() {}, textContent: '' }; }
  },
  window: { FCC_CONFIG: { nonce: 'config-nonce', ajaxUrl: '/wp-admin/admin-ajax.php', labels }, location: {} },
  sessionStorage: { setItem() {} }, setTimeout(callback, delay) { if (delay === 0) resetTimers.push(callback); }, console,
  FormData: class {
    constructor(form) {
      this.values = new Map(Object.entries(form.values));
      if (form.controls.agree_terms?.checked) this.values.set('agree_terms', 'on');
    }
    set(key, value) { this.values.set(key, value); }
    get(key) { return this.values.get(key); }
  },
  fetch: async (url, options) => {
    fetchCalls++;
    assert.equal(url, '/wp-admin/admin-ajax.php');
    assert.equal(options.body.get('action'), 'fcc_submit_order');
    assert.equal(options.body.get('_fcc_nonce'), 'form-nonce');
    assert.equal(options.body.get('agree_terms'), 'on', 'Consent must be sent to the server');
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
    agree_terms: { name: 'agree_terms', checked: agree, focus() {} }, website_url: { value: '' }
  };
  const form = {
    dataset: {}, values: { _fcc_nonce: 'form-nonce' }, listeners: {}, controls,
    button: { textContent: 'Gửi yêu cầu', disabled: false }, status: null,
    reset() { if (controls.agree_terms) controls.agree_terms.checked = false; this.listeners.reset?.(); },
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
  assert.equal(inline.button.disabled, true, 'Submit starts disabled without consent');
  inline.controls.agree_terms.checked = true;
  inline.listeners.change({target: inline.controls.agree_terms});
  assert.equal(inline.button.disabled, false, 'Checking consent enables submit');
  inline.controls.agree_terms.checked = false;
  inline.listeners.change({target: inline.controls.agree_terms});
  assert.equal(inline.button.disabled, true, 'Unchecking consent locks submit again');
  inline.controls.agree_terms.checked = true;
  inline.listeners.change({target: inline.controls.agree_terms});
  inline.reset();
  while (resetTimers.length) resetTimers.shift()();
  assert.equal(inline.button.disabled, true, 'Reset returns submit to the unchecked state');
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
  const missing = makeForm();
  delete missing.controls.agree_terms;
  sandbox.initOrderSystem();
  await sandbox.handleOrderSubmit({target: missing, preventDefault() {}});
  assert.equal(missing.button.disabled, true, 'Missing consent control fails closed');
  assert.equal(fetchCalls, 3, 'Missing consent cannot send a request');
  let complete;
  reply = new Promise(resolve => { complete = resolve; });
  const pending = makeForm();
  sandbox.initOrderSystem();
  const submission = sandbox.handleOrderSubmit({target: pending, preventDefault() {}});
  assert.equal(pending.button.disabled, true, 'Sending locks the button even with consent');
  pending.controls.agree_terms.checked = false;
  pending.listeners.change({target: pending.controls.agree_terms});
  complete({success: false, data: {message: 'Test rejection'}});
  await submission;
  assert.equal(pending.button.disabled, true, 'Late rejection cannot enable unchecked consent');
  pending.controls.agree_terms.checked = true;
  pending.listeners.change({target: pending.controls.agree_terms});
  assert.equal(pending.button.disabled, false, 'Consent allows retry after rejection');
  assert.equal(fetchCalls, 4);
  console.log('PASS: consent initial/change/reset/missing/pending states, submitted consent, validation, duplicate prevention and response errors.');
})().catch(error => { console.error(error); process.exitCode = 1; });
