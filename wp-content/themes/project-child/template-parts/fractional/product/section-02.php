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

              <img id="pd-main-image" class="pd-main-img" src="<?php echo esc_url( project_child_fcc_product_value( 'image' ) ); ?>" alt="<?php echo esc_attr( project_child_fcc_product_value( 'imageAlt' ) ); ?>">
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

            <?php $fcc_detail_rating = project_child_fcc_rating_text( project_core_fcc_product( get_queried_object_id(), false ), true ); ?>
            <?php if ( '' !== $fcc_detail_rating ) : ?>
            <div class="pd-cocoon-rating">
              <span class="pd-stars">★★★★★</span>
              <span><?php echo esc_html( $fcc_detail_rating ); ?></span>
            </div>
            <?php endif; ?>

            
            <p class="pd-cocoon-desc" id="pd-tagline"><?php echo esc_html( project_child_fcc_product_value( 'tagline' ) ); ?></p>

            
            <div class="pd-cocoon-claims">
              <div class="pd-claim-item">
                <svg width="17" height="12" viewbox="0 0 24 16" fill="none" style="border-radius:2px;box-shadow:0 0 2px rgba(0,0,0,0.25);flex-shrink:0;"><rect width="24" height="16" fill="#FFFFFF"></rect><circle cx="12" cy="8" r="4.8" fill="#BC002D"></circle></svg>
                <span><?php echo project_child_fcc_text( 'fcc_ui_476e0450734143', 'shared' ); ?></span>
              </div>
              <span class="pd-claim-sep">•</span>
              <div class="pd-claim-item">
                <svg width="15" height="15" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><path d="m4.93 4.93 4.24 4.24"></path><path d="m14.83 9.17 4.24-4.24"></path><path d="m14.83 14.83 4.24 4.24"></path><path d="m9.17 14.83-4.24 4.24"></path></svg>
                <span><?php echo project_child_fcc_text( 'fcc_ui_bcf7198870556e', 'shared' ); ?></span>
              </div>
              <span class="pd-claim-sep">•</span>
              <div class="pd-claim-item">
                <svg width="15" height="15" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="m9 12 2 2 4-4"></path></svg>
                <span><?php echo project_child_fcc_text( 'fcc_ui_a4761e5516401e', 'shared' ); ?></span>
              </div>
              <span class="pd-claim-sep">•</span>
              <div class="pd-claim-item">
                <svg width="15" height="15" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="20" height="14" x="2" y="5" rx="2"></rect><line x1="2" x2="22" y1="10" y2="10"></line></svg>
                <span><?php echo project_child_fcc_text( 'fcc_ui_3a43752a881468', 'shared' ); ?></span>
              </div>
            </div>

            
            <?php
            $fcc_current_product = function_exists( 'project_core_fcc_product' ) ? project_core_fcc_product( get_queried_object_id(), true ) : array();
            $fcc_targets         = ! empty( $fcc_current_product['targetUsers'] ) ? (array) $fcc_current_product['targetUsers'] : array();
            if ( empty( $fcc_targets ) ) {
              $fcc_target_sum = project_child_fcc_product_value( 'target_summary' );
              if ( $fcc_target_sum ) {
                $fcc_targets = array_filter( array_map( 'trim', explode( '•', $fcc_target_sum ) ) );
              }
            }
            $fcc_actives = ! empty( $fcc_current_product['actives'] ) ? (array) $fcc_current_product['actives'] : array();
            $fcc_usps    = ! empty( $fcc_current_product['uspBullets'] ) ? (array) $fcc_current_product['uspBullets'] : array();
            ?>
            <!-- Section: Tabs (Thích hợp với / Thành phần chính / Điểm Nổi Bật) -->
            <div class="pd-cocoon-section pd-tabs-section">
              <div class="pd-tabs-nav" role="tablist" aria-label="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_m_19d74a6b4cae', 'shared' ) ); ?>">
                <button type="button" role="tab" class="pd-tab-btn is-active" id="pd-tab-targets-btn" aria-selected="true" aria-controls="pd-tab-targets-pane">
                  <?php echo project_child_fcc_text( 'fcc_ui_2cacb01c3941c0', 'shared' ); ?>
                </button>
                <button type="button" role="tab" class="pd-tab-btn" id="pd-tab-actives-btn" aria-selected="false" aria-controls="pd-tab-actives-pane">
                  <?php echo project_child_fcc_text( 'fcc_ui_b2f2c633e021b5', 'shared' ); ?>
                </button>
                <button type="button" role="tab" class="pd-tab-btn" id="pd-tab-usps-btn" aria-selected="false" aria-controls="pd-tab-usps-pane">
                  <?php $tab_usp_label = project_child_fcc_text( 'fcc_ui_tab_usps', 'shared' ); echo $tab_usp_label ? $tab_usp_label : esc_html( 'Điểm Nổi Bật' ); ?>
                </button>
              </div>

              <div class="pd-tab-panes">
                <div class="pd-tab-pane is-active" id="pd-tab-targets-pane" role="tabpanel" aria-labelledby="pd-tab-targets-btn">
                  <ul class="pd-tab-bullet-list" id="pd-target-list-bullets">
                    <?php foreach ( $fcc_targets as $t_item ) :
                      $t_clean = project_child_fcc_clean_bullet( $t_item );
                      if ( '' === $t_clean ) { continue; }
                    ?>
                      <li>
                        <span class="pd-bullet-check" aria-hidden="true">
                          <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                            <circle cx="10" cy="10" r="9" fill="#FAF6EE" stroke="#C5A059" stroke-width="1.4"/>
                            <path d="M6 10.2L8.8 13L14 7.8" stroke="#8C6A28" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                          </svg>
                        </span>
                        <span class="pd-bullet-text"><?php echo esc_html( $t_clean ); ?></span>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                  <p class="pd-cocoon-text sr-only" id="pd-target-summary" style="display:none;"><?php echo esc_html( project_child_fcc_product_value( 'target_summary' ) ); ?></p>
                </div>

                <div class="pd-tab-pane" id="pd-tab-actives-pane" role="tabpanel" aria-labelledby="pd-tab-actives-btn" hidden>
                  <ul class="pd-tab-bullet-list" id="pd-actives-list-bullets">
                    <?php
                    if ( ! empty( $fcc_actives ) ) :
                      foreach ( $fcc_actives as $a_item ) :
                        $a_clean = project_child_fcc_clean_bullet( is_array( $a_item ) ? $a_item['name'] : $a_item );
                        if ( '' === $a_clean ) { continue; }
                      ?>
                        <li><span class="pd-bullet-dash">—</span><span class="pd-bullet-text"><?php echo esc_html( $a_clean ); ?></span></li>
                      <?php endforeach;
                    else :
                      $fcc_actives_sum = project_child_fcc_product_value( 'active_summary' );
                      if ( $fcc_actives_sum ) :
                        $fcc_actives_list = array_filter( array_map( 'trim', explode( '•', $fcc_actives_sum ) ) );
                        foreach ( $fcc_actives_list as $a_item ) :
                          $a_clean = project_child_fcc_clean_bullet( $a_item );
                          if ( '' === $a_clean ) { continue; }
                        ?>
                          <li><span class="pd-bullet-dash">—</span><span class="pd-bullet-text"><?php echo esc_html( $a_clean ); ?></span></li>
                        <?php endforeach;
                      endif;
                    endif;
                    ?>
                  </ul>
                  <p class="pd-cocoon-text sr-only" id="pd-actives-summary" style="display:none;"><?php echo esc_html( project_child_fcc_product_value( 'active_summary' ) ); ?></p>
                </div>

                <div class="pd-tab-pane" id="pd-tab-usps-pane" role="tabpanel" aria-labelledby="pd-tab-usps-btn" hidden>
                  <ul class="pd-tab-bullet-list pd-tab-usp-list" id="pd-usps-list-bullets">
                    <?php foreach ( $fcc_usps as $u_item ) :
                      $u_clean = project_child_fcc_clean_bullet( $u_item );
                      if ( '' === $u_clean ) { continue; }
                    ?>
                      <li>
                        <span class="pd-bullet-check" aria-hidden="true">
                          <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                            <circle cx="10" cy="10" r="9" fill="#FAF6EE" stroke="#C5A059" stroke-width="1.4"/>
                            <path d="M6 10.2L8.8 13L14 7.8" stroke="#8C6A28" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                          </svg>
                        </span>
                        <span class="pd-bullet-text"><?php echo esc_html( $u_clean ); ?></span>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              </div>
            </div>

            <div class="pd-cocoon-meta-line">
              <span><strong><?php echo project_child_fcc_text( 'fcc_ui_7c2ab4b4eda5ba', 'shared' ); ?></strong> <span id="pd-volume"><?php echo esc_html( project_child_fcc_product_value( 'volume' ) ); ?></span></span>
              <span>•</span>
              <span><strong><?php echo project_child_fcc_text( 'fcc_ui_09caa8fe5fdc81', 'shared' ); ?></strong> <span id="pd-spec-cbmp" class="text-gold"><?php echo esc_html( project_child_fcc_product_value( 'cbmp' ) ); ?></span></span>
            </div>

            <button class="btn-pd-cocoon-buy" data-open-order data-product-select="prod_<?php echo esc_attr( get_queried_object_id() ); ?>" id="pd-buy-now-btn">
              <span id="pd-buy-btn-label"><?php echo esc_html( project_child_fcc_product_value( 'buy' ) ); ?></span>
              <svg width="20" height="20" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
            </button>

            <div class="pd-cocoon-sub-bar">
              <a <?php project_child_fcc_contact_attributes( 'zalo' ); ?> target="_blank" rel="noopener noreferrer" class="pd-cocoon-sub-link">
                <svg width="15" height="15" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                <span><?php echo project_child_fcc_text( 'fcc_ui_20748aba1fa4a2', 'shared', false ); ?></span>
              </a>
              <span>•</span>
              <a <?php project_child_fcc_contact_attributes( 'hotline' ); ?> class="pd-cocoon-sub-link">
                <svg width="15" height="15" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                <span><?php echo project_child_fcc_text( 'fcc_ui_d0f30a260b8018', 'shared', false ); ?></span>
              </a>
              <span>•</span>
              <span><?php echo project_child_fcc_text( 'fcc_ui_82c0e470cd9121', 'shared' ); ?></span>
            </div>

            
          </div>
        </div>


        
        <div class="pd-dossier-section" id="pd-dossier">
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
              <span class="pd-dossier-item-value"><?php echo esc_html( project_core_fcc_contact( 'hotlineDisplay' ) ); ?></span>
              <span class="pd-dossier-item-sub"><?php echo project_child_fcc_text( 'fcc_ui_a93e048327d51f', 'shared' ); ?></span>
            </div>
          </div>
        </div>

        
        <!-- COMBINED SECTION: INGREDIENTS & USAGE -->
        <div class="pd-usage-section">
          <div class="section-header">
            <span class="section-tag"><?php echo project_child_fcc_text( 'fcc_ui_8b8deb3c94c1ca', 'shared' ); ?></span>
            <h2 class="section-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_42526f178a133b', 'shared' ); ?></h2>
            <p class="section-desc"><?php echo project_child_fcc_text( 'fcc_ui_2ec853bc877a83', 'shared' ); ?></p>
          </div>
          <div class="pd-twocol-grid">
            <!-- COLUMN 1: ACTIVES & MECHANISMS -->
            <div class="pd-card-panel">
              <div class="pd-panel-header">
                <div class="pd-panel-icon">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 2v7.527a2 2 0 0 1-.211.896L4.72 20.55a1 1 0 0 0 .9 1.45h12.76a1 1 0 0 0 .9-1.45l-5.069-10.127A2 2 0 0 1 14 9.527V2"></path><path d="M8.5 2h7"></path><path d="M7 16h10"></path></svg>
                </div>
                <h3 class="pd-panel-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_5adcdfe588f53d', 'shared' ); ?></h3>
              </div>
              <div class="pd-actives-grid" id="pd-actives-grid"><?php project_child_fcc_fragment( 'actives' ); ?></div>
            </div>

            <!-- COLUMN 2: HOW TO USE -->
            <div class="pd-card-panel">
              <div class="pd-panel-header">
                <div class="pd-panel-icon">
                  <svg width="20" height="20" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"></path><path d="M6 6h10"></path><path d="M6 10h10"></path></svg>
                </div>
                <h3 class="pd-panel-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_e9dfbe7e600722', 'shared' ); ?></h3>
              </div>
              <div class="pd-steps-list" id="pd-steps-list"><?php project_child_fcc_fragment( 'steps' ); ?></div>

              <!-- TINGLING NOTICE -->
              <?php $fcc_product_notes = project_child_fcc_product_value( 'notes' ); ?>
              <?php if ( '' !== trim( (string) $fcc_product_notes ) ) : ?>
              <div class="pd-tingling-box">
                <div class="pd-tingling-icon">
                  <svg width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v4"></path><path d="m4.93 4.93 2.83 2.83"></path><path d="M2 12h4"></path><path d="m4.93 19.07 2.83-2.83"></path><path d="M12 22v-4"></path><path d="m19.07 19.07-2.83-2.83"></path><path d="M22 12h-4"></path><path d="m19.07 4.93-2.83 2.83"></path></svg>
                </div>
                <p class="pd-tingling-text" id="pd-note-text"><?php echo esc_html( $fcc_product_notes ); ?></p>
              </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <?php
        $fcc_has_combos = false;
        if ( function_exists( 'project_core_fcc_combos' ) ) {
          $fcc_current_id = get_queried_object_id();
          foreach ( project_core_fcc_combos() as $combo_check ) {
            if ( ! empty( $combo_check['productIds'] ) && in_array( $fcc_current_id, (array) $combo_check['productIds'], true ) ) {
              $fcc_has_combos = true;
              break;
            }
          }
        }
        if ( $fcc_has_combos ) :
        ?>
        <!-- RELATED COMBOS -->
        <div class="pd-combos-section" id="pd-combos-section">
          <div class="section-header">
            <span class="section-tag"><?php echo project_child_fcc_text( 'fcc_ui_cfd8da94dbbe98', 'shared' ); ?></span>
            <h2 class="section-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_6a7e8255f4bd36', 'shared' ); ?></h2>
            <p class="section-desc"><?php echo project_child_fcc_text( 'fcc_ui_345ca4d93b0bd2', 'shared' ); ?></p>
          </div>
          <div class="combos-grid" id="pd-related-combos"><?php project_child_fcc_fragment( 'combos' ); ?></div>
        </div>
        <?php endif; ?>
      </div>
    </section>
