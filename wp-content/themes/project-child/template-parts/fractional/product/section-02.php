<?php defined( 'ABSPATH' ) || exit; ?>
<section class="section" style="padding-top: 0; padding-bottom: 40px;">
      <div class="container">
        <div class="pd-hero-grid">
          
          <div class="pd-showcase-left">
            <div class="pd-gallery-stage">
              
              <div class="pd-hero-callout">
                <span class="pd-callout-bar"></span>
                <div class="pd-callout-text">
                  <strong id="pd-callout-title"><?php echo esc_html( project_child_fcc_product_value( 'callout_title' ) ); ?></strong>
                  <span id="pd-callout-sub"><?php echo esc_html( project_child_fcc_product_value( 'callout_subtitle' ) ); ?></span>
                </div>
              </div>

              <img id="pd-main-image" class="pd-main-img" src="<?php echo esc_url( project_child_fcc_product_value( 'image' ) ); ?>" alt="<?php echo esc_attr( project_child_fcc_product_value( 'name' ) ); ?>">
            </div>

            
            <div class="pd-thumbs-row" id="pd-gallery-thumbs"><?php project_child_fcc_fragment( 'gallery' ); ?></div>
          </div>

          
          <div class="pd-showcase-right">
            <div class="pd-category-crumb">
              <span id="pd-category-badge"><?php echo esc_html( project_child_fcc_product_value( 'category' ) ); ?></span> • <span id="pd-origin-badge"><?php echo esc_html( project_child_fcc_product_value( 'origin' ) ); ?></span>
            </div>

            <h1 class="pd-cocoon-title heading-serif" id="pd-title"><?php echo esc_html( project_child_fcc_product_value( 'name' ) ); ?></h1>

            
            <div class="pd-cocoon-price-row">
              <span class="pd-cocoon-price" id="pd-sale-price"><?php echo esc_html( project_child_fcc_product_value( 'price' ) ); ?></span>
              <span class="pd-cocoon-old-price" id="pd-original-price"><?php echo esc_html( project_child_fcc_product_value( 'original' ) ); ?></span>
              <span class="pd-cocoon-save-pill" id="pd-save-amount"><?php echo esc_html( project_child_fcc_product_value( 'saving' ) ); ?></span>
            </div>

            <div class="pd-cocoon-rating">
              <span class="pd-stars">★★★★★</span>
              <span><?php echo project_child_fcc_text( 'fcc_ui_2bd71effeb0585', 'shared' ); ?></span>
            </div>

            
            <p class="pd-cocoon-desc" id="pd-tagline"><?php echo esc_html( project_child_fcc_product_value( 'tagline' ) ); ?></p>

            
            <div class="pd-cocoon-claims">
              <div class="pd-claim-item">
                <svg width="15" height="15" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                <span><?php echo project_child_fcc_text( 'fcc_ui_476e0450734143', 'shared' ); ?></span>
              </div>
              <span class="pd-claim-sep">•</span>
              <div class="pd-claim-item">
                <svg width="15" height="15" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><path d="m4.93 4.93 4.24 4.24"></path><path d="m14.83 9.17 4.24-4.24"></path><path d="m14.83 14.83 4.24 4.24"></path><path d="m9.17 14.83-4.24 4.24"></path></svg>
                <span><?php echo project_child_fcc_text( 'fcc_ui_bcf7198870556e', 'shared' ); ?></span>
              </div>
              <span class="pd-claim-sep">•</span>
              <div class="pd-claim-item">
                <svg width="15" height="15" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 6L9 17l-5-5"></path></svg>
                <span><?php echo project_child_fcc_text( 'fcc_ui_a4761e5516401e', 'shared' ); ?></span>
              </div>
              <span class="pd-claim-sep">•</span>
              <div class="pd-claim-item">
                <svg width="15" height="15" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="20" height="14" x="2" y="5" rx="2"></rect><line x1="2" x2="22" y1="10" y2="10"></line></svg>
                <span><?php echo project_child_fcc_text( 'fcc_ui_3a43752a881468', 'shared' ); ?></span>
              </div>
            </div>

            
            <div class="pd-cocoon-section">
              <h3 class="pd-cocoon-heading"><?php echo project_child_fcc_text( 'fcc_ui_2cacb01c3941c0', 'shared' ); ?></h3>
              <p class="pd-cocoon-text" id="pd-target-summary"><?php echo esc_html( project_child_fcc_product_value( 'target_summary' ) ); ?></p>
            </div>

            
            <div class="pd-cocoon-section">
              <h3 class="pd-cocoon-heading"><?php echo project_child_fcc_text( 'fcc_ui_b2f2c633e021b5', 'shared' ); ?></h3>
              <p class="pd-cocoon-text" id="pd-actives-summary"><?php echo esc_html( project_child_fcc_product_value( 'active_summary' ) ); ?></p>
            </div>

            
            <div class="pd-cocoon-meta-line">
              <span><strong><?php echo project_child_fcc_text( 'fcc_ui_7c2ab4b4eda5ba', 'shared' ); ?></strong> <span id="pd-volume"><?php echo esc_html( project_child_fcc_product_value( 'volume' ) ); ?></span></span>
              <span>•</span>
              <span><strong><?php echo project_child_fcc_text( 'fcc_ui_4e3e203cfd2278', 'shared' ); ?></strong> <span id="pd-spec-origin"><?php echo esc_html( project_child_fcc_product_value( 'origin' ) ); ?></span></span>
              <span>•</span>
              <span><strong><?php echo project_child_fcc_text( 'fcc_ui_09caa8fe5fdc81', 'shared' ); ?></strong> <span id="pd-spec-cbmp" class="text-gold"><?php echo esc_html( project_child_fcc_product_value( 'cbmp' ) ); ?></span></span>
            </div>

            
            <div class="pd-cocoon-cert-row">
              <div class="pd-cert-badge">
                <svg width="20" height="20" viewbox="0 0 24 16" fill="none" style="border-radius:2px;box-shadow:0 0 2px rgba(0,0,0,0.3);"><rect width="24" height="16" fill="#FFFFFF"></rect><circle cx="12" cy="8" r="4.8" fill="#BC002D"></circle></svg>
                <span><?php echo project_child_fcc_text( 'fcc_ui_07d4db9057c1e0', 'shared' ); ?></span>
              </div>
              <div class="pd-cert-badge">
                <svg width="20" height="20" viewbox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="m9 12 2 2 4-4"></path></svg>
                <span><?php echo project_child_fcc_text( 'fcc_ui_d1f450c933841c', 'shared' ); ?></span>
              </div>
              <div class="pd-cert-badge">
                <svg width="20" height="20" viewbox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2"><path d="M20 6L9 17l-5-5"></path></svg>
                <span><?php echo project_child_fcc_text( 'fcc_ui_f1cf9cbf3a1a05', 'shared' ); ?></span>
              </div>
            </div>

            
            <button class="btn-pd-cocoon-buy" data-open-order id="pd-buy-now-btn">
              <span id="pd-buy-btn-label"><?php echo esc_html( project_child_fcc_product_value( 'buy' ) ); ?></span>
              <svg width="20" height="20" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
            </button>

            
            <div class="pd-cocoon-sub-bar">
              <a href="<?php echo esc_url( project_child_fcc_link( 'https://zalo.me/0985561862' ) ); ?>" target="_blank" rel="noopener noreferrer" class="pd-cocoon-sub-link">
                <svg width="15" height="15" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                <span><?php echo project_child_fcc_text( 'fcc_ui_20748aba1fa4a2', 'shared', false ); ?></span>
              </a>
              <span>•</span>
              <a href="<?php echo esc_url( project_child_fcc_link( 'tel:0985561862' ) ); ?>" class="pd-cocoon-sub-link">
                <svg width="15" height="15" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                <span><?php echo project_child_fcc_text( 'fcc_ui_d0f30a260b8018', 'shared', false ); ?></span>
              </a>
              <span>•</span>
              <span><?php echo project_child_fcc_text( 'fcc_ui_82c0e470cd9121', 'shared' ); ?></span>
            </div>
          </div>
        </div>

        
        <div class="pd-usp-section" id="pd-dac-diem-noi-bat">
          <div class="section-header">
            <span class="section-tag"><?php echo project_child_fcc_text( 'fcc_ui_8594d5c948e8fc', 'shared' ); ?></span>
            <h2 class="section-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_cade7d2a34862c', 'shared' ); ?></h2>
            <p class="section-desc"><?php echo project_child_fcc_text( 'fcc_ui_5d5f80440bfe3c', 'shared' ); ?></p>
          </div>
          <div class="pd-usp-wrap">
            <ul class="pd-usp-list" id="pd-usp-list"><?php project_child_fcc_fragment( 'usp' ); ?></ul>
          </div>
        </div>

        
        <div class="pd-dossier-section">
          <div class="pd-dossier-header">
            <div>
              <div class="pd-dossier-tag">
                <svg width="12" height="12" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                <?php echo project_child_fcc_text( 'fcc_ui_00dbdd876ed3f1', 'shared' ); ?>
              </div>
              <h2 class="pd-dossier-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_0521464c014938', 'shared' ); ?></h2>
              <p class="pd-dossier-desc">
                <?php echo project_child_fcc_text( 'fcc_ui_3c17f4f2108157', 'shared' ); ?>
              </p>
            </div>
            <div class="pd-dossier-seal-badge">
              <svg width="20" height="20" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="m9 12 2 2 4-4"></path></svg>
              <span><?php echo project_child_fcc_text( 'fcc_ui_c6521a32293b35', 'shared' ); ?></span>
            </div>
          </div>

          <div class="pd-dossier-grid">
            <div class="pd-dossier-item">
              <span class="pd-dossier-item-label"><?php echo project_child_fcc_text( 'fcc_ui_1117e9caf2f9c5', 'shared' ); ?></span>
              <span class="pd-dossier-item-value" id="pd-dossier-name"><?php echo esc_html( project_child_fcc_product_value( 'name' ) ); ?></span>
              <span class="pd-dossier-item-sub"><?php echo project_child_fcc_text( 'fcc_ui_d030a6d0c4b852', 'shared' ); ?></span>
            </div>
            <div class="pd-dossier-item">
              <span class="pd-dossier-item-label"><?php echo project_child_fcc_text( 'fcc_ui_672c75317fb0c1', 'shared' ); ?></span>
              <span class="pd-dossier-item-value" id="pd-dossier-origin"><?php echo esc_html( project_child_fcc_product_value( 'origin' ) ); ?></span>
              <span class="pd-dossier-item-sub"><?php echo project_child_fcc_text( 'fcc_ui_e8e91f7268d663', 'shared' ); ?></span>
            </div>
            <div class="pd-dossier-item">
              <span class="pd-dossier-item-label"><?php echo project_child_fcc_text( 'fcc_ui_62e8c9b9513489', 'shared' ); ?></span>
              <span class="pd-dossier-item-value text-gold" id="pd-dossier-cbmp"><?php echo esc_html( project_child_fcc_product_value( 'cbmp' ) ); ?></span>
              <span class="pd-dossier-item-sub"><?php echo project_child_fcc_text( 'fcc_ui_e4e9ad9ac34848', 'shared' ); ?></span>
            </div>
            <div class="pd-dossier-item">
              <span class="pd-dossier-item-label"><?php echo project_child_fcc_text( 'fcc_ui_c9174fa4d7a18c', 'shared' ); ?></span>
              <span class="pd-dossier-item-value" id="pd-dossier-status" style="color: #4ADE80;"><?php echo esc_html( project_child_fcc_product_value( 'legalStatus' ) ); ?></span>
              <span class="pd-dossier-item-sub"><?php echo project_child_fcc_text( 'fcc_ui_edb76d7790c3d5', 'shared' ); ?></span>
            </div>
            <div class="pd-dossier-item">
              <span class="pd-dossier-item-label"><?php echo project_child_fcc_text( 'fcc_ui_7b7743af09d591', 'shared' ); ?></span>
              <span class="pd-dossier-item-value"><?php echo project_child_fcc_text( 'fcc_ui_2812b49f68fc1b', 'shared' ); ?></span>
              <span class="pd-dossier-item-sub"><?php echo project_child_fcc_text( 'fcc_ui_81827da6a441bb', 'shared' ); ?></span>
            </div>
            <div class="pd-dossier-item">
              <span class="pd-dossier-item-label"><?php echo project_child_fcc_text( 'fcc_ui_3b59a86c555faa', 'shared' ); ?></span>
              <span class="pd-dossier-item-value">0985 561 862</span>
              <span class="pd-dossier-item-sub"><?php echo project_child_fcc_text( 'fcc_ui_a93e048327d51f', 'shared' ); ?></span>
            </div>
          </div>
        </div>

        
        <div class="pd-actives-section">
          <div class="section-header">
            <span class="section-tag"><?php echo project_child_fcc_text( 'fcc_ui_8b8deb3c94c1ca', 'shared' ); ?></span>
            <h2 class="section-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_5adcdfe588f53d', 'shared' ); ?></h2>
            <p class="section-desc"><?php echo project_child_fcc_text( 'fcc_ui_79ee3469c0cac9', 'shared' ); ?></p>
          </div>
          <div class="pd-actives-grid" id="pd-actives-grid"><?php project_child_fcc_fragment( 'actives' ); ?></div>
        </div>

        
        <div class="pd-usage-section">
          <div class="section-header">
            <span class="section-tag"><?php echo project_child_fcc_text( 'fcc_ui_f5310755fa3015', 'shared' ); ?></span>
            <h2 class="section-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_42526f178a133b', 'shared' ); ?></h2>
            <p class="section-desc"><?php echo project_child_fcc_text( 'fcc_ui_2ec853bc877a83', 'shared' ); ?></p>
          </div>
          <div class="pd-twocol-grid">
            
            <div class="pd-card-panel">
              <div class="pd-panel-header">
                <div class="pd-panel-icon">
                  <svg width="20" height="20" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><path d="m4.93 4.93 4.24 4.24"></path><path d="m14.83 9.17 4.24-4.24"></path><path d="m14.83 14.83 4.24 4.24"></path><path d="m9.17 14.83-4.24 4.24"></path></svg>
                </div>
                <h3 class="pd-panel-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_7766fc9ecb857b', 'shared' ); ?></h3>
              </div>
              <ul class="pd-target-list" id="pd-target-list"><?php project_child_fcc_fragment( 'targets' ); ?></ul>
            </div>

            
            <div class="pd-card-panel">
              <div class="pd-panel-header">
                <div class="pd-panel-icon">
                  <svg width="20" height="20" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"></path><path d="M6 6h10"></path><path d="M6 10h10"></path></svg>
                </div>
                <h3 class="pd-panel-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_e9dfbe7e600722', 'shared' ); ?></h3>
              </div>
              <div class="pd-steps-list" id="pd-steps-list"><?php project_child_fcc_fragment( 'steps' ); ?></div>

              
              <div class="pd-tingling-box">
                <div class="pd-tingling-icon">
                  <svg width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v4"></path><path d="m4.93 4.93 2.83 2.83"></path><path d="M2 12h4"></path><path d="m4.93 19.07 2.83-2.83"></path><path d="M12 22v-4"></path><path d="m19.07 19.07-2.83-2.83"></path><path d="M22 12h-4"></path><path d="m19.07 4.93-2.83 2.83"></path></svg>
                </div>
                <p class="pd-tingling-text" id="pd-note-text"><?php echo esc_html( project_child_fcc_product_value( 'notes' ) ); ?></p>
              </div>
            </div>
          </div>
        </div>

        
        <div class="pd-combos-section">
          <div class="section-header">
            <span class="section-tag"><?php echo project_child_fcc_text( 'fcc_ui_cfd8da94dbbe98', 'shared' ); ?></span>
            <h2 class="section-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_6a7e8255f4bd36', 'shared' ); ?></h2>
            <p class="section-desc"><?php echo project_child_fcc_text( 'fcc_ui_345ca4d93b0bd2', 'shared' ); ?></p>
          </div>
          <div class="combos-grid" id="pd-related-combos" style="grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));"><?php project_child_fcc_fragment( 'combos' ); ?></div>
        </div>

        
        <div class="pd-order-box" id="pd-order-section">
          <div class="pd-order-header">
            <span class="badge-pill badge-gold" style="margin-bottom: 8px;"><?php echo project_child_fcc_text( 'fcc_ui_6bcf2a73908762', 'shared' ); ?></span>
            <h3 class="heading-serif" style="font-size: 28px; margin-bottom: 8px;"><?php echo project_child_fcc_text( 'fcc_ui_231220a45140b9', 'shared' ); ?></h3>
            <p style="font-size: 14.5px; color: var(--color-text-muted); max-width: 560px; margin: 0 auto;">
              <?php echo project_child_fcc_text( 'fcc_ui_162da04035299f', 'shared' ); ?>
            </p>
          </div>

          <form id="inline-order-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <input type="text" name="website_url" style="display:none !important;" tabindex="-1" autocomplete="off">
            <div class="form-grid">
              <div class="form-grid-2 form-grid">
                <div class="form-group">
                  <label class="form-label" for="inline-order-form-customer_name"><?php echo project_child_fcc_text( 'fcc_ui_c0f92fda1faf20', 'shared', false ); ?> <span class="required"><?php echo project_child_fcc_text( 'fcc_ui_bc90e573831e54', 'shared', false ); ?></span></label>
                  <input type="text" name="customer_name" class="form-control" placeholder="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_e92a39efe375cb', 'shared' ) ); ?>" required id="inline-order-form-customer_name">
                </div>
                <div class="form-group">
                  <label class="form-label" for="inline-order-form-customer_phone"><?php echo project_child_fcc_text( 'fcc_ui_2599277f442df4', 'shared', false ); ?> <span class="required"><?php echo project_child_fcc_text( 'fcc_ui_bc90e573831e54', 'shared', false ); ?></span></label>
                  <input type="tel" name="customer_phone" class="form-control" placeholder="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_e023e68eba385b', 'shared' ) ); ?>" required id="inline-order-form-customer_phone">
                </div>
              </div>
              <div class="form-group">
                <label class="form-label" for="inline-product-select"><?php echo project_child_fcc_text( 'fcc_ui_8a73cc57b45bc2', 'shared', false ); ?> <span class="required"><?php echo project_child_fcc_text( 'fcc_ui_bc90e573831e54', 'shared', false ); ?></span></label>
                <select name="selected_item" class="form-control form-select" required id="inline-product-select"><?php project_child_fcc_order_options(); ?></select>
              </div>
              <div class="form-group">
                <label class="form-label" for="inline-order-form-customer_address"><?php echo project_child_fcc_text( 'fcc_ui_d4a1dc81975845', 'shared', false ); ?></label>
                <input type="text" name="customer_address" class="form-control" placeholder="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_44ef8652c97ae4', 'shared' ) ); ?>" id="inline-order-form-customer_address">
              </div>
              <div class="form-group">
                <label class="form-label" for="inline-order-form-customer_note"><?php echo project_child_fcc_text( 'fcc_ui_7ba461f49016e9', 'shared', false ); ?></label>
                <textarea name="customer_note" class="form-control" placeholder="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_814bc7fe54e46d', 'shared' ) ); ?>" id="inline-order-form-customer_note" style="min-height: 70px;"></textarea>
              </div>
              <div class="form-checkbox-row">
                <input type="checkbox" name="agree_terms" id="pd-agree" required>
                <label for="pd-agree"><?php echo project_child_fcc_text( 'fcc_ui_1a2520ccc60375', 'shared', false ); ?></label>
              </div>
              <button type="submit" class="btn-submit-order" style="padding: 18px; font-size: 16px;"><?php echo project_child_fcc_text( 'fcc_ui_f2e6976d2f8823', 'shared', false ); ?></button>
            </div>
          <?php project_child_fcc_form_fields(); ?></form>
        </div>
      </div>
    </section>
