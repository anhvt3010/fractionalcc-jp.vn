jQuery(function ($) {
  const editor = document.getElementById('fcc-product-media-editor');
  if (!editor) return;
  const list = editor.querySelector('.fcc-media-list');
  const orderInput = editor.querySelector('[name="fcc_product_gallery"]');
  const primaryInput = editor.querySelector('[name="fcc_product_primary"]');
  const status = editor.querySelector('.fcc-media-status');
  let images = JSON.parse(editor.querySelector('.fcc-media-data').textContent);
  function button(text, action) {
    const node = document.createElement('button');
    node.type = 'button'; node.className = 'button'; node.textContent = text;
    node.addEventListener('click', action); return node;
  }
  function sync() {
    orderInput.value = images.map(image => image.id).join(',');
    status.textContent = images.length + ' ảnh. ' + (Number(primaryInput.value) ? 'Đã chọn ảnh đại diện.' : 'Chưa chọn ảnh đại diện.');
  }
  function render() {
    list.replaceChildren();
    images.forEach((image, index) => {
      const item = document.createElement('li'); item.dataset.id = image.id;
      const preview = document.createElement('img'); preview.src = image.url; preview.alt = image.title || 'Ảnh sản phẩm';
      const title = document.createElement('span'); title.className = 'fcc-media-title'; title.textContent = (index + 1) + '. ' + preview.alt;
      const primary = button(Number(primaryInput.value) === image.id ? '✓ Đại diện' : 'Đặt đại diện', () => { primaryInput.value = image.id; render(); });
      primary.setAttribute('aria-pressed', String(Number(primaryInput.value) === image.id));
      const move = delta => { const next = index + delta; if (next < 0 || next >= images.length) return; [images[index], images[next]] = [images[next], images[index]]; render(); list.children[next].querySelector('button:not(:disabled)').focus(); };
      const up = button('↑', () => move(-1)); up.classList.add('fcc-media-up'); up.disabled = index === 0; up.setAttribute('aria-label', 'Đưa ảnh lên trước');
      const down = button('↓', () => move(1)); down.disabled = index === images.length - 1; down.setAttribute('aria-label', 'Đưa ảnh xuống sau');
      const remove = button('Bỏ ảnh', () => { images = images.filter(entry => entry.id !== image.id); if (Number(primaryInput.value) === image.id) primaryInput.value = images[0]?.id || 0; render(); });
      item.append(preview, title, primary, up, down, remove); list.appendChild(item);
    });
    sync();
  }
  $(list).sortable({ items: '> li', cancel: 'button', tolerance: 'pointer', update() {
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
