/** Build the design's UI interactions against server-rendered WordPress content. */
import { readFileSync, writeFileSync } from 'node:fs';
import { resolve } from 'node:path';

const root = resolve(import.meta.dirname, '..');
let source = readFileSync(resolve(root, 'template/fractional-cc/assets/js/main.js'), 'utf8');
function replaceFunction(name, replacement) {
  const start = source.indexOf(`function ${name}(`);
  if (start < 0) throw new Error(`Missing source function ${name}`);
  const following = /^function \w+\(/gm;
  following.lastIndex = start + 1;
  const next = following.exec(source);
  const end = next ? next.index : source.length;
  source = source.slice(0, start) + replacement + '\n\n' + source.slice(end);
}

replaceFunction('initCatalogFilter', `function initCatalogFilter() {
  const grid = document.getElementById('product-catalog-grid');
  if (!grid) return;
  const cards = Array.from(grid.querySelectorAll('.product-card'));
  const typeButtons = document.querySelectorAll('.filter-btn');
  const concernButtons = document.querySelectorAll('.concern-btn');
  const search = document.getElementById('product-search');
  const count = document.getElementById('catalog-count-display');
  let type = 'all', concern = 'all', term = '';
  const empty = document.createElement('div');
  empty.style.cssText = 'grid-column:1/-1;text-align:center;padding:48px 20px;background:#FFF;border-radius:12px;border:1px solid var(--color-border)';
  const heading = document.createElement('p');
  heading.textContent = 'Không tìm thấy sản phẩm phù hợp';
  heading.style.cssText = 'font-size:18px;font-weight:700;margin-bottom:8px';
  const reset = document.createElement('button');
  reset.className = 'btn btn-outline'; reset.textContent = 'Xem tất cả sản phẩm';
  empty.append(heading, reset); grid.append(empty);
  function render() {
    let visible = 0;
    cards.forEach(card => {
      const matches = (type === 'all' || card.dataset.type === type) &&
        (concern === 'all' || (card.dataset.concerns || '').split(' ').includes(concern)) &&
        (!term || (card.dataset.search || '').toLocaleLowerCase('vi').includes(term));
      card.hidden = !matches; if (matches) visible++;
    });
    empty.hidden = visible > 0;
    if (count) count.textContent = 'Hiển thị ' + visible + '/' + cards.length + ' sản phẩm';
  }
  function bind(buttons, setValue) {
    buttons.forEach(button => button.addEventListener('click', () => {
      buttons.forEach(item => item.classList.remove('active')); button.classList.add('active');
      setValue(button); render();
    }));
  }
  bind(typeButtons, button => { type = button.dataset.type || 'all'; });
  bind(concernButtons, button => { concern = button.dataset.concern || 'all'; });
  if (search) search.addEventListener('input', () => { term = search.value.trim().toLocaleLowerCase('vi'); render(); });
  window.resetFilters = () => {
    type = concern = 'all'; term = ''; if (search) search.value = '';
    [...typeButtons, ...concernButtons].forEach(button => button.classList.toggle('active', button.dataset.type === 'all' || button.dataset.concern === 'all'));
    render();
  };
  reset.addEventListener('click', window.resetFilters); render();
}`);

replaceFunction('initProductDetailView', `function initProductDetailView() {
  const product = window.FCC_DATA?.products.find(item => item.id === window.FCC_CONFIG?.productId);
  if (!product) return;
  document.querySelectorAll('#pd-buy-now-btn,#pd-mobile-buy-btn,#pd-header-order-btn,#pd-header-order-btn-mob').forEach(button => {
    button.dataset.productSelect = 'prod_' + product.id;
  });
  const inlineSelect = document.getElementById('inline-product-select');
  if (inlineSelect) inlineSelect.value = 'prod_' + product.id;
  const image = document.getElementById('pd-main-image');
  document.querySelectorAll('[data-detail-image]').forEach(button => button.addEventListener('click', () => {
    if (image) image.src = button.dataset.detailImage;
    document.querySelectorAll('[data-detail-image]').forEach(item => item.classList.remove('active'));
    button.classList.add('active');
  }));
}`);

replaceFunction('handleOrderSubmit', `async function handleOrderSubmit(e) {
  e.preventDefault();
  const form = e.target;
  const nameInput = form.querySelector('[name="customer_name"]');
  const phoneInput = form.querySelector('[name="customer_phone"]');
  const productSelect = form.querySelector('[name="selected_item"]');
  const addressInput = form.querySelector('[name="customer_address"]');
  const noteInput = form.querySelector('[name="customer_note"]');
  const agreeCheck = form.querySelector('[name="agree_terms"]');
  const submitBtn = form.querySelector('button[type="submit"]') || form.querySelector('.btn-primary');

  const name = nameInput ? nameInput.value.trim() : '';
  if (name.length < 2) {
    showToast('Vui lòng nhập họ và tên của bạn (tối thiểu 2 ký tự)');
    nameInput?.focus();
    return;
  }

  const phone = phoneInput ? phoneInput.value.trim().replace(/\\s+/g, '') : '';
  const phoneRegex = /^(0|\\+84)(3|5|7|8|9)[0-9]{8}$/;
  if (!phoneRegex.test(phone)) {
    showToast('Số điện thoại không đúng định dạng Việt Nam (10 số, bắt đầu bằng 0 hoặc +84)');
    phoneInput?.focus();
    return;
  }

  if (!productSelect || !productSelect.value) {
    showToast('Vui lòng chọn Combo hoặc Sản phẩm bạn muốn đặt');
    productSelect?.focus();
    return;
  }

  if (agreeCheck && !agreeCheck.checked) {
    showToast('Vui lòng đồng ý với điều khoản tư vấn và chính sách bảo mật');
    agreeCheck?.focus();
    return;
  }

  if (form.querySelector('[name="website_url"]')?.value) return;

  const originalBtnText = submitBtn ? submitBtn.textContent : '';
  if (submitBtn) {
    submitBtn.disabled = true;
    submitBtn.textContent = 'Đang gửi đơn hàng...';
  }

  const formData = new FormData(form);
  formData.set('action', 'fcc_submit_order');
  formData.set('_fcc_nonce', window.FCC_CONFIG?.nonce || '');
  formData.set('selected_item_name', productSelect.options[productSelect.selectedIndex]?.text || '');

  try {
    const endpoint = window.FCC_CONFIG?.ajaxUrl || window.FCC_CONFIG?.endpoint || '/wp-admin/admin-ajax.php';
    const response = await fetch(endpoint, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    });

    const result = await response.json();

    if (result && result.success) {
      showToast(result.data?.message || 'Đặt hàng thành công! BUI DANG-JP sẽ liên hệ với bạn sớm nhất.');
      form.reset();

      try {
        sessionStorage.setItem('fcc_order_code', result.data?.orderCode || '');
        sessionStorage.setItem('fcc_order_name', result.data?.customerName || name);
        sessionStorage.setItem('fcc_order_phone', result.data?.customerPhone || phone);
        sessionStorage.setItem('fcc_order_product', result.data?.selectedItem || '');
      } catch (e) {}

      const modal = document.getElementById('order-modal');
      if (modal && modal.classList.contains('active')) {
        setTimeout(() => {
          modal.classList.remove('active');
          document.body.style.overflow = '';
        }, 1200);
      }

      setTimeout(() => {
        window.location.href = result.data?.redirectUrl || window.FCC_CONFIG?.thankyouUrl || '/cam-on/';
      }, 1500);
    } else {
      const errMsg = result?.data?.message || 'Có lỗi xảy ra khi gửi đơn. Vui lòng thử lại hoặc liên hệ qua Zalo/Hotline.';
      showToast(errMsg);
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.textContent = originalBtnText;
      }
    }
  } catch (err) {
    form.submit();
  }
}`);

replaceFunction('initThankYouPage', `function initThankYouPage() {
  const urlParams = new URLSearchParams(window.location.search);
  const orderFromUrl = urlParams.get('order');
  let orderCode = orderFromUrl || '';
  let customerName = '';
  let customerPhone = '';
  let productName = '';

  try {
    orderCode = orderCode || sessionStorage.getItem('fcc_order_code') || '';
    customerName = sessionStorage.getItem('fcc_order_name') || '';
    customerPhone = sessionStorage.getItem('fcc_order_phone') || '';
    productName = sessionStorage.getItem('fcc_order_product') || '';
  } catch (e) {}

  if (orderCode) {
    const codeEl = document.getElementById('order-id-val');
    if (codeEl) codeEl.textContent = orderCode;
  }
  if (customerName) {
    const nameEl = document.getElementById('order-name-val');
    if (nameEl) nameEl.textContent = customerName;
  }
  if (customerPhone) {
    const phoneEl = document.getElementById('order-phone-val');
    if (phoneEl) {
      phoneEl.textContent = customerPhone.length >= 7 
        ? customerPhone.slice(0, 4) + ' ••• ' + customerPhone.slice(-3) 
        : customerPhone;
    }
  }
  if (productName) {
    const prodEl = document.getElementById('order-product-val');
    if (prodEl) prodEl.textContent = productName;
  }
}`);

source = source.replace("if (document.getElementById('thank-you-summary'))", "if (document.getElementById('receipt-container') || document.getElementById('thank-you-summary'))");
source = source.replace(/\s*try \{\s*localStorage\.setItem\('fcc_last_order', JSON\.stringify\(orderData\)\);\s*\} catch \(err\) \{\}/, '');
source = source.replace(/\s*orderId: 'FCC-' \+ Math\.floor\(100000 \+ Math\.random\(\) \* 900000\),/, '');
source = source.replace("zalo.href = 'https://zalo.me/0985561862'", "zalo.href = window.FCC_DATA.brand.zaloLink");
source = source.replace("viewReceipt.href = 'cam-on.html'", "viewReceipt.href = window.FCC_CONFIG.thankyouUrl");
source = source.replace('if (!modal) return;', "if (!modal) { window.location.href = window.FCC_CONFIG.homeUrl + '#dat-hang'; return; }");
source = source.replace("const forms = [document.getElementById('inline-order-form'), document.getElementById('modal-order-form')];", "const forms = Array.from(document.querySelectorAll('form')).filter(form => form.querySelector('[name=selected_item]'));");
source = source.replace("const search = document.getElementById('product-search');", "const search = document.getElementById('catalog-search-input');");
source = source.replace("document.querySelectorAll('.filter-btn')", "document.querySelectorAll('button.catalog-filter-btn')");
source = source.replace("document.querySelectorAll('.concern-btn')", "document.querySelectorAll('button.concern-filter-btn')");
source = source.replace("const trackingData = {};", "const trackingData = {};\n  try {");
source = source.replace('window.FCC_TRACKING = trackingData;', "window.FCC_TRACKING = trackingData;\n  } catch { window.FCC_TRACKING = {}; }");
writeFileSync(resolve(root, 'wp-content/themes/project-child/assets/fractional/main.js'), source);
console.log('Adapted UI behavior; catalog/detail content is server-rendered and personal data is not kept in localStorage.');
