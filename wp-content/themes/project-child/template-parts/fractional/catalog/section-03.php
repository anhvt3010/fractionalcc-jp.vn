<?php defined( 'ABSPATH' ) || exit; ?>
<section class="section section-soft" id="danh-sach-san-pham">
      <div class="container">
        <div class="section-header">
          <span class="section-tag"><?php echo project_child_fcc_text( 'fcc_ui_34f1c64852fcf1', 'catalog' ); ?></span>
          <h2 class="section-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_c05ac9ce8537b8', 'catalog' ); ?></h2>
          <p class="section-desc"><?php echo project_child_fcc_text( 'fcc_ui_349eefd455ce28', 'catalog' ); ?></p>
        </div>

        <?php
        $fcc_combos_list   = function_exists( 'project_core_fcc_combos' ) ? project_core_fcc_combos() : array();
        $fcc_products_list = function_exists( 'project_core_fcc_posts' ) ? project_core_fcc_posts( 'fcc_product' ) : array();
        $fcc_combo_count   = count( $fcc_combos_list );
        $fcc_total_count   = count( $fcc_products_list ) + $fcc_combo_count;
        ?>
        <div class="catalog-layout">
          <aside class="catalog-sidebar">
            <div class="catalog-filter-bar">
              <div class="catalog-filter-head">
                <div class="catalog-filter-title">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -3px; margin-right: 6px; color: var(--color-accent-gold);"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                  <span><?php echo project_child_fcc_text( 'fcc_ui_m_e49982d492e8', 'catalog' ); ?></span>
                </div>
                <button type="button" class="catalog-filter-reset-link" onclick="if(window.resetFilters)window.resetFilters();" title="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_m_74bbdb6f91ad', 'catalog' ) ); ?>">
                  <?php echo project_child_fcc_text( 'fcc_ui_m_70b9a85fe261', 'catalog' ); ?>
                </button>
              </div>

              <div class="filter-group filter-group-search">
                <div class="filter-group-title"><?php echo project_child_fcc_text( 'fcc_ui_m_ab18d55d894d', 'catalog' ); ?></div>
                <div class="search-input-wrap">
                  <span class="search-icon-placeholder" aria-hidden="true">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <circle cx="11" cy="11" r="8"></circle>
                      <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                  </span>
                  <input type="text" id="catalog-search-input" placeholder="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_m_9e15ecef7bc1', 'catalog' ) ); ?>">
                </div>
              </div>

              <div class="filter-group">
                <div class="filter-group-title"><?php echo project_child_fcc_text( 'fcc_ui_48798253ca89eb', 'catalog' ); ?></div>
                <div class="filter-btn-group">
                  <button class="filter-pill catalog-filter-btn active" data-type="all"><?php echo project_child_fcc_text( 'fcc_ui_m_d18cedce708b', 'catalog' ); ?><?php echo esc_html( $fcc_total_count ); ?>)</button>
                  <button class="filter-pill catalog-filter-btn" data-type="combo"><?php echo project_child_fcc_text( 'fcc_ui_m_9163e78c6936', 'catalog' ); ?><?php echo esc_html( $fcc_combo_count ); ?>)</button>
                  <button class="filter-pill catalog-filter-btn" data-type="serum"><?php echo project_child_fcc_text( 'fcc_ui_194cc0829f0ee7', 'catalog', false ); ?></button>
                  <button class="filter-pill catalog-filter-btn" data-type="mask"><?php echo project_child_fcc_text( 'fcc_ui_60fc74489364b1', 'catalog', false ); ?></button>
                  <button class="filter-pill catalog-filter-btn" data-type="peel"><?php echo project_child_fcc_text( 'fcc_ui_0b2ba17ebf6f89', 'catalog', false ); ?></button>
                  <button class="filter-pill catalog-filter-btn" data-type="lip"><?php echo project_child_fcc_text( 'fcc_ui_9a4d37ad95961e', 'catalog', false ); ?></button>
                </div>
              </div>

              <div class="filter-group">
                <div class="filter-group-title"><?php echo project_child_fcc_text( 'fcc_ui_095f2e7b9d189c', 'catalog' ); ?></div>
                <div class="filter-btn-group">
                  <button class="filter-pill concern-filter-btn active" data-concern="all"><?php echo project_child_fcc_text( 'fcc_ui_7b2318e8669115', 'catalog', false ); ?></button>
                  <button class="filter-pill concern-filter-btn" data-concern="brightening"><?php echo project_child_fcc_text( 'fcc_ui_a285f700f0fa89', 'catalog', false ); ?></button>
                  <button class="filter-pill concern-filter-btn" data-concern="aging"><?php echo project_child_fcc_text( 'fcc_ui_454e9c381df9a7', 'catalog', false ); ?></button>
                  <button class="filter-pill concern-filter-btn" data-concern="pore"><?php echo project_child_fcc_text( 'fcc_ui_c590d5d4344453', 'catalog', false ); ?></button>
                  <button class="filter-pill concern-filter-btn" data-concern="recovery"><?php echo project_child_fcc_text( 'fcc_ui_606279bbd299c3', 'catalog', false ); ?></button>
                  <button class="filter-pill concern-filter-btn" data-concern="lip"><?php echo project_child_fcc_text( 'fcc_ui_6e9d38765efb04', 'catalog', false ); ?></button>
                </div>
              </div>
            </div>
          </aside>

          <div class="catalog-main">
            <div class="catalog-toolbar">
              <div id="catalog-count-display" class="catalog-count-display">
                <?php echo project_child_fcc_text( 'fcc_ui_m_64de9e7f299d', 'catalog' ); ?> <?php echo esc_html( $fcc_total_count . '/' . $fcc_total_count ); ?> <?php echo project_child_fcc_text( 'fcc_ui_m_20e21a88f38a', 'catalog' ); ?>
              </div>
            </div>

            <div class="brief-product-grid" id="product-catalog-grid"><?php project_child_fcc_fragment( 'catalog' ); ?></div>
          </div>
        </div>
      </div>
    </section>
