<?php defined( 'ABSPATH' ) || exit; ?>
<section class="section catalog-consultation-section">
  <div class="container">
    <div class="catalog-consultation-card">
      <h2 class="heading-serif consultation-title"><?php echo project_child_fcc_text( 'fcc_ui_3edc35aff457ed', 'catalog' ); ?></h2>

      <p class="consultation-desc">
        <?php echo project_child_fcc_text( 'fcc_ui_dcbe07c7fc3d6a', 'catalog' ); ?>
      </p>

      <div class="consultation-actions">
        <button class="btn btn-primary consultation-primary-btn" data-open-order>
          <?php echo project_child_fcc_text( 'fcc_ui_b39d8a84e1bf08', 'catalog', false ); ?>
        </button>

        <div class="consultation-quick-text">
          <span>Tư vấn trực tiếp:</span>
          <a href="<?php echo esc_url( project_child_fcc_link( 'https://zalo.me/0985561862' ) ); ?>" target="_blank" rel="noopener noreferrer" class="consultation-link">Chat Zalo</a>
          <span class="consultation-sep" aria-hidden="true">•</span>
          <a href="<?php echo esc_url( project_child_fcc_link( 'tel:0985561862' ) ); ?>" class="consultation-link">Hotline: 0985 561 862</a>
        </div>
      </div>
    </div>
  </div>
</section>

