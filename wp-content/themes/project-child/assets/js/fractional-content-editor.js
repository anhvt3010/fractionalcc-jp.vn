/* Find existing ACF fields by location, label or current text; retain native saving. */
(function () {
  'use strict';
  function init() {
    const panel = document.getElementById('fcc-content-finder');
    if (!panel) return;
    const input = document.getElementById('fcc-field-search');
    const results = document.getElementById('fcc-field-results');
    const status = document.getElementById('fcc-field-search-status');
    const normalize = value => value.toLocaleLowerCase('vi').normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/đ/g, 'd');
    const entries = [];
    document.querySelectorAll('#acf-group_fcc_site, #acf-group_fcc_ui_shared').forEach(group => {
      const container = group.querySelector('.acf-fields');
      if (!container) return;
      let tab = null;
      Array.from(container.children).forEach(field => {
        if (field.dataset.type === 'tab') {
          tab = { key: field.dataset.key, label: field.querySelector('.acf-tab-button')?.textContent.trim() || '' };
          return;
        }
        if (!field.dataset.name || field.dataset.type === 'message') return;
        const label = field.querySelector('.acf-label > label')?.textContent.trim();
        if (!label) return;
        entries.push({ field, group, tab, label, archived: /luu tru|tham chieu/.test(normalize(tab?.label || '')), text: normalize(`${tab?.label || ''} ${label} ${field.querySelector('.description')?.textContent || ''}`) });
      });
    });
    function open(entry) {
      if (entry.group.classList.contains('closed')) entry.group.querySelector('.handlediv')?.click();
      if (entry.tab) entry.group.querySelector(`.acf-tab-wrap .acf-tab-button[data-key="${entry.tab.key}"]`)?.click();
      document.querySelectorAll('.fcc-field-found').forEach(field => field.classList.remove('fcc-field-found'));
      entry.field.classList.add('fcc-field-found');
      entry.field.scrollIntoView({ block: 'center', behavior: 'smooth' });
      const control = Array.from(entry.field.querySelectorAll('input:not([type="hidden"]), textarea, select, button, a')).find(node => !node.disabled && node.offsetParent !== null);
      control?.focus({ preventScroll: true });
    }
    input.addEventListener('input', () => {
      const terms = normalize(input.value.trim()).split(/\s+/).filter(Boolean);
      results.replaceChildren();
      results.hidden = !terms.length;
      if (!terms.length) { status.textContent = 'Kết quả sẽ ghi rõ nhóm và tên trường. Chọn kết quả để mở đúng tab.'; return; }
      const matches = entries.filter(entry => {
        const values = Array.from(entry.field.querySelectorAll('input:not([type="hidden"]), textarea, select')).map(node => node.tagName === 'SELECT' ? Array.from(node.selectedOptions).map(option => option.textContent).join(' ') : node.value).join(' ');
        const text = `${entry.text} ${normalize(values)}`;
        return terms.every(term => text.includes(term));
      }).sort((left, right) => Number(left.archived) - Number(right.archived));
      matches.slice(0, 20).forEach(entry => {
        const item = document.createElement('li');
        const button = document.createElement('button');
        button.type = 'button';
        button.textContent = `${entry.tab?.label || entry.group.querySelector('h2')?.textContent || ''} › ${entry.label}`;
        button.addEventListener('click', () => open(entry));
        item.append(button);
        results.append(item);
      });
      status.textContent = matches.length ? `Tìm thấy ${matches.length} trường.${matches.length > 20 ? ' Đang hiển thị 20 kết quả; nhập thêm từ để thu hẹp.' : ' Chọn kết quả để mở đúng tab.'}` : 'Không tìm thấy. Thử tên khu vực, ví dụ: menu, đánh giá, hồ sơ pháp lý.';
    });
    input.addEventListener('keydown', event => {
      if (event.key === 'Enter') { event.preventDefault(); results.querySelector('button')?.focus(); }
      if (event.key === 'Escape') { input.value = ''; input.dispatchEvent(new Event('input')); }
    });
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})();
