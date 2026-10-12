/* Extend the native editor; its AJAX, row replacement and cancel behavior remain native. */
(function () {
  'use strict';
  if (!window.inlineEditPost) return;
  const editor = window.inlineEditPost;
  const originalEdit = editor.edit;
  const originalSave = editor.save;

  editor.edit = function (id) {
    const result = originalEdit.apply(this, arguments);
    const postId = typeof id === 'object' ? this.getId(id) : id;
    const row = document.getElementById('post-' + postId);
    const form = document.getElementById('edit-' + postId);
    const fields = form?.querySelector('.fcc-quick-edit-fields');
    if (!fields) return result;
    fields.disabled = true;
    const error = fields.querySelector('.fcc-quick-edit-error');
    error.hidden = true;
    try {
      const payload = row?.querySelector('.fcc-product-quick-data');
      if (!payload) throw new Error('Missing product values');
      const values = JSON.parse(payload.dataset.fields);
      fields.querySelectorAll('[data-fcc-field]').forEach(input => {
        const name = input.dataset.fccField;
        if (!Object.prototype.hasOwnProperty.call(values, name)) throw new Error('Incomplete product values');
        input.value = values[name];
      });
      fields.disabled = false;
    } catch {
      error.textContent = 'Không tải được thông tin sản phẩm. Hãy tải lại danh sách hoặc mở màn Sửa để cập nhật.';
      error.hidden = false;
    }
    return result;
  };

  editor.save = function (id) {
    const postId = typeof id === 'object' ? this.getId(id) : id;
    const fields = document.getElementById('edit-' + postId)?.querySelector('.fcc-quick-edit-fields');
    if (fields && !fields.disabled) {
      for (const input of fields.querySelectorAll('[data-fcc-field]')) {
        if (!input.checkValidity()) {
          input.reportValidity();
          return false;
        }
      }
    }
    return originalSave.apply(this, arguments);
  };
})();
