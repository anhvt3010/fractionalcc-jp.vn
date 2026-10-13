<?php
/** Existing buy action, selecting the viewed product where applicable. @package Project_Child */
defined( 'ABSPATH' ) || exit;
?>
<button type="button" class="btn-header-order" data-open-order data-product-select="<?php echo esc_attr( $args['order_code'] ); ?>"><?php echo project_child_fcc_text( 'fcc_ui_9681bed187e219', 'shared', false ); ?></button>
