jQuery(function ($) {
  const editor = document.getElementById('fcc-product-media-editor');
  if (!editor) return;
  const list = editor.querySelector('.fcc-media-list');
  const orderInput = editor.querySelector('[name="fcc_product_gallery"]');
  const primaryInput = editor.querySelector('[name="fcc_product_primary"]');
  const hoverInput = editor.querySelector('[name="fcc_product_hover"]');
  const status = editor.querySelector('.fcc-media-status');
  let images = JSON.parse(editor.querySelector('.fcc-media-data').textContent);

  function button(text, action, className = 'button') {
    const node = document.createElement('button');
    node.type = 'button'; node.className = className; node.textContent = text;
    node.addEventListener('click', action); return node;
  }

  function sync() {
    orderInput.value = images.map(image => image.id).join(',');
    const primaryId = Number(primaryInput.value);
    const hoverId = hoverInput ? Number(hoverInput.value) : 0;
    const parts = [images.length + ' ảnh.'];
    parts.push(primaryId ? 'Đã chọn ảnh đại diện.' : 'Chưa chọn ảnh đại diện.');
    if (hoverId) {
      parts.push('Đã đánh dấu ảnh khi rê chuột (Hover card).');
    }
    status.textContent = parts.join(' ');
  }

  function render() {
    list.replaceChildren();
    const primaryId = Number(primaryInput.value);
    const hoverId = hoverInput ? Number(hoverInput.value) : 0;

    images.forEach((image, index) => {
      const item = document.createElement('li'); item.dataset.id = image.id;
      const isPrimary = primaryId === image.id;
      const isHover = hoverId === image.id;
      if (isPrimary) item.classList.add('is-primary');
      if (isHover) item.classList.add('is-hover');

      const previewWrap = document.createElement('div');
      previewWrap.className = 'fcc-media-preview-wrap';
      const preview = document.createElement('img'); preview.src = image.url; preview.alt = image.title || 'Ảnh sản phẩm';
      previewWrap.appendChild(preview);

      if (isPrimary) {
        const primaryBadge = document.createElement('span');
        primaryBadge.className = 'fcc-media-badge fcc-media-badge-primary';
        primaryBadge.textContent = 'Ảnh đại diện';
        previewWrap.appendChild(primaryBadge);
      }
      if (isHover) {
        const hoverBadge = document.createElement('span');
        hoverBadge.className = 'fcc-media-badge fcc-media-badge-hover';
        hoverBadge.textContent = 'Ảnh khi rê chuột (Hover card)';
        previewWrap.appendChild(hoverBadge);
      }

      const title = document.createElement('span'); title.className = 'fcc-media-title'; title.textContent = (index + 1) + '. ' + preview.alt;

      const actions = document.createElement('div');
      actions.className = 'fcc-media-actions';

      const primaryBtn = button(isPrimary ? '✓ Đại diện' : 'Đặt đại diện', () => {
        primaryInput.value = image.id;
        render();
      }, isPrimary ? 'button button-primary fcc-btn-primary is-active' : 'button fcc-btn-primary');
      primaryBtn.setAttribute('aria-pressed', String(isPrimary));

      const hoverBtn = button(isHover ? '✓ Ảnh hover (Hover card)' : 'Đặt ảnh hover', () => {
        if (hoverInput) {
          hoverInput.value = isHover ? 0 : image.id;
          render();
        }
      }, isHover ? 'button fcc-btn-hover is-active' : 'button fcc-btn-hover');
      hoverBtn.setAttribute('title', isHover ? 'Nhấn để bỏ chọn ảnh khi rê chuột' : 'Đánh dấu làm Ảnh khi rê chuột (Hover card)');
      hoverBtn.setAttribute('aria-pressed', String(isHover));

      const navRow = document.createElement('div');
      navRow.className = 'fcc-media-nav-row';

      const move = delta => {
        const next = index + delta;
        if (next < 0 || next >= images.length) return;
        [images[index], images[next]] = [images[next], images[index]];
        render();
        list.children[next].querySelector('button:not(:disabled)').focus();
      };
      const up = button('↑', () => move(-1)); up.classList.add('fcc-media-up'); up.disabled = index === 0; up.setAttribute('aria-label', 'Đưa ảnh lên trước');
      const down = button('↓', () => move(1)); down.disabled = index === images.length - 1; down.setAttribute('aria-label', 'Đưa ảnh xuống sau');
      const remove = button('Bỏ ảnh', () => {
        images = images.filter(entry => entry.id !== image.id);
        if (primaryId === image.id) primaryInput.value = images[0]?.id || 0;
        if (hoverId === image.id && hoverInput) hoverInput.value = 0;
        render();
      });
      remove.classList.add('fcc-media-remove');

      navRow.append(up, down, remove);
      actions.append(primaryBtn, hoverBtn, navRow);
      item.append(previewWrap, title, actions);
      list.appendChild(item);
    });
    sync();
  }

  $(list).sortable({ items: '> li', cancel: 'button, .fcc-media-badge', tolerance: 'pointer', update() {
    const byId = new Map(images.map(image => [image.id, image]));
    images = Array.from(list.children).map(item => byId.get(Number(item.dataset.id))); render();
  }});

  editor.querySelector('.fcc-media-add').addEventListener('click', () => {
    const frame = wp.media({ title: 'Chọn ảnh sản phẩm', button: { text: 'Thêm vào danh sách' }, library: { type: 'image' }, multiple: true });
    frame.on('select', () => {
      frame.state().get('selection').toJSON().forEach(image => {
        if (!images.some(entry => entry.id === image.id)) images.push({ id: image.id, url: image.sizes?.thumbnail?.url || image.url, title: image.title || image.filename });
      });
      if (!Number(primaryInput.value) && images.length) primaryInput.value = images[0].id;
      render();
    });
    frame.open();
  });
  render();
});
