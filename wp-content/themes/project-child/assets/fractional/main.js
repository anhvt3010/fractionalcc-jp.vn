function fccLabel(key) { return window.FCC_CONFIG?.labels?.[key] || ''; }

/**
 * FRACTIONAL CC - Main Interactive & CRO Script
 * Handles: Order Modal, Pre-selected combos, Form validation,
 * FAQ Accordion, Mobile Menu, UTM capture, Product Catalog Filter,
 * Dynamic Product Detail loader, and Thank-you redirection.
 */

document.addEventListener('DOMContentLoaded', function () {
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

  // 12. Initialize Feature Pillars Stack Deck on Mobile
  initFeaturePillarsStack();

  // 13. Initialize Routine Process Stack Deck on Mobile (Section 06)

  // 14. Initialize Policy Page Navigation & Scrollspy
  initPolicyNav();
});

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
      form.addEventListener('change', event => {
        if (event.target.name !== 'agree_terms') return;
        syncOrderSubmitState(form);
        const status = form.querySelector('.fcc-order-status');
        if (event.target.checked && form.dataset.submitting !== 'true' && status) status.textContent = '';
      });
      form.addEventListener('reset', () => setTimeout(() => syncOrderSubmitState(form), 0));
      syncOrderSubmitState(form);
      form.addEventListener('invalid', event => {
        const message = event.target.name === 'agree_terms'
          ? fccLabel('message_1')
          : event.target.name === 'selected_item'
            ? fccLabel('message_2')
            : event.target.validationMessage;
        showOrderFormMessage(form, message);
      }, true);
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
  defaultOpt.textContent = fccLabel('message_3');
  selectEl.appendChild(defaultOpt);

  // Group 1: Combos
  const comboGroup = document.createElement('optgroup');
  comboGroup.label = fccLabel('message_4');
  window.FCC_DATA.combos.forEach(cb => {
    const opt = document.createElement('option');
    opt.value = `combo_${cb.id}`;
    opt.textContent = `${cb.name} - ${formatVND(cb.salePrice)}`;
    comboGroup.appendChild(opt);
  });
  selectEl.appendChild(comboGroup);

  // Group 2: Individual Products
  const prodGroup = document.createElement('optgroup');
  prodGroup.label = fccLabel('message_5');
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

  const form = modal.querySelector('form');
  if (form) syncOrderSubmitState(form);
  modal.classList.add('active');
  document.body.style.overflow = 'hidden';
}

function closeOrderModal() {
  const modal = document.getElementById('order-modal');
  if (!modal) return;
  modal.classList.remove('active');
  document.body.style.overflow = '';
}

function showOrderFormMessage(form, message) {
  let status = form.querySelector('.fcc-order-status');
  if (!status) {
    status = document.createElement('p');
    status.className = 'fcc-order-status';
    status.setAttribute('role', 'status');
    status.setAttribute('aria-live', 'polite');
    const button = form.querySelector('button[type="submit"]');
    if (button) button.insertAdjacentElement('beforebegin', status);
    else form.appendChild(status);
  }
  status.textContent = message;
}

/** Consent and in-flight requests jointly control every order submit button. */
function syncOrderSubmitState(form) {
  const agreement = form.querySelector('[name="agree_terms"]');
  const button = form.querySelector('button[type="submit"]');
  if (button) button.disabled = !agreement?.checked || form.dataset.submitting === 'true';
}

async function handleOrderSubmit(e) {
  e.preventDefault();
  const form = e.target;
  if (form.dataset.submitting === 'true') return;
  const reportError = message => {
    showOrderFormMessage(form, message);
    showToast(message);
  };
  const nameInput = form.querySelector('[name="customer_name"]');
  const phoneInput = form.querySelector('[name="customer_phone"]');
  const productSelect = form.querySelector('[name="selected_item"]');
  const addressInput = form.querySelector('[name="customer_address"]');
  const noteInput = form.querySelector('[name="customer_note"]');
  const agreeCheck = form.querySelector('[name="agree_terms"]');
  const submitBtn = form.querySelector('button[type="submit"]') || form.querySelector('.btn-primary');

  const name = nameInput ? nameInput.value.trim() : '';
  if (name.length < 2) {
    reportError(fccLabel('message_6'));
    nameInput?.focus();
    return;
  }

  const phone = phoneInput ? phoneInput.value.trim().replace(/\s+/g, '') : '';
  const phoneRegex = /^(0|\+84)(3|5|7|8|9)[0-9]{8}$/;
  if (!phoneRegex.test(phone)) {
    reportError(fccLabel('message_7'));
    phoneInput?.focus();
    return;
  }

  if (!productSelect || !productSelect.value) {
    reportError(fccLabel('message_8'));
    productSelect?.focus();
    return;
  }

  if (!agreeCheck || !agreeCheck.checked) {
    reportError(fccLabel('message_1'));
    agreeCheck?.focus();
    return;
  }

  if (form.querySelector('[name="website_url"]')?.value) return;

  const originalBtnText = submitBtn ? submitBtn.textContent : '';
  form.dataset.submitting = 'true';
  showOrderFormMessage(form, fccLabel('message_9'));
  if (submitBtn) {
    submitBtn.disabled = true;
    submitBtn.textContent = fccLabel('message_10');
  }

  const formData = new FormData(form);
  formData.set('action', 'fcc_submit_order');
  formData.set('_fcc_nonce', formData.get('_fcc_nonce') || window.FCC_CONFIG?.nonce || '');
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
      showOrderFormMessage(form, result.data?.message || fccLabel('message_11'));
      showToast(result.data?.message || fccLabel('message_12'));
      form.reset();

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
      const errMsg = result?.data?.message || fccLabel('message_13');
      reportError(errMsg);
      form.dataset.submitting = 'false';
      if (submitBtn) {
        submitBtn.textContent = originalBtnText;
      }
      syncOrderSubmitState(form);
    }
  } catch (err) {
    // A failed response can arrive after the server saved/sent the order; never resubmit automatically.
    reportError(fccLabel('message_14'));
    form.dataset.submitting = 'false';
    if (submitBtn) {
      submitBtn.textContent = originalBtnText;
    }
    syncOrderSubmitState(form);
  }
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
  if (amount == null) return fccLabel('message_15');
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
  heading.textContent = fccLabel('message_16');
  heading.style.cssText = 'font-size:18px;font-weight:700;margin-bottom:8px';
  const reset = document.createElement('button');
  reset.className = 'btn btn-outline'; reset.textContent = fccLabel('message_17');
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
    if (count) count.textContent = fccLabel('message_18') + visible + '/' + cards.length + fccLabel('message_19');
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
  const id = window.FCC_CONFIG?.productId;
  const combo = window.FCC_DATA?.combos.find(item => item.id === id);
  const product = combo || window.FCC_DATA?.products.find(item => item.id === id);
  if (!product) return;
  const orderCode = (combo ? 'combo_' : 'prod_') + product.id;
  document.querySelectorAll('#pd-buy-now-btn,#pd-mobile-buy-btn,#pd-header-order-btn,#pd-header-order-btn-mob').forEach(button => {
    button.dataset.productSelect = orderCode;
  });
  const inlineSelect = document.getElementById('inline-product-select');
  if (inlineSelect) inlineSelect.value = orderCode;
  initProductImageGallery();
}

function initProductImageGallery() {
  const image = document.getElementById('pd-main-image');
  const buttons = Array.from(document.querySelectorAll('#pd-gallery-thumbs [data-detail-image]'));
  const trigger = document.getElementById('pd-image-open');
  const dialog = document.getElementById('pd-image-dialog');
  if (!image || !buttons.length) return;
  let currentIndex = Math.max(0, buttons.findIndex(button => button.dataset.detailImage === image.src));
  let popupButtons = [];

  function selectImage(index) {
    currentIndex = (index + buttons.length) % buttons.length;
    const selected = buttons[currentIndex];
    image.src = selected.dataset.detailImage;
    image.alt = selected.dataset.detailAlt || '';
    if (trigger) trigger.href = image.src;
    buttons.forEach((button, position) => {
      button.classList.toggle('active', position === currentIndex);
      button.setAttribute('aria-pressed', String(position === currentIndex));
    });
    if (dialog?.open) syncDialog();
  }
  buttons.forEach((button, index) => button.addEventListener('click', () => selectImage(index)));
  // The image link remains usable when the browser cannot open native dialogs.
  if (!trigger || !dialog || typeof dialog.showModal !== 'function') return;

  const dialogImage = document.getElementById('pd-dialog-image');
  const dialogThumbs = document.getElementById('pd-image-dialog-thumbs');
  const count = document.getElementById('pd-image-dialog-count');
  const previous = document.getElementById('pd-image-prev');
  const next = document.getElementById('pd-image-next');
  let previousOverflow = '';

  function syncDialog() {
    dialogImage.src = image.src;
    dialogImage.alt = image.alt;
    count.textContent = `${currentIndex + 1} / ${buttons.length}`;
    popupButtons.forEach((button, index) => {
      button.classList.toggle('active', index === currentIndex);
      button.setAttribute('aria-pressed', String(index === currentIndex));
    });
    popupButtons[currentIndex]?.scrollIntoView({ block: 'nearest', inline: 'nearest' });
  }

  trigger.addEventListener('click', event => {
    event.preventDefault();
    if (dialog.open) return;
    if (!popupButtons.length) {
      popupButtons = buttons.map((button, index) => {
        const thumbnail = button.cloneNode(true);
        thumbnail.removeAttribute('data-detail-image');
        thumbnail.removeAttribute('data-detail-alt');
        thumbnail.addEventListener('click', () => selectImage(index));
        return thumbnail;
      });
      dialogThumbs.replaceChildren(...popupButtons);
    }
    previousOverflow = document.body.style.overflow;
    dialog.showModal();
    document.body.style.overflow = 'hidden';
    syncDialog();
  });
  previous.disabled = next.disabled = buttons.length < 2;
  previous.addEventListener('click', () => selectImage(currentIndex - 1));
  next.addEventListener('click', () => selectImage(currentIndex + 1));
  document.getElementById('pd-image-close').addEventListener('click', () => dialog.close());
  dialog.addEventListener('click', event => {
    if (event.target !== dialog) return;
    const rect = dialog.getBoundingClientRect();
    if (event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom) dialog.close();
  });
  dialog.addEventListener('keydown', event => {
    if (event.altKey || event.ctrlKey || event.metaKey) return;
    if (event.key === 'Tab') {
      const controls = Array.from(dialog.querySelectorAll('button:not([disabled])'));
      const first = controls[0];
      const last = controls[controls.length - 1];
      if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
      } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
      }
    }
    if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
      event.preventDefault();
      selectImage(currentIndex + (event.key === 'ArrowRight' ? 1 : -1));
    }
  });
  dialog.addEventListener('close', () => {
    document.body.style.overflow = previousOverflow;
    trigger.focus({ preventScroll: true });
  });
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
    const maxScroll = track.scrollWidth - track.clientWidth - 10;

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
        textSpan.textContent = isCollapsed ? fccLabel('message_20') : fccLabel('message_21');
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
        if (textSpan) textSpan.textContent = fccLabel('message_21');
        btn.setAttribute('aria-expanded', 'true');
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

/**
 * 12. Feature Pillars Stack Deck on Mobile (Section 04)
 * Allows tapping stacked cards to expand active card and collapse others.
 */
function initFeaturePillarsStack() {
  const grid = document.querySelector('.brief-feature-grid');
  if (!grid) return;

  const cards = grid.querySelectorAll('.feature-pillar-card');
  if (!cards.length) return;

  function isMobileDeck() {
    return window.matchMedia('(max-width: 767px)').matches;
  }

  function toggleCard(targetCard) {
    if (!isMobileDeck()) return;

    const isAlreadyActive = targetCard.classList.contains('is-active');

    // Close all cards
    cards.forEach(function (card) {
      card.classList.remove('is-active');
      card.setAttribute('aria-expanded', 'false');
    });

    // If not active, expand it; if active, collapse it back into the stack
    if (!isAlreadyActive) {
      targetCard.classList.add('is-active');
      targetCard.setAttribute('aria-expanded', 'true');
    }
  }

  cards.forEach(function (card) {
    // Header click toggles open/close
    const header = card.querySelector('.feature-pillar-header');
    if (header) {
      header.addEventListener('click', function (e) {
        if (!isMobileDeck()) return;
        e.stopPropagation();
        toggleCard(card);
      });
    }

    // Card click: clicking anywhere on an inactive card expands it
    card.addEventListener('click', function () {
      if (!isMobileDeck()) return;
      if (!card.classList.contains('is-active')) {
        toggleCard(card);
      }
    });

    // Accessibility keyboard support
    card.addEventListener('keydown', function (e) {
      if (!isMobileDeck()) return;
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        toggleCard(card);
      }
    });
  });
}

/**
 * 14. Policy Page Navigation & Scrollspy
 */
function initPolicyNav() {
  const sidebar = document.querySelector('.policy-sidebar');
  if (!sidebar) return;

  const links = sidebar.querySelectorAll('.policy-nav-link');
  const cards = document.querySelectorAll('.policy-content-area .policy-card');
  if (!links.length || !cards.length) return;

  function getStickyOffset() {
    const header = document.querySelector('.site-header');
    const headerHeight = header ? header.offsetHeight : 88;
    const adminBar = document.querySelector('#wpadminbar');
    var adminBarHeight = 0;
    if (adminBar && window.getComputedStyle(adminBar).position === 'fixed') {
      adminBarHeight = adminBar.offsetHeight;
    }
    // 28px comfortable clearance below the sticky header
    return headerHeight + adminBarHeight + 28;
  }

  function scrollToCard(targetCard, isSmooth) {
    const offset = getStickyOffset();
    const cardTop = targetCard.getBoundingClientRect().top + window.pageYOffset;
    window.scrollTo({
      top: Math.max(0, Math.round(cardTop - offset)),
      behavior: isSmooth ? 'smooth' : 'auto'
    });
  }

  var isClickScrolling = false;
  var clickTimeout = null;

  links.forEach(function (link) {
    link.addEventListener('click', function (e) {
      const targetId = this.getAttribute('href');
      if (!targetId || !targetId.startsWith('#')) return;
      const targetCard = document.querySelector(targetId);
      if (targetCard) {
        e.preventDefault();
        isClickScrolling = true;
        clearTimeout(clickTimeout);
        clickTimeout = setTimeout(function () {
          isClickScrolling = false;
        }, 900);

        links.forEach(function (l) { l.classList.remove('active'); });
        this.classList.add('active');

        scrollToCard(targetCard, true);

        if (window.history && window.history.pushState) {
          window.history.pushState(null, null, targetId);
        }
      }
    });
  });

  // Handle direct hash navigation on initial page load
  if (window.location.hash) {
    const initialTarget = document.querySelector(window.location.hash);
    if (initialTarget && initialTarget.classList.contains('policy-card')) {
      setTimeout(function () {
        scrollToCard(initialTarget, false);
      }, 150);
    }
  }

  // Scrollspy: update active sidebar link as user scrolls
  var ticking = false;
  function updateActiveNav() {
    if (isClickScrolling) return;
    const triggerLine = getStickyOffset() + 30;
    var currentActiveId = null;

    cards.forEach(function (card) {
      var rect = card.getBoundingClientRect();
      if (rect.top <= triggerLine) {
        currentActiveId = card.id;
      }
    });

    if (!currentActiveId && cards.length) {
      currentActiveId = cards[0].id;
    }

    if (currentActiveId) {
      links.forEach(function (link) {
        if (link.getAttribute('href') === '#' + currentActiveId) {
          link.classList.add('active');
        } else {
          link.classList.remove('active');
        }
      });
    }
  }

  window.addEventListener('scroll', function () {
    if (!ticking) {
      window.requestAnimationFrame(function () {
        updateActiveNav();
        ticking = false;
      });
      ticking = true;
    }
  }, { passive: true });
}
