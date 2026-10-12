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
  if (document.getElementById('receipt-container') || document.getElementById('thank-you-summary')) {
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
});

/**
 * Capture UTM and ad click parameters for lead tracking
 */
function captureMarketingTracking() {
  const urlParams = new URLSearchParams(window.location.search);
  const utmKeys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term', 'gclid', 'fbclid', 'ttclid'];
  const trackingData = {};
  try {

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
  } catch { window.FCC_TRACKING = {}; }
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
    navMenu.id = navMenu.id || 'fcc-mobile-menu';
    toggleBtn.setAttribute('aria-controls', navMenu.id);
    toggleBtn.setAttribute('aria-expanded', 'false');
    const closeMenu = () => {
      navMenu.classList.remove('mobile-open');
      toggleBtn.classList.remove('active');
      toggleBtn.setAttribute('aria-expanded', 'false');
    };
    toggleBtn.addEventListener('click', () => {
      const isOpen = navMenu.classList.toggle('mobile-open');
      toggleBtn.classList.toggle('active', isOpen);
      toggleBtn.setAttribute('aria-expanded', String(isOpen));
    });

    // Close menu when clicking a link
    navMenu.querySelectorAll('.nav-link').forEach(link => {
      link.addEventListener('click', () => {
        closeMenu();
      });
    });
    document.addEventListener('keydown', event => {
      if (event.key === 'Escape' && navMenu.classList.contains('mobile-open')) {
        closeMenu();
        toggleBtn.focus();
      }
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
  const forms = Array.from(document.querySelectorAll('form')).filter(form => form.querySelector('[name=selected_item]'));
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
  if (!modal) { window.location.href = window.FCC_CONFIG.homeUrl + '#dat-hang'; return; }

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

async function handleOrderSubmit(e) {
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

  const phone = phoneInput ? phoneInput.value.trim().replace(/\s+/g, '') : '';
  const phoneRegex = /^(0|\+84)(3|5|7|8|9)[0-9]{8}$/;
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
  zalo.className = 'btn btn-primary'; zalo.href = window.FCC_DATA.brand.zaloLink; zalo.target = '_blank'; zalo.rel = 'noopener noreferrer'; zalo.textContent = 'Mở Zalo để gửi';
  const viewReceipt = document.createElement('a');
  viewReceipt.className = 'btn btn-outline'; viewReceipt.href = window.FCC_CONFIG.thankyouUrl; viewReceipt.textContent = 'Xem xác nhận đơn →';
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
  const grid = document.getElementById('product-catalog-grid');
  if (!grid) return;
  const cards = Array.from(grid.querySelectorAll('.product-card'));
  const typeButtons = document.querySelectorAll('button.catalog-filter-btn');
  const concernButtons = document.querySelectorAll('button.concern-filter-btn');
  const search = document.getElementById('catalog-search-input');
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
    if (count) count.textContent = 'Hiển thị ' + visible + '/' + cards.length + ' sản phẩm & combo';
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
}

function initProductDetailView() {
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
}

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

function initThankYouPage() {
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
}

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

