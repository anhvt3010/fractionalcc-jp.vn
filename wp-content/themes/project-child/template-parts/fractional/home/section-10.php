<?php defined( 'ABSPATH' ) || exit; ?>
<section class="section order-section" id="dat-hang">
      <div id="pd-order-section" style="position:relative;top:-70px;visibility:hidden;"></div>
      <div class="container">
        <div class="section-header" style="margin-bottom: 28px;">
          <span class="section-tag"><?php echo project_child_fcc_text( 'fcc_ui_4e46f443e4f763', 'home' ); ?></span>
          <h2 class="section-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_25a82794037d75', 'home' ); ?></h2>
          <p class="section-desc"><?php echo project_child_fcc_text( 'fcc_ui_1d8af52afe2c00', 'home' ); ?></p>
        </div>

        <div class="order-box-wrap">
          <div class="order-layout-grid">
            <div class="order-form-column">
              <form id="inline-order-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">

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
                </div>
                <?php project_child_fcc_form_fields( is_singular( 'fcc_product' ) ? 'product_inline' : 'home_inline' ); ?>
              </form>
            </div>

            <div class="order-sidebar-column">
              <div class="order-sidebar-card order-contact-card">
                <h3 class="order-sidebar-card-title">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                  <span><?php echo project_child_fcc_text( 'fcc_ui_m_4f498b8722ef', 'shared' ); ?></span>
                </h3>

                <div class="order-contact-list">
                  <div class="order-contact-item">
                    <span class="order-contact-item-icon">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                    </span>
                    <div>
                      <strong><?php echo project_child_fcc_text( 'fcc_ui_m_92a089a749c6', 'shared' ); ?></strong>
                      <a href="<?php echo esc_url( project_child_fcc_link( 'tel:0985561862' ) ); ?>"><?php echo esc_html( project_core_fcc_site( 'hotlineDisplay', '0985 561 862' ) ); ?></a>
                    </div>
                  </div>

                  <div class="order-contact-item">
                    <span class="order-contact-item-icon">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    </span>
                    <div>
                      <strong><?php echo project_child_fcc_text( 'fcc_ui_m_d3dbd43eb42f', 'shared' ); ?></strong>
                      <a href="<?php echo esc_url( project_child_fcc_link( 'mailto:jpbuidangco.ltd@gmail.com' ) ); ?>"><?php echo esc_html( project_core_fcc_site( 'email', 'jpbuidangco.ltd@gmail.com' ) ); ?></a>
                    </div>
                  </div>
                </div>

                <div class="order-contact-btns">
                  <a href="<?php echo esc_url( project_child_fcc_link( 'https://zalo.me/0985561862' ) ); ?>" target="_blank" rel="noopener noreferrer" class="contact-pill-btn contact-pill-zalo">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2C6.477 2 2 6.03 2 11C2 13.565 3.195 15.864 5.12 17.472L4.2 21.2C4.085 21.664 4.545 22.052 4.965 21.848L9.29 19.742C10.158 19.911 11.063 20 12 20C17.523 20 22 15.97 22 11C22 6.03 17.523 2 12 2Z" fill="#FFFFFF"></path><text x="12" y="14" font-family="'Plus Jakarta Sans', Arial, sans-serif" font-size="7.2" font-weight="800" fill="#0068FF" text-anchor="middle" letter-spacing="-0.3">Zalo</text></svg>
                    <span><?php echo project_child_fcc_text( 'fcc_ui_m_db25d68f6300', 'shared' ); ?></span>
                  </a>

                  <a href="<?php echo esc_url( project_child_fcc_link( 'https://m.me/buidangjp' ) ); ?>" target="_blank" rel="noopener noreferrer" class="contact-pill-btn contact-pill-messenger">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.36 2 2 6.13 2 11.7c0 2.91 1.19 5.44 3.12 7.15.16.14.26.35.26.57l-.37 2.21c-.08.47.38.86.82.7l2.58-1.02c.18-.07.38-.07.56-.01.95.26 1.97.4 3.03.4 5.64 0 10-4.13 10-9.7S17.64 2 12 2zm1 10.5-2.5-2.5-3.5 3.5 3-4.5 2.5 2.5 3.5-3.5-3 4.5z"/></svg>
                    <span><?php echo project_child_fcc_text( 'fcc_ui_m_5c77cd896de7', 'shared' ); ?></span>
                  </a>

                  <a href="<?php echo esc_url( project_child_fcc_link( 'tel:0985561862' ) ); ?>" class="contact-pill-btn contact-pill-phone">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                    <span><?php echo project_child_fcc_text( 'fcc_ui_m_f412d16b3a3e', 'shared' ); ?></span>
                  </a>
                </div>
              </div>

              <div class="order-sidebar-card order-policy-card">
                <h3 class="order-sidebar-card-title">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                  <span><?php echo project_child_fcc_text( 'fcc_ui_m_a4ad94c43d5b', 'shared' ); ?></span>
                </h3>

                <ul class="order-policy-list">
                  <li>
                    <svg class="order-policy-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                    <div><strong><?php echo project_child_fcc_text( 'fcc_ui_m_df7e31188f91', 'shared' ); ?></strong> <?php echo project_child_fcc_text( 'fcc_ui_m_68273e5a84bb', 'shared' ); ?></div>
                  </li>
                  <li>
                    <svg class="order-policy-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                    <div><strong><?php echo project_child_fcc_text( 'fcc_ui_m_fb7037579c0c', 'shared' ); ?></strong> <?php echo project_child_fcc_text( 'fcc_ui_m_a2a451396944', 'shared' ); ?></div>
                  </li>
                  <li>
                    <svg class="order-policy-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"></rect><line x1="2" x2="22" y1="10" y2="10"></line></svg>
                    <div><strong><?php echo project_child_fcc_text( 'fcc_ui_m_89b96c48a7e5', 'shared' ); ?></strong> <?php echo project_child_fcc_text( 'fcc_ui_m_2781e65c6bfe', 'shared' ); ?></div>
                  </li>
                  <li>
                    <svg class="order-policy-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                    <div><strong><?php echo project_child_fcc_text( 'fcc_ui_m_63d5110e4475', 'shared' ); ?></strong> <?php echo project_child_fcc_text( 'fcc_ui_m_c2a91961cf47', 'shared' ); ?></div>
                  </li>
                </ul>

                <div class="order-policy-badge">
                  <span><?php echo project_child_fcc_text( 'fcc_ui_m_65d268dfc2a2', 'shared' ); ?></span> • <span><?php echo project_child_fcc_text( 'fcc_ui_m_f0739114e699', 'shared' ); ?></span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
