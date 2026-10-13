<?php defined( 'ABSPATH' ) || exit; ?>
<section class="section section-soft" id="danh-muc">
      <div class="container">
        <div class="section-header">
          <span class="section-tag"><?php echo project_child_fcc_text( 'fcc_ui_63b89170181142', 'home' ); ?></span>
          <h2 class="section-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_44137315906c33', 'home' ); ?></h2>
          <p class="section-desc"><?php echo project_child_fcc_text( 'fcc_ui_ab00a54528cf05', 'home' ); ?></p>
        </div>

        <div class="brief-product-grid">
          <?php project_child_fcc_fragment( 'home_catalog' ); ?>
        </div>


        <div class="fcc-catalog-more-box">
          <p class="fcc-catalog-more-text">
            <?php echo project_child_fcc_text( 'fcc_ui_573d0aefa67d9b', 'home' ); ?>
          </p>
          <a href="<?php echo esc_url( project_child_fcc_link( 'san-pham.html' ) ); ?>" class="btn btn-outline fcc-catalog-more-btn">
            <?php echo project_child_fcc_text( 'fcc_ui_839b3de79eb53b', 'home', false ); ?>
          </a>
        </div>
      </div>
    </section>
