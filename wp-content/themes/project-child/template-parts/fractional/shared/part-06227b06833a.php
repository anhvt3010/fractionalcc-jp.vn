<?php defined( 'ABSPATH' ) || exit; ?>
<div class="mobile-sticky-bar">
    <a <?php project_child_fcc_contact_attributes( 'hotline' ); ?> class="btn-mobile-call">
      <span><?php echo project_child_fcc_text( 'fcc_ui_020367c06caf00', 'shared', false ); ?></span>
      <span><?php echo project_child_fcc_text( 'fcc_ui_07358083768019', 'shared', false ); ?></span>
    </a>
    <button class="btn-mobile-buy" data-open-order data-product-select="<?php echo esc_attr( project_child_fcc_featured_combo_order_code() ); ?>">
      <span>MUA COMBO</span>
    </button>
  </div>
