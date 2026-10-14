<?php defined( 'ABSPATH' ) || exit; ?>
<div class="hero-viewport-wrap">
      
      <section <?php project_child_fcc_hero_background_attributes(); ?>>
        <div class="container hero-grid fcc-hero-product-first">
          <div class="hero-content">
            <div class="hero-badge-wrap">
              <span class="badge-pill badge-gold"><svg width="14" height="14" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="badge-icon" aria-hidden="true"><path d="M3 18h18M4 18l2-10 6 6 6-6 2 10H4z"></path></svg> <?php echo project_child_fcc_text( 'fcc_ui_fbc8886b40af10', 'home' ); ?></span>
              <span class="badge-pill badge-dark"><svg width="14" height="14" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="badge-icon" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="m9 12 2 2 4-4"></path></svg> <?php echo project_child_fcc_text( 'fcc_ui_1b556d0408712c', 'home' ); ?></span>
            </div>
            
            <h1 class="hero-title heading-serif">
              <span class="hero-title-line hero-title-line-1"><?php echo project_child_fcc_text( 'fcc_ui_487435027051ea', 'home' ); ?></span>
              <span class="hero-title-line hero-title-line-2 hero-title-gold"><?php echo project_child_fcc_text( 'fcc_ui_7c4ad8cffee502', 'home' ); ?></span>
            </h1>
            
            <p class="hero-subtitle">
              <?php echo project_child_fcc_text( 'fcc_ui_543aa81dfd94d5', 'home' ); ?> <strong><?php echo project_child_fcc_text( 'fcc_ui_88efa52705849b', 'home' ); ?></strong>
            </p>

          </div>

          <div class="hero-visual">
            <div class="hero-card">
              <?php
              echo project_child_fcc_picture_html(
                  project_child_fcc_hero_product_image_url(),
                  project_child_fcc_hero_product_image_alt(),
                  'hero-banner-img',
                  array(
                      'width'         => 441,
                      'height'        => 432,
                      'loading'       => 'eager',
                      'fetchpriority' => 'high',
                      'sizes'         => project_child_fcc_hero_image_sizes(),
                  )
              );
              ?>
            </div>
          </div>

          <div class="hero-conversion">
            <div class="hero-cta-group">
              <button class="btn btn-primary" data-open-order data-product-select="<?php echo esc_attr( project_child_fcc_order_code( 'combo_C-CX4' ) ); ?>">
                <?php echo project_child_fcc_text( 'fcc_ui_e05dc8077446a2', 'home', false ); ?>
              </button>
              <a href="#san-pham-chu-luc" class="btn btn-outline">
                <?php echo project_child_fcc_text( 'fcc_ui_378ce494d40351', 'home', false ); ?>
              </a>
            </div>

            <div class="hero-trust-micro">
              <div class="hero-stars">★★★★★</div>
              <span><?php echo project_child_fcc_text( 'fcc_ui_898c9707cbc6fb', 'home' ); ?></span>
            </div>
          </div>

        </div>
      </section>

      
      <section class="trust-bar">
        <div class="container">
          <div class="trust-bar-grid">
            
            <div class="trust-bar-item">
              <div class="trust-bar-icon-wrap" title="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_722cc950bbd51f', 'shared' ) ); ?>">
                <svg width="28" height="20" viewbox="0 0 24 16" fill="none" xmlns="http://www.w3.org/2000/svg" style="border-radius: 2px; box-shadow: 0 0 2px rgba(0,0,0,0.25);">
                  <rect width="24" height="16" fill="#FFFFFF"></rect>
                  <circle cx="12" cy="8" r="4.8" fill="#BC002D"></circle>
                </svg>
              </div>
              <div class="trust-bar-content">
                <div class="trust-bar-title"><?php echo project_child_fcc_text( 'fcc_ui_c92fd3458a307a', 'home' ); ?></div>
                <p><?php echo project_child_fcc_text( 'fcc_ui_18645916ec2e37', 'home' ); ?></p>
              </div>
            </div>

            
            <div class="trust-bar-item">
              <div class="trust-bar-icon-wrap" title="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_0db343ac35a4d7', 'shared' ) ); ?>">
                <?php
                echo project_child_fcc_picture_html(
                    project_child_fcc_image( 'fcc_ui_be08f7c57f78ec' ),
                    project_child_fcc_image_alt( 'fcc_ui_be08f7c57f78ec', 'fcc_ui_6d82420f357692' ),
                    '',
                    array(
                        'width'  => 40,
                        'height' => 40,
                        'style'  => 'width: 40px; height: 40px; object-fit: contain; border-radius: 50%;',
                    )
                );
                ?>
              </div>
              <div class="trust-bar-content">
                <div class="trust-bar-title"><?php echo project_child_fcc_text( 'fcc_ui_7f36fd34e50688', 'home' ); ?></div>
                <p><?php echo project_child_fcc_text( 'fcc_ui_3cb5377b9686ad', 'home' ); ?></p>
              </div>
            </div>

            
            <div class="trust-bar-item">
              <div class="trust-bar-icon-wrap" title="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_2e477cc3b39cf0', 'shared' ) ); ?>">
                <svg width="22" height="22" viewbox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                  <path d="m9 12 2 2 4-4"></path>
                </svg>
              </div>
              <div class="trust-bar-content">
                <div class="trust-bar-title"><?php echo project_child_fcc_text( 'fcc_ui_7fae43eabd17c5', 'home' ); ?></div>
                <p><?php echo project_child_fcc_text( 'fcc_ui_740d386bb06816', 'home' ); ?></p>
              </div>
            </div>

            
            <div class="trust-bar-item">
              <div class="trust-bar-icon-wrap" title="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_8ce2cfbee099eb', 'shared' ) ); ?>">
                <svg width="24" height="24" viewbox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <line x1="1" y1="10" x2="4" y2="10"></line>
                  <line x1="2" y1="13" x2="5" y2="13"></line>
                  <rect x="5" y="7" width="10" height="9" rx="1"></rect>
                  <path d="M15 10h3.5l2.5 3.5v2.5h-6V10z"></path>
                  <circle cx="8.5" cy="18" r="2"></circle>
                  <circle cx="18" cy="18" r="2"></circle>
                </svg>
              </div>
              <div class="trust-bar-content">
                <div class="trust-bar-title"><?php echo project_child_fcc_text( 'fcc_ui_71d9bd21e43bed', 'home' ); ?></div>
                <p><?php echo project_child_fcc_text( 'fcc_ui_ad6fe4262d4b15', 'home' ); ?></p>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
