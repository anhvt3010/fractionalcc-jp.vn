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
          <span><?php echo project_child_fcc_text( 'fcc_ui_m_955ba00d5b32', 'catalog' ); ?></span>
          <a <?php project_child_fcc_contact_attributes( 'zalo' ); ?> target="_blank" rel="noopener noreferrer" class="consultation-link"><?php echo project_child_fcc_text( 'fcc_ui_m_6d64ee9c2e3b', 'catalog' ); ?></a>
          <span class="consultation-sep" aria-hidden="true">•</span>
          <a <?php project_child_fcc_contact_attributes( 'hotline' ); ?> class="consultation-link"><?php echo project_child_fcc_text( 'fcc_ui_m_5a19a5862bce', 'catalog' ); ?></a>
        </div>
      </div>
    </div>
  </div>
</section>

