<?php defined( 'ABSPATH' ) || exit; ?>
<section class="section order-section" id="dat-hang">
      <div class="container">
        <div class="section-header" style="margin-bottom: 28px;">
          <span class="section-tag"><?php echo project_child_fcc_text( 'fcc_ui_4e46f443e4f763', 'home' ); ?></span>
          <h2 class="section-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_25a82794037d75', 'home' ); ?></h2>
          <p class="section-desc"><?php echo project_child_fcc_text( 'fcc_ui_1d8af52afe2c00', 'home' ); ?></p>
        </div>

        <div class="order-box-wrap">
          <form id="inline-order-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            
            <input type="text" name="website_url" style="display:none !important;" tabindex="-1" autocomplete="off">

            <div class="form-grid">
              <div class="form-grid-2 form-grid">
                <div class="form-group">
                  <label class="form-label" for="inline-order-form-customer_name"><?php echo project_child_fcc_text( 'fcc_ui_37595032923f43', 'home', false ); ?> <span class="required"><?php echo project_child_fcc_text( 'fcc_ui_f2aea442e0362d', 'home', false ); ?></span></label>
                  <input type="text" name="customer_name" class="form-control" placeholder="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_e92a39efe375cb', 'shared' ) ); ?>" required id="inline-order-form-customer_name">
                </div>
                <div class="form-group">
                  <label class="form-label" for="inline-order-form-customer_phone"><?php echo project_child_fcc_text( 'fcc_ui_9d2954361bdcb7', 'home', false ); ?> <span class="required"><?php echo project_child_fcc_text( 'fcc_ui_f2aea442e0362d', 'home', false ); ?></span></label>
                  <input type="tel" name="customer_phone" class="form-control" placeholder="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_cc9b6af3ede286', 'shared' ) ); ?>" required id="inline-order-form-customer_phone">
                </div>
              </div>

              <div class="form-group">
                <label class="form-label" for="inline-product-select"><?php echo project_child_fcc_text( 'fcc_ui_5c3f8c57f0ccdd', 'home', false ); ?> <span class="required"><?php echo project_child_fcc_text( 'fcc_ui_f2aea442e0362d', 'home', false ); ?></span></label>
                  <select name="selected_item" class="form-control form-select" required id="inline-product-select"><?php project_child_fcc_order_options(); ?></select>
              </div>

              <div class="form-group">
                <label class="form-label" for="inline-order-form-customer_address"><?php echo project_child_fcc_text( 'fcc_ui_f7fb405b36f89a', 'home', false ); ?></label>
                  <input type="text" name="customer_address" class="form-control" placeholder="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_44ef8652c97ae4', 'shared' ) ); ?>" id="inline-order-form-customer_address">
              </div>

              <div class="form-group">
                <label class="form-label" for="inline-order-form-customer_note"><?php echo project_child_fcc_text( 'fcc_ui_ec79fb64aa5901', 'home', false ); ?></label>
                  <textarea name="customer_note" class="form-control" placeholder="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_4da5e0bc6baf4a', 'shared' ) ); ?>" id="inline-order-form-customer_note"></textarea>
              </div>

              <div class="form-checkbox-row">
                <input type="checkbox" name="agree_terms" id="inline-agree" required>
                <label for="inline-agree">
                  <?php echo project_child_fcc_text( 'fcc_ui_1194cfb117972b', 'home', false ); ?> <a href="<?php echo esc_url( project_child_fcc_link( 'chinh-sach.html' ) ); ?>" target="_blank" style="text-decoration: underline; color: var(--color-primary);"><?php echo project_child_fcc_text( 'fcc_ui_b972bd8a95f745', 'home', false ); ?></a><?php echo project_child_fcc_text( 'fcc_ui_1e11cbdee57e18', 'home', false ); ?>
                </label>
              </div>

              <button type="submit" class="btn-submit-order">
                <?php echo project_child_fcc_text( 'fcc_ui_7bf6d434d49b89', 'home', false ); ?>
              </button>

              
              <div class="quick-contacts-row">
                <a href="<?php echo esc_url( project_child_fcc_link( 'https://zalo.me/0985561862' ) ); ?>" target="_blank" rel="noopener noreferrer" class="contact-pill-btn contact-pill-zalo">
                  <?php echo project_child_fcc_text( 'fcc_ui_ae4554170a6187', 'home', false ); ?>
                </a>
                
                <a href="<?php echo esc_url( project_child_fcc_link( 'tel:0985561862' ) ); ?>" class="contact-pill-btn contact-pill-phone">
                  <?php echo project_child_fcc_text( 'fcc_ui_e3aaf9923353d9', 'home', false ); ?>
                </a>
              </div>

              <div class="form-secure-note" style="margin-top: 18px;">
                <span><?php echo project_child_fcc_text( 'fcc_ui_07bd799199d8cf', 'home' ); ?></span> • <span><?php echo project_child_fcc_text( 'fcc_ui_0c54117f7f63bc', 'home' ); ?></span> • <span><?php echo project_child_fcc_text( 'fcc_ui_29d9729f6162a0', 'home' ); ?></span>
              </div>
            </div>
          <?php project_child_fcc_form_fields(); ?></form>
        </div>
      </div>
    </section>
