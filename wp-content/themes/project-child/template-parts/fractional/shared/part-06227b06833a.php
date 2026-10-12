<?php defined( 'ABSPATH' ) || exit; ?>
<div class="mobile-sticky-bar">
    <a href="<?php echo esc_url( project_child_fcc_link( 'tel:0985561862' ) ); ?>" class="btn-mobile-call">
      <span><?php echo project_child_fcc_text( 'fcc_ui_020367c06caf00', 'shared', false ); ?></span>
      <span><?php echo project_child_fcc_text( 'fcc_ui_07358083768019', 'shared', false ); ?></span>
    </a>
    <button class="btn-mobile-buy" data-open-order data-product-select="<?php echo esc_attr( project_child_fcc_order_code( 'combo_C-CX4' ) ); ?>">
      <span><?php echo project_child_fcc_text( 'fcc_ui_783c9befd8e9f8', 'shared', false ); ?></span>
    </button>
  </div>
