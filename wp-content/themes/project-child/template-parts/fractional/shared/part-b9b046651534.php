<?php defined( 'ABSPATH' ) || exit; ?>
<div class="modal-overlay" id="order-modal">
    <div class="modal-container">
      <button class="modal-close-btn" id="modal-close-btn" aria-label="Đóng">
        <svg width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
      <div style="text-align: center; margin-bottom: 24px; padding: 0 36px;">
        <span class="badge-pill badge-gold" style="margin-bottom: 6px;"><?php echo project_child_fcc_text( 'fcc_ui_f2b083aef82352', 'shared' ); ?></span>
        <h3 style="font-size: 24px; color: var(--color-text-main); margin-bottom: 6px;"><?php echo project_child_fcc_text( 'fcc_ui_e97d9d953b50ea', 'shared' ); ?></h3>
        <p style="font-size: 13.5px; color: var(--color-text-muted);"><?php echo project_child_fcc_text( 'fcc_ui_dfc1bf59d438e2', 'shared' ); ?></p>
      </div>
      <form id="modal-order-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <input type="text" name="website_url" style="display:none !important;" tabindex="-1" autocomplete="off">
        <div class="form-grid">
          <div class="form-group">
            <label class="form-label" for="modal-order-form-customer_name"><?php echo project_child_fcc_text( 'fcc_ui_1496dc7df7ac7f', 'shared', false ); ?> <span class="required"><?php echo project_child_fcc_text( 'fcc_ui_b69c738f215c43', 'shared', false ); ?></span></label>
                  <input type="text" name="customer_name" class="form-control" placeholder="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_484f408cddfc35', 'shared' ) ); ?>" required id="modal-order-form-customer_name">
          </div>
          <div class="form-group">
            <label class="form-label" for="modal-order-form-customer_phone"><?php echo project_child_fcc_text( 'fcc_ui_f2710d708cc9d4', 'shared', false ); ?> <span class="required"><?php echo project_child_fcc_text( 'fcc_ui_b69c738f215c43', 'shared', false ); ?></span></label>
                  <input type="tel" name="customer_phone" class="form-control" placeholder="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_e023e68eba385b', 'shared' ) ); ?>" required id="modal-order-form-customer_phone">
          </div>
          <div class="form-group">
            <label class="form-label" for="modal-product-select"><?php echo project_child_fcc_text( 'fcc_ui_235bacf2e47e24', 'shared', false ); ?> <span class="required"><?php echo project_child_fcc_text( 'fcc_ui_b69c738f215c43', 'shared', false ); ?></span></label>
                  <select name="selected_item" class="form-control form-select" required id="modal-product-select"><?php project_child_fcc_order_options(); ?></select>
          </div>
          <div class="form-group">
            <label class="form-label" for="modal-order-form-customer_address"><?php echo project_child_fcc_text( 'fcc_ui_f9e51ea87d8c27', 'shared', false ); ?></label>
                  <input type="text" name="customer_address" class="form-control" placeholder="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_3dedbf56c53eec', 'shared' ) ); ?>" id="modal-order-form-customer_address">
          </div>
          <div class="form-group">
            <label class="form-label" for="modal-order-form-customer_note"><?php echo project_child_fcc_text( 'fcc_ui_2ea28da43d47d0', 'shared', false ); ?></label>
                  <textarea name="customer_note" class="form-control" style="min-height: 60px;" placeholder="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_8ab23cd60b12f1', 'shared' ) ); ?>" id="modal-order-form-customer_note"></textarea>
          </div>
          <div class="form-checkbox-row">
            <input type="checkbox" name="agree_terms" id="modal-agree" required>
            <label for="modal-agree"><?php echo project_child_fcc_text( 'fcc_ui_2eb27fa50153ca', 'shared', false ); ?></label>
          </div>
          <button type="submit" class="btn-submit-order"><?php echo project_child_fcc_text( 'fcc_ui_9681bed187e219', 'shared', false ); ?></button>
        </div>
      <?php project_child_fcc_form_fields(); ?></form>
    </div>
  </div>
