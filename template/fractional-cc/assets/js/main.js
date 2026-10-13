/**
 * FRACTIONAL CC - Main Interactive & CRO Script
 * Handles: Order Modal, Pre-selected combos, Form validation,
 * FAQ Accordion, Mobile Menu, UTM capture, Product Catalog Filter,
 * Dynamic Product Detail loader, and Thank-you redirection.
 */

document.addEventListener('DOMContentLoaded', function () {
  // 1. Capture UTM parameters & referrer
  captureMarketingTracking();

  // 2. Initialize FAQ accordions
  initFaqAccordions();

  // 3. Initialize Mobile Navigation
  initMobileNav();

  // 4. Initialize Order Modal & Quick Buy buttons
  initOrderSystem();

  // 5. Initialize Product List Filter if on san-pham.html
  if (document.getElementById('product-catalog-grid')) {
    initCatalogFilter();
  }

  // 6. Initialize Product Detail Page if on chi-tiet-san-pham.html
  initProductDetailTabs();
  if (document.getElementById('product-detail-view')) {
    initProductDetailView();
  }

  // 7. Initialize Thank-you page data if on cam-on.html
  if (document.getElementById('thank-you-summary')) {
    initThankYouPage();
  }

  // 8. Initialize Floating Actions & Scroll to top
  initFloatingActions();

  // 9. Initialize Luxury Review Carousel Slider
  if (document.getElementById('reviews-slider-track')) {
    initReviewSlider();
  }

  // 10. Initialize Routine Collapsible & Toggle
  initRoutineCollapsible();

  // 11. Initialize Skin Concerns Tabs (van-de-lan-da)
  initConcernsTabs();
});

/**
 * Capture UTM and ad click parameters for lead tracking
 */
function captureMarketingTracking() {
  const urlParams = new URLSearchParams(window.location.search);
  const utmKeys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term', 'gclid', 'fbclid', 'ttclid'];
  const trackingData = {};

  utmKeys.forEach(key => {
    if (urlParams.has(key)) {
      trackingData[key] = urlParams.get(key);
      sessionStorage.setItem('fcc_' + key, urlParams.get(key));
    } else if (sessionStorage.getItem('fcc_' + key)) {
      trackingData[key] = sessionStorage.getItem('fcc_' + key);
    }
  });

  // Store in global window for form submissions
  window.FCC_TRACKING = trackingData;
}

/**
 * FAQ Accordion
 */
function initFaqAccordions() {
  const faqItems = document.querySelectorAll('.faq-item');
  if (!faqItems.length) return;

  faqItems.forEach(item => {
    const questionBtn = item.querySelector('.faq-question');
    const ans = item.querySelector('.faq-answer');

    // Khởi tạo trạng thái ban đầu
    if (item.classList.contains('active') && ans) {
      ans.style.maxHeight = ans.scrollHeight + 'px';
      if (questionBtn) questionBtn.setAttribute('aria-expanded', 'true');
    } else if (ans) {
      ans.style.maxHeight = '0px';
      if (questionBtn) questionBtn.setAttribute('aria-expanded', 'false');
    }

    const handleToggle = (e) => {
      // Nếu click vào nội dung câu trả lời khi đang mở thì không đóng lại (để người dùng bôi đen đọc text)
      if (e && e.target && e.target.closest('.faq-answer') && item.classList.contains('active')) return;

      const isCurrentActive = item.classList.contains('active');

      // Đóng tất cả các mục khác mượt mà
      faqItems.forEach(other => {
        if (other !== item && other.classList.contains('active')) {
          other.classList.remove('active');
          const otherAns = other.querySelector('.faq-answer');
          const otherBtn = other.querySelector('.faq-question');
          if (otherAns) otherAns.style.maxHeight = '0px';
          if (otherBtn) otherBtn.setAttribute('aria-expanded', 'false');
        }
      });

      // Bật / tắt mục hiện tại
      if (isCurrentActive) {
        item.classList.remove('active');
        if (ans) ans.style.maxHeight = '0px';
        if (questionBtn) questionBtn.setAttribute('aria-expanded', 'false');
      } else {
        item.classList.add('active');
        if (ans) ans.style.maxHeight = ans.scrollHeight + 'px';
        if (questionBtn) questionBtn.setAttribute('aria-expanded', 'true');
      }
    };

    if (questionBtn) {
      questionBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        handleToggle(e);
      });
    }

    // Cho phép bấm vào bất kỳ vị trí nào trên thẻ khi thẻ đang đóng
    item.addEventListener('click', (e) => {
      if (!item.classList.contains('active')) {
        handleToggle(e);
      }
    });
  });

  // Tự động căn chỉnh lại chiều cao khi resize cửa sổ cho câu hỏi đang mở
  window.addEventListener('resize', () => {
    document.querySelectorAll('.faq-item.active .faq-answer').forEach(ans => {
      ans.style.maxHeight = ans.scrollHeight + 'px';
    });
  });
}

/**
 * Mobile Navigation Menu
 */
function initMobileNav() {
  const toggleBtn = document.querySelector('.mobile-nav-toggle');
  const navMenu = document.querySelector('.nav-menu');

  if (toggleBtn && navMenu) {
    toggleBtn.addEventListener('click', () => {
      const isOpen = navMenu.classList.toggle('mobile-open');
      toggleBtn.classList.toggle('active', isOpen);
    });

    // Close menu when clicking a link
    navMenu.querySelectorAll('.nav-link').forEach(link => {
      link.addEventListener('click', () => {
        navMenu.classList.remove('mobile-open');
        toggleBtn.classList.remove('active');
      });
    });
  }
}

/**
 * Order & Lead Generation System
 */
function initOrderSystem() {
  const modal = document.getElementById('order-modal');
  const modalClose = document.getElementById('modal-close-btn');
  const modalSelect = document.getElementById('modal-product-select');
  const inlineSelect = document.getElementById('inline-product-select');

  // Populate selects from FCC_DATA
  if (window.FCC_DATA) {
    populateProductOptions(modalSelect);
    populateProductOptions(inlineSelect);
  }

  // Open modal trigger buttons
  document.querySelectorAll('[data-open-order]').forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      const preSelectId = this.getAttribute('data-product-select') || '';
      openOrderModal(preSelectId);
    });
  });

  // Close modal
  if (modalClose && modal) {
    modalClose.addEventListener('click', () => closeOrderModal());
    modal.addEventListener('click', (e) => {
      if (e.target === modal) closeOrderModal();
    });
  }

  // Handle Form Submissions (Inline & Modal)
  const forms = [document.getElementById('inline-order-form'), document.getElementById('modal-order-form')];
  forms.forEach(form => {
    if (form) {
      form.addEventListener('submit', handleOrderSubmit);
      form.addEventListener('input', () => form.querySelector('.order-handoff')?.remove());
    }
  });
  document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && modal?.classList.contains('active')) closeOrderModal();
  });
}

function populateProductOptions(selectEl) {
  if (!selectEl) return;
  const currentVal = selectEl.value;
  selectEl.innerHTML = '';

  const defaultOpt = document.createElement('option');
  defaultOpt.value = '';
  defaultOpt.textContent = '-- Chọn Combo hoặc Sản Phẩm --';
  selectEl.appendChild(defaultOpt);

  // Group 1: Combos
  const comboGroup = document.createElement('optgroup');
  comboGroup.label = '★ CÁC COMBO TIẾT KIỆM (KHUYÊN DÙNG)';
  window.FCC_DATA.combos.forEach(cb => {
    const opt = document.createElement('option');
    opt.value = `combo_${cb.id}`;
    opt.textContent = `${cb.name} - ${formatVND(cb.salePrice)}`;
    comboGroup.appendChild(opt);
  });
  selectEl.appendChild(comboGroup);

  // Group 2: Individual Products
  const prodGroup = document.createElement('optgroup');
  prodGroup.label = 'SẢN PHẨM ĐƠN LẺ';
  window.FCC_DATA.products.forEach(p => {
    const opt = document.createElement('option');
    opt.value = `prod_${p.id}`;
    opt.textContent = `${p.shortName} - ${formatVND(p.salePrice)}`;
    prodGroup.appendChild(opt);
  });
  selectEl.appendChild(prodGroup);

  if (currentVal) selectEl.value = currentVal;
}

function openOrderModal(preSelectValue) {
  const modal = document.getElementById('order-modal');
  const modalSelect = document.getElementById('modal-product-select');
  if (!modal) return;

  if (modalSelect && preSelectValue) {
    modalSelect.value = preSelectValue;
  }

  modal.classList.add('active');
  document.body.style.overflow = 'hidden';
}

function closeOrderModal() {
  const modal = document.getElementById('order-modal');
  if (!modal) return;
  modal.classList.remove('active');
  document.body.style.overflow = '';
}

function handleOrderSubmit(e) {
  e.preventDefault();
  const form = e.target;
  const nameInput = form.querySelector('[name="customer_name"]');
  const phoneInput = form.querySelector('[name="customer_phone"]');
  const productSelect = form.querySelector('[name="selected_item"]');
  const addressInput = form.querySelector('[name="customer_address"]');
  const noteInput = form.querySelector('[name="customer_note"]');
  const agreeCheck = form.querySelector('[name="agree_terms"]');

  // Validate Full Name
  const name = nameInput ? nameInput.value.trim() : '';
  if (name.length < 2) {
    showToast('Vui lòng nhập họ và tên của bạn (tối thiểu 2 ký tự)');
    nameInput.focus();
    return;
  }

  // Validate Vietnamese Phone Number
  const phone = phoneInput ? phoneInput.value.trim().replace(/\s+/g, '') : '';
  const phoneRegex = /^(0|\+84)(3|5|7|8|9)[0-9]{8}$/;
  if (!phoneRegex.test(phone)) {
    showToast('Số điện thoại không đúng định dạng Việt Nam (10 số, bắt đầu bằng 0 hoặc +84)');
    phoneInput.focus();
    return;
  }

  // Validate Product Selection
  if (!productSelect || !productSelect.value) {
    showToast('Vui lòng chọn Combo hoặc Sản phẩm bạn muốn đặt');
    productSelect.focus();
    return;
  }

  // Validate Terms Agreement
  if (agreeCheck && !agreeCheck.checked) {
    showToast('Vui lòng đồng ý với điều khoản tư vấn và chính sách bảo mật');
    agreeCheck.focus();
    return;
  }

  if (form.querySelector('[name="website_url"]')?.value) return;

  // Prepare a message for the customer's selected contact channel.
  const orderData = {
    orderId: 'FCC-' + Math.floor(100000 + Math.random() * 900000),
    createdAt: new Date().toISOString(),
    customerName: name,
    customerPhone: phone,
    customerAddress: addressInput ? addressInput.value.trim() : 'Chưa nhập (sẽ cung cấp khi xác nhận)',
    customerNote: noteInput ? noteInput.value.trim() : '',
    selectedItemCode: productSelect.value,
    selectedItemName: productSelect.options[productSelect.selectedIndex].text,
    tracking: window.FCC_TRACKING || {}
  };

  try {
    localStorage.setItem('fcc_last_order', JSON.stringify(orderData));
  } catch (err) {}

  renderOrderHandoff(form, orderData);
}

function renderOrderHandoff(container, order) {
  container.querySelector('.order-handoff')?.remove();
  const box = document.createElement('section');
  box.className = 'order-handoff';
  box.setAttribute('aria-live', 'polite');
  const heading = document.createElement('h3');
  heading.textContent = 'Kiểm tra & gửi yêu cầu đặt hàng';
  const note = document.createElement('p');
  note.textContent = 'Yêu cầu chưa được gửi. Sao chép nội dung và gửi qua Zalo, hoặc mở email rồi nhấn Gửi. BUI DANG-JP sẽ xác nhận sản phẩm, phí vận chuyển và thanh toán với bạn.';
  const message = ['Yêu cầu đặt hàng Fractional CC', `Họ tên: ${order.customerName}`, `Điện thoại: ${order.customerPhone}`, `Sản phẩm: ${order.selectedItemName}`, `Địa chỉ: ${order.customerAddress || 'Cung cấp khi xác nhận'}`, `Ghi chú: ${order.customerNote || 'Không'}`].join('\n');
  const text = document.createElement('textarea');
  text.value = message;
  text.readOnly = true;
  text.setAttribute('aria-label', 'Nội dung yêu cầu đặt hàng');
  const actions = document.createElement('div');
  actions.className = 'order-handoff-actions';
  const copy = document.createElement('button');
  copy.type = 'button'; copy.className = 'btn btn-outline'; copy.textContent = 'Sao chép nội dung';
  copy.addEventListener('click', async () => {
    try { await navigator.clipboard.writeText(message); showToast('Đã sao chép. Mở Zalo và dán nội dung để gửi.'); }
    catch { text.focus(); text.select(); showToast('Hãy sao chép nội dung đã chọn rồi gửi qua Zalo.'); }
  });
  const zalo = document.createElement('a');
  zalo.className = 'btn btn-primary'; zalo.href = 'https://zalo.me/0985561862'; zalo.target = '_blank'; zalo.rel = 'noopener noreferrer'; zalo.textContent = 'Mở Zalo để gửi';
  const viewReceipt = document.createElement('a');
  viewReceipt.className = 'btn btn-outline'; viewReceipt.href = 'cam-on.html'; viewReceipt.textContent = 'Xem xác nhận đơn →';
  actions.append(copy, zalo, viewReceipt); box.append(heading, note, text, actions); container.appendChild(box);
  box.scrollIntoView({behavior:'smooth', block:'nearest'});
}

/**
 * Toast Notice Utility
 */
function showToast(msg) {
  let toast = document.getElementById('fcc-toast');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'fcc-toast';
    toast.className = 'toast-notice';
    document.body.appendChild(toast);
  }
  toast.textContent = msg;
  toast.classList.add('show');
  setTimeout(() => {
    toast.classList.remove('show');
  }, 3500);
}

/**
 * Currency Formatter
 */
function formatVND(amount) {
  if (amount == null) return 'Liên hệ tư vấn';
  return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
}

/**
 * Catalog Page Filter (/san-pham.html)
 */
function initCatalogFilter() {
  const filterBtns = document.querySelectorAll('.catalog-filter-btn');
  const concernBtns = document.querySelectorAll('.concern-filter-btn');
  const searchInput = document.getElementById('catalog-search-input');
  const grid = document.getElementById('product-catalog-grid');
  const countDisplay = document.getElementById('catalog-count-display');

  let activeType = 'all';
  let activeConcern = 'all';
  let searchTerm = '';

  function renderFilteredProducts() {
    if (!window.FCC_DATA || !grid) return;
    const allProducts = window.FCC_DATA.products;

    const filtered = allProducts.filter(p => {
      // Type match
      const typeMatch = activeType === 'all' || p.categoryType === activeType;
      // Concern match
      const concernMatch = activeConcern === 'all' || p.skinConcerns.includes(activeConcern);
      // Search match
      const searchMatch = !searchTerm || 
        p.name.toLowerCase().includes(searchTerm) || 
        p.shortName.toLowerCase().includes(searchTerm) ||
        p.tagline.toLowerCase().includes(searchTerm);

      return typeMatch && concernMatch && searchMatch;
    });

    if (countDisplay) {
      countDisplay.textContent = `Hiển thị ${filtered.length}/10 sản phẩm`;
    }

    if (filtered.length === 0) {
      grid.innerHTML = `
        <div style="grid-column: 1/-1; text-align: center; padding: 48px 20px; background: #FFF; border-radius: 12px; border: 1px solid var(--color-border);">
          <p style="font-size: 18px; font-weight: 700; color: var(--color-text-main); margin-bottom: 8px;">Không tìm thấy sản phẩm phù hợp</p>
          <p style="color: var(--color-text-muted); font-size: 14px; margin-bottom: 20px;">Vui lòng thử chọn tiêu chí lọc khác hoặc để lại thông tin để chuyên viên hỗ trợ.</p>
          <button class="btn btn-outline" onclick="resetFilters()">Xem tất cả sản phẩm</button>
        </div>
      `;
      return;
    }

    grid.innerHTML = filtered.map(p => `
      <div class="product-card" data-id="${p.id}">
        <div class="product-thumb">
          <img src="${p.image}" alt="${p.name}" loading="lazy" />
          <span class="product-cbmp-tag">Số CBMP: ${p.cbmp}</span>
        </div>
        <div class="product-info">
          <span class="product-category-name">${p.origin} • ${p.spiculeCount || 'Công nghệ vi kim'}</span>
          <div class="card-rating-row">
            <span class="card-rating-stars">★★★★★</span>
            <span>${p.rating || '5.0'} (${p.reviewCount || 300}+ đánh giá)</span>
          </div>
          <h3 class="product-title" title="${p.name}">
            <a href="chi-tiet-san-pham.html?id=${p.id}">${p.shortName}</a>
          </h3>
          <p style="font-size: 12.5px; color: var(--color-text-body); margin-bottom: 12px; line-height: 1.45;">
            ${p.tagline}
          </p>
          <div class="product-price-row">
            <span class="product-price-current">${formatVND(p.salePrice)}</span>
            ${p.originalPrice ? `<span class="product-price-original">${formatVND(p.originalPrice)}</span>` : ''}
          </div>
          <div class="product-actions">
            <a href="chi-tiet-san-pham.html?id=${p.id}" class="btn-card-view">Chi tiết</a>
            <button class="btn-card-buy" data-open-order data-product-select="prod_${p.id}">Đặt mua</button>
          </div>
        </div>
      </div>
    `).join('');

    // Rebind newly created order buttons
    grid.querySelectorAll('[data-open-order]').forEach(btn => {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        const preVal = this.getAttribute('data-product-select');
        openOrderModal(preVal);
      });
    });
  }

  // Type filter click
  filterBtns.forEach(btn => {
    btn.addEventListener('click', function () {
      filterBtns.forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      activeType = this.getAttribute('data-type') || 'all';
      renderFilteredProducts();
    });
  });

  // Concern filter click
  concernBtns.forEach(btn => {
    btn.addEventListener('click', function () {
      concernBtns.forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      activeConcern = this.getAttribute('data-concern') || 'all';
      renderFilteredProducts();
    });
  });

  // Search input
  if (searchInput) {
    searchInput.addEventListener('input', function () {
      searchTerm = this.value.trim().toLowerCase();
      renderFilteredProducts();
    });
  }

  window.resetFilters = function () {
    activeType = 'all';
    activeConcern = 'all';
    searchTerm = '';
    if (searchInput) searchInput.value = '';
    filterBtns.forEach(b => b.classList.toggle('active', b.getAttribute('data-type') === 'all'));
    concernBtns.forEach(b => b.classList.toggle('active', b.getAttribute('data-concern') === 'all'));
    renderFilteredProducts();
  };

  // Initial render
  renderFilteredProducts();
}

/**
 * Dynamic Product Detail View (/chi-tiet-san-pham.html)
 */
function initProductDetailView() {
  if (!window.FCC_DATA) return;
  const urlParams = new URLSearchParams(window.location.search);
  const productId = parseInt(urlParams.get('id'), 10) || 1;
  const product = window.FCC_DATA.products.find(p => p.id === productId) || window.FCC_DATA.products[0];

  // Set Page Title
  document.title = `${product.name} | Fractional CC Nhật Bản Chính Hãng`;

  // Breadcrumbs & Heading
  const breadcrumbName = document.getElementById('pd-breadcrumb-name');
  if (breadcrumbName) breadcrumbName.textContent = product.shortName;

  const breadcrumbCategory = document.getElementById('pd-breadcrumb-category');
  if (breadcrumbCategory) breadcrumbCategory.textContent = product.category;

  const titleEl = document.getElementById('pd-title');
  if (titleEl) titleEl.textContent = product.name;

  const taglineEl = document.getElementById('pd-tagline');
  if (taglineEl) taglineEl.textContent = product.tagline;

  // Cocoon-style Hero Callout
  const calloutTitle = document.getElementById('pd-callout-title');
  if (calloutTitle) calloutTitle.textContent = product.spiculeCount ? 'CÔNG NGHỆ CHUẨN NHẬT' : 'CHUẨN TOKYO';

  const calloutSub = document.getElementById('pd-callout-sub');
  if (calloutSub) calloutSub.textContent = product.spiculeCount ? product.spiculeCount : 'TÁI TẠO TẦNG SÂU';

  const catBadge = document.getElementById('pd-category-badge');
  if (catBadge) catBadge.textContent = product.category;

  const orgBadge = document.getElementById('pd-origin-badge');
  if (orgBadge) orgBadge.textContent = product.origin;

  const volumeEl = document.getElementById('pd-volume');
  if (volumeEl) volumeEl.textContent = product.volume;

  const cbmpBadge = document.getElementById('pd-cbmp-badge');
  if (cbmpBadge) cbmpBadge.textContent = `Số CBMP: ${product.cbmp}`;

  // Specifications
  const specOrigin = document.getElementById('pd-spec-origin');
  if (specOrigin) specOrigin.textContent = product.origin;

  const specCategory = document.getElementById('pd-spec-category');
  if (specCategory) specCategory.textContent = product.category;

  const specVolume = document.getElementById('pd-spec-volume');
  if (specVolume) specVolume.textContent = product.volume;

  const specCbmp = document.getElementById('pd-spec-cbmp');
  if (specCbmp) specCbmp.textContent = product.cbmp;

  // Cocoon-style Editorial Summaries & Tab Bullets
  const targetBullets = document.getElementById('pd-target-list-bullets');
  if (targetBullets && product.targetUsers) {
    targetBullets.innerHTML = product.targetUsers.map(t => {
      const clean = t.replace(/^[\s✅✓✔☑•*–—\-]+/u, '').trim();
      return `<li><span class="pd-bullet-check" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="9" fill="#FAF6EE" stroke="#C5A059" stroke-width="1.4"/><path d="M6 10.2L8.8 13L14 7.8" stroke="#8C6A28" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span><span class="pd-bullet-text">${clean}</span></li>`;
    }).join('');
  }
  const targetSummary = document.getElementById('pd-target-summary');
  if (targetSummary && product.targetUsers) {
    targetSummary.textContent = product.targetUsers.map(t => t.replace(/^[\s✅✓✔☑•*-]+/u, '').trim()).join(' • ');
  }

  const activesBullets = document.getElementById('pd-actives-list-bullets');
  if (activesBullets && product.actives) {
    activesBullets.innerHTML = product.actives.map(a => {
      const name = typeof a === 'string' ? a : (a.name || '');
      const clean = name.replace(/^[\s✅✓✔☑•*–—\-]+/u, '').trim();
      return `<li><span class="pd-bullet-dash">—</span><span class="pd-bullet-text">${clean}</span></li>`;
    }).join('');
  }
  const activesSummary = document.getElementById('pd-actives-summary');
  if (activesSummary && product.actives) {
    activesSummary.textContent = product.actives.map(a => a.name).join(' • ');
  }

  const uspsBullets = document.getElementById('pd-usps-list-bullets');
  if (uspsBullets && product.uspBullets) {
    uspsBullets.innerHTML = product.uspBullets.map(u => {
      const clean = u.replace(/^[\s✅✓✔☑•*–—\-]+/u, '').trim();
      return `<li><span class="pd-bullet-check" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="9" fill="#FAF6EE" stroke="#C5A059" stroke-width="1.4"/><path d="M6 10.2L8.8 13L14 7.8" stroke="#8C6A28" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span><span class="pd-bullet-text">${clean}</span></li>`;
    }).join('');
  }

  // Dossier Section
  const dossierName = document.getElementById('pd-dossier-name');
  if (dossierName) dossierName.textContent = product.name;

  const dossierCbmp = document.getElementById('pd-dossier-cbmp');
  if (dossierCbmp) dossierCbmp.textContent = product.cbmp;

  const dossierStatus = document.getElementById('pd-dossier-status');
  if (dossierStatus) dossierStatus.textContent = product.legalStatus;

  const dossierOrigin = document.getElementById('pd-dossier-origin');
  if (dossierOrigin) dossierOrigin.textContent = product.origin;

  // Prices
  const salePriceEl = document.getElementById('pd-sale-price');
  if (salePriceEl) salePriceEl.textContent = formatVND(product.salePrice);

  const origPriceEl = document.getElementById('pd-original-price');
  if (origPriceEl) {
    if (product.originalPrice) {
      origPriceEl.textContent = formatVND(product.originalPrice);
      origPriceEl.hidden = false;
    } else {
      origPriceEl.hidden = true;
    }
  }

  const saveAmountEl = document.getElementById('pd-save-amount');
  if (saveAmountEl) {
    if (product.originalPrice && product.salePrice) {
      const saved = product.originalPrice - product.salePrice;
      saveAmountEl.textContent = `Tiết kiệm ${formatVND(saved)}`;
      saveAmountEl.hidden = false;
    } else {
      saveAmountEl.hidden = true;
    }
  }

  // Buy Button Label
  const buyBtnLabel = document.getElementById('pd-buy-btn-label');
  if (buyBtnLabel) {
    buyBtnLabel.textContent = product.salePrice ? `MUA NGAY – ${formatVND(product.salePrice)}` : 'MUA NGAY';
  }

  // Images
  const mainImg = document.getElementById('pd-main-image');
  if (mainImg) {
    mainImg.src = product.image;
    mainImg.alt = product.name;
  }

  // Thumbnails gallery
  const thumbWrap = document.getElementById('pd-gallery-thumbs');
  if (thumbWrap && product.gallery) {
    thumbWrap.innerHTML = product.gallery.map((imgSrc, idx) => `
      <button type="button" class="pd-thumb-btn ${idx === 0 ? 'active' : ''}" onclick="switchDetailImage('${imgSrc}', this)" aria-label="Xem ảnh ${idx + 1}">
        <img src="${imgSrc}" alt="${product.shortName} ảnh ${idx + 1}" loading="lazy" />
      </button>
    `).join('');
  }

  // USPs with SINGLE clean SVG checkmark (No double emoji!)
  const uspList = document.getElementById('pd-usp-list');
  if (uspList && product.uspBullets) {
    uspList.innerHTML = product.uspBullets.map(u => {
      const cleanText = u.replace(/^[\s✅✓✔☑•*-]+/u, '').trim();
      return `
        <li class="pd-usp-item">
          <span class="pd-usp-icon" aria-hidden="true">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20 6L9 17l-5-5"></path>
            </svg>
          </span>
          <span class="pd-usp-text">${cleanText}</span>
        </li>
      `;
    }).join('');
  }

  // Actives Grid
  const activesGrid = document.getElementById('pd-actives-grid');
  if (activesGrid && product.actives) {
    activesGrid.innerHTML = product.actives.map(a => `
      <div class="pd-active-card">
        <span class="pd-active-tag">${a.tag || 'Hoạt Chất Đột Phá'}</span>
        <h3 class="pd-active-title">${a.name}</h3>
        <p class="pd-active-desc">${a.desc}</p>
      </div>
    `).join('');
  }

  // Target Users List
  const targetList = document.getElementById('pd-target-list');
  if (targetList && product.targetUsers) {
    targetList.innerHTML = product.targetUsers.map(t => {
      const cleanText = t.replace(/^[✓\s*•-]+\s*/, '').trim();
      return `
        <li class="pd-target-item">
          <span class="pd-target-check" aria-hidden="true">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20 6L9 17l-5-5"></path>
            </svg>
          </span>
          <span>${cleanText}</span>
        </li>
      `;
    }).join('');
  }

  // How to use steps
  const howToUseList = document.getElementById('pd-steps-list');
  if (howToUseList && product.howToUse) {
    howToUseList.innerHTML = product.howToUse.map((step, idx) => {
      const cleanStep = step.replace(/^Bước\s*\d+[:.]\s*/i, '').trim();
      return `
        <div class="pd-step-row">
          <div class="pd-step-num">${idx + 1}</div>
          <p class="pd-step-text">${cleanStep}</p>
        </div>
      `;
    }).join('');
  }

  // Tingling Note
  const noteEl = document.getElementById('pd-note-text');
  if (noteEl && product.notes) {
    noteEl.textContent = product.notes;
  }

  // Pre-set CTA button for this product
  const ctaBtn = document.getElementById('pd-buy-now-btn');
  if (ctaBtn) {
    ctaBtn.setAttribute('data-product-select', `prod_${product.id}`);
  }

  const mobileBuyBtn = document.getElementById('pd-mobile-buy-btn');
  if (mobileBuyBtn) {
    mobileBuyBtn.setAttribute('data-product-select', `prod_${product.id}`);
  }

  const headerOrderBtn = document.getElementById('pd-header-order-btn');
  if (headerOrderBtn) {
    headerOrderBtn.setAttribute('data-product-select', `prod_${product.id}`);
  }

  const headerOrderBtnMob = document.getElementById('pd-header-order-btn-mob');
  if (headerOrderBtnMob) {
    headerOrderBtnMob.setAttribute('data-product-select', `prod_${product.id}`);
  }

  // Pre-select in inline form
  const inlineSelect = document.getElementById('inline-product-select');
  if (inlineSelect) {
    inlineSelect.value = `prod_${product.id}`;
  }

  // Related Combos that contain this product
  const relatedCombosWrap = document.getElementById('pd-related-combos');
  const relatedCombosSection = document.getElementById('pd-combos-section') || (relatedCombosWrap ? relatedCombosWrap.closest('.pd-combos-section') : null);
  if (relatedCombosWrap) {
    const containingCombos = (window.FCC_DATA?.combos || []).filter(cb => (cb.productIds || []).includes(product.id));
    if (containingCombos.length > 0) {
      if (relatedCombosSection) relatedCombosSection.hidden = false;
      relatedCombosWrap.innerHTML = containingCombos.map(cb => `
        <div class="combo-card ${cb.isBestSeller ? 'is-featured' : ''}">
          ${cb.isBestSeller ? '<div class="combo-top-badge">BÁN CHẠY NHẤT</div>' : ''}
          <div class="combo-card-header">
            <h4 class="combo-name">${cb.name}</h4>
            <p class="combo-ideal">${cb.idealFor}</p>
          </div>
          <div class="combo-card-body">
            <div class="combo-gift-box">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:4px;display:inline-block;vertical-align:text-top;">
                <path d="M20 12v10H4V12"/><path d="M2 7h20v5H2z"/><path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/>
              </svg>
              Quà tặng: ${cb.gift}
            </div>
            <div class="combo-price-wrap">
              ${cb.originalPrice ? `<div class="combo-original-price">${formatVND(cb.originalPrice)}</div>` : ''}
              <div class="combo-sale-price-row">
                <span class="combo-sale-price">${formatVND(cb.salePrice)}</span>
              </div>
            </div>
            <button class="btn btn-primary btn-full" data-open-order data-product-select="combo_${cb.id}">
              Đặt Combo Ưu Đãi
            </button>
          </div>
        </div>
      `).join('');
      relatedCombosWrap.querySelectorAll('[data-open-order]').forEach(button => {
        button.addEventListener('click', () => openOrderModal(button.dataset.productSelect));
      });
    } else {
      if (relatedCombosSection) {
        relatedCombosSection.hidden = true;
      }
      relatedCombosWrap.innerHTML = '';
    }
  }

  window.switchDetailImage = function (newSrc, btn) {
    if (mainImg) mainImg.src = newSrc;
    document.querySelectorAll('.pd-thumb-btn').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');
  };
}

/**
 * Thank You Page (/cam-on.html)
 */
function initThankYouPage() {
  // No server confirmation is available; never present a local draft as a submitted order.
  const box = document.getElementById('thank-you-summary');
  if (!box) return;
  box.replaceChildren();
  const title = document.createElement('h1');
  title.textContent = 'Liên hệ xác nhận đơn hàng';
  const note = document.createElement('p');
  note.textContent = 'Vui lòng gửi yêu cầu qua Zalo 0985 561 862 hoặc email jpbuidangco.ltd@gmail.com để BUI DANG-JP nhận và xác nhận đơn.';
  const link = document.createElement('a');
  link.className = 'btn btn-primary'; link.href = 'index.html#dat-hang'; link.textContent = 'Điền thông tin đặt hàng';
  box.append(title, note, link);
}

/**
 * 8. Floating Action Buttons & Scroll to Top
 */
function initFloatingActions() {
  const scrollTopBtn = document.getElementById('floating-scroll-top');
  if (scrollTopBtn) {
    scrollTopBtn.addEventListener('click', (e) => {
      e.preventDefault();
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });

    // Toggle visibility based on scroll position
    const checkScroll = () => {
      if (window.scrollY > 200) {
        scrollTopBtn.classList.add('visible');
      } else {
        scrollTopBtn.classList.remove('visible');
      }
    };

    window.addEventListener('scroll', checkScroll, { passive: true });
    checkScroll(); // check on initial load
  }
}

/**
 * 9. Luxury Review Horizontal Carousel & Filter Controller
 */
function initReviewSlider() {
  const track = document.getElementById('reviews-slider-track');
  if (!track) return;

  const prevBtn = document.getElementById('review-prev-btn');
  const nextBtn = document.getElementById('review-next-btn');
  const counterCurrent = document.getElementById('review-counter-current');
  const counterTotal = document.getElementById('review-counter-total');
  const dotsContainer = document.getElementById('reviews-slider-dots');
  const allCards = Array.from(track.querySelectorAll('.review-card'));

  // Helper: Rebuild pagination dots based on cards
  function rebuildDots() {
    if (!dotsContainer) return;
    dotsContainer.innerHTML = '';

    allCards.forEach((card, idx) => {
      const dot = document.createElement('button');
      dot.className = 'reviews-slider-dot' + (idx === 0 ? ' active' : '');
      dot.setAttribute('data-index', idx);
      dot.setAttribute('aria-label', `Xem đánh giá ${idx + 1}`);
      dot.addEventListener('click', () => {
        scrollToCardIndex(idx);
      });
      dotsContainer.appendChild(dot);
    });

    if (counterTotal) {
      counterTotal.textContent = String(allCards.length).padStart(2, '0');
    }
    updateActiveState();
  }

  // Helper: Scroll to specific card index
  function scrollToCardIndex(index) {
    if (!allCards[index]) return;
    const targetCard = allCards[index];
    const targetLeft = targetCard.offsetLeft - track.offsetLeft;
    track.scrollTo({
      left: targetLeft,
      behavior: 'smooth'
    });
  }

  // Helper: Update active dot, counter, and button disabled states
  function updateActiveState() {
    if (!allCards.length) return;

    let activeIdx = 0;
    let minDiff = Infinity;
    const currentScroll = track.scrollLeft;

    allCards.forEach((card, idx) => {
      const cardLeft = card.offsetLeft - track.offsetLeft;
      const diff = Math.abs(cardLeft - currentScroll);
      if (diff < minDiff) {
        minDiff = diff;
        activeIdx = idx;
      }
    });

    // Update Counter
    if (counterCurrent) {
      counterCurrent.textContent = String(activeIdx + 1).padStart(2, '0');
    }

    // Update Dots
    if (dotsContainer) {
      const dots = dotsContainer.querySelectorAll('.reviews-slider-dot');
      dots.forEach((dot, idx) => {
        if (idx === activeIdx) {
          dot.classList.add('active');
        } else {
          dot.classList.remove('active');
        }
      });
    }

    // Update Nav Buttons
    if (prevBtn) {
      prevBtn.disabled = currentScroll <= 10;
    }
    if (nextBtn) {
      const maxScroll = track.scrollWidth - track.clientWidth - 10;
      nextBtn.disabled = currentScroll >= maxScroll;
    }
  }

  // Previous & Next Navigation Clicks
  if (prevBtn) {
    prevBtn.addEventListener('click', () => {
      const firstCard = allCards[0];
      const step = firstCard ? firstCard.offsetWidth + 22 : 400;
      track.scrollBy({ left: -step, behavior: 'smooth' });
    });
  }

  if (nextBtn) {
    nextBtn.addEventListener('click', () => {
      const firstCard = allCards[0];
      const step = firstCard ? firstCard.offsetWidth + 22 : 400;
      track.scrollBy({ left: step, behavior: 'smooth' });
    });
  }

  // Scroll event with throttle / requestAnimationFrame
  let isScrolling = false;
  track.addEventListener('scroll', () => {
    if (!isScrolling) {
      window.requestAnimationFrame(() => {
        updateActiveState();
        isScrolling = false;
      });
      isScrolling = true;
    }
  }, { passive: true });

  // Desktop Drag-to-Scroll support
  let isDown = false;
  let startX;
  let scrollLeftStart;

  track.addEventListener('mousedown', (e) => {
    isDown = true;
    track.classList.add('is-dragging');
    startX = e.pageX - track.offsetLeft;
    scrollLeftStart = track.scrollLeft;
  });

  track.addEventListener('mouseleave', () => {
    if (isDown) {
      isDown = false;
      track.classList.remove('is-dragging');
    }
  });

  track.addEventListener('mouseup', () => {
    if (isDown) {
      isDown = false;
      track.classList.remove('is-dragging');
    }
  });

  track.addEventListener('mousemove', (e) => {
    if (!isDown) return;
    e.preventDefault();
    const x = e.pageX - track.offsetLeft;
    const walk = (x - startX) * 1.5; // Drag sensitivity
    track.scrollLeft = scrollLeftStart - walk;
  });

  // Initial setup
  rebuildDots();
}

/**
 * Routine Collapsible Toggle (Thu gọn / Mở rộng chu trình)
 */
function initRoutineCollapsible() {
  const toggleButtons = document.querySelectorAll('.routine-toggle-btn');
  toggleButtons.forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      const card = this.closest('.routine-column-card');
      if (!card) return;
      const isCollapsed = card.classList.toggle('collapsed');
      const textSpan = this.querySelector('.toggle-text');
      if (textSpan) {
        textSpan.textContent = isCollapsed ? 'Xem chi tiết' : 'Thu gọn';
      }
      this.setAttribute('aria-expanded', !isCollapsed);
    });
  });

  const summaries = document.querySelectorAll('.routine-collapsed-summary');
  summaries.forEach(summary => {
    summary.addEventListener('click', function () {
      const card = this.closest('.routine-column-card');
      if (!card) return;
      card.classList.remove('collapsed');
      const btn = card.querySelector('.routine-toggle-btn');
      if (btn) {
        const textSpan = btn.querySelector('.toggle-text');
        if (textSpan) textSpan.textContent = 'Thu gọn';
        btn.setAttribute('aria-expanded', 'true');
      }
    });
  });
}

/**
 * Product Detail Tabs: Thích hợp với / Thành phần chính
 */
function initProductDetailTabs() {
  const tabButtons = document.querySelectorAll('.pd-tab-btn');
  if (!tabButtons.length) return;

  tabButtons.forEach(button => {
    button.addEventListener('click', (e) => {
      e.preventDefault();
      const parentNav = button.closest('.pd-tabs-nav');
      if (!parentNav) return;
      const container = parentNav.closest('.pd-tabs-section') || parentNav.parentElement;
      const targetId = button.getAttribute('aria-controls');

      parentNav.querySelectorAll('.pd-tab-btn').forEach(btn => {
        const isActive = (btn === button);
        btn.classList.toggle('is-active', isActive);
        btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
      });

      if (container) {
        container.querySelectorAll('.pd-tab-pane').forEach(pane => {
          const isMatch = (pane.id === targetId);
          pane.classList.toggle('is-active', isMatch);
          if (isMatch) {
            pane.removeAttribute('hidden');
          } else {
            pane.setAttribute('hidden', '');
          }
        });
      }
    });
  });
}

/**
 * 11. Initialize Skin Concerns Tab Switcher (van-de-lan-da)
 */
function initConcernsTabs() {
  const tabBtns = document.querySelectorAll('.concern-tab-btn');
  const panes = document.querySelectorAll('.concern-tab-pane');
  if (!tabBtns.length || !panes.length) return;

  function switchTab(targetId, updateHash) {
    if (typeof updateHash === 'undefined') updateHash = true;
    var matched = false;
    tabBtns.forEach(function (btn) {
      var isTarget = btn.getAttribute('data-tab') === targetId;
      btn.classList.toggle('active', isTarget);
      btn.setAttribute('aria-selected', isTarget ? 'true' : 'false');
      if (isTarget) matched = true;
    });

    panes.forEach(function (pane) {
      var isTarget = pane.id === 'pane-' + targetId;
      pane.classList.toggle('active', isTarget);
    });

    if (matched && updateHash && window.history && window.history.replaceState) {
      window.history.replaceState(null, '', '#' + targetId);
    }
  }

  tabBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var targetId = this.getAttribute('data-tab');
      if (targetId) switchTab(targetId, true);
    });
  });

  // Activate tab from URL hash if present
  var currentHash = window.location.hash.replace('#', '');
  if (currentHash && document.getElementById('pane-' + currentHash)) {
    switchTab(currentHash, false);
  }
}


