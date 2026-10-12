<?php defined( 'ABSPATH' ) || exit; ?>
<section class="section section-soft" id="danh-sach-san-pham">
      <div class="container">
        <div class="section-header" style="text-align: left; margin-bottom: 24px;">
          <span class="section-tag"><?php echo project_child_fcc_text( 'fcc_ui_34f1c64852fcf1', 'catalog' ); ?></span>
          <h2 class="section-title heading-serif" style="font-size: 28px;"><?php echo project_child_fcc_text( 'fcc_ui_c05ac9ce8537b8', 'catalog' ); ?></h2>
          <p class="section-desc"><?php echo project_child_fcc_text( 'fcc_ui_349eefd455ce28', 'catalog' ); ?></p>
        </div>

        
        <div class="catalog-filter-bar">
          <div>
            <div class="filter-group-title"><?php echo project_child_fcc_text( 'fcc_ui_48798253ca89eb', 'catalog' ); ?></div>
            <div class="filter-btn-group">
              <button class="filter-pill catalog-filter-btn active" data-type="all"><?php echo project_child_fcc_text( 'fcc_ui_df07a2537fcd88', 'catalog', false ); ?></button>
              <button class="filter-pill catalog-filter-btn" data-type="serum"><?php echo project_child_fcc_text( 'fcc_ui_194cc0829f0ee7', 'catalog', false ); ?></button>
              <button class="filter-pill catalog-filter-btn" data-type="mask"><?php echo project_child_fcc_text( 'fcc_ui_60fc74489364b1', 'catalog', false ); ?></button>
              <button class="filter-pill catalog-filter-btn" data-type="peel"><?php echo project_child_fcc_text( 'fcc_ui_0b2ba17ebf6f89', 'catalog', false ); ?></button>
              <button class="filter-pill catalog-filter-btn" data-type="lip"><?php echo project_child_fcc_text( 'fcc_ui_9a4d37ad95961e', 'catalog', false ); ?></button>
            </div>
          </div>

          <div>
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

          <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-top: 6px;">
            <div class="search-input-wrap" style="max-width: 380px;">
              <span class="search-icon-placeholder"><?php echo project_child_fcc_text( 'fcc_ui_8d40d1e6f76506', 'catalog' ); ?></span>
              <input type="text" id="catalog-search-input" placeholder="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_89d1e3ccd6cffc', 'shared' ) ); ?>">
            </div>
            <div id="catalog-count-display" style="font-size: 13px; font-weight: 700; color: var(--color-text-muted);">
              <?php echo project_child_fcc_text( 'fcc_ui_272817da17636c', 'catalog' ); ?>
            </div>
          </div>
        </div>

        
        <div class="brief-product-grid" id="product-catalog-grid"><?php project_child_fcc_fragment( 'catalog' ); ?></div>
      </div>
    </section>
