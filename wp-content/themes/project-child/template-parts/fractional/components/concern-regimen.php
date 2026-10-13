<?php
/** Selected cards reuse live catalog content, in the configured order. @package Project_Child */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'project_core_fcc_regimen_posts' ) ) { return; }
$posts = project_core_fcc_regimen_posts( isset( $args['concern'] ) ? $args['concern'] : '', get_queried_object_id() );
if ( ! $posts ) { return; }
$combos = array();
if ( in_array( 'fcc_combo', wp_list_pluck( $posts, 'post_type' ), true ) ) {
	$combos = array_column( project_core_fcc_combos(), null, 'id' );
}
?>
<div class="concern-products-grid">
	<?php foreach ( $posts as $selected ) : ?>
		<?php if ( 'fcc_product' === $selected->post_type ) : ?>
			<?php get_template_part( 'template-parts/fractional/components/product-card', null, array( 'product' => project_core_fcc_product( $selected->ID, false ) ) ); ?>
		<?php elseif ( isset( $combos[ $selected->ID ] ) ) : ?>
			<?php $combo = $combos[ $selected->ID ]; ?>
			<div class="concern-combo-card-compact" data-id="combo-<?php echo esc_attr( $combo['id'] ); ?>">
				<div class="combo-compact-thumb">
					<img src="<?php echo esc_url( $combo['image'] ); ?>" alt="<?php echo esc_attr( $combo['name'] ); ?>" loading="lazy">
					<span class="combo-compact-badge"><?php echo project_child_fcc_text( 'fcc_ui_m_878351020275', 'shared' ); ?></span>
				</div>
				<div class="combo-compact-info">
					<h4 class="combo-compact-title"><?php echo esc_html( $combo['name'] ); ?></h4>
					<p class="combo-compact-desc"><?php echo esc_html( implode( ' + ', $combo['productsIncluded'] ) ); ?></p>
					<div class="combo-compact-price-row">
						<span class="combo-price-now"><?php echo esc_html( project_child_fcc_price( $combo['salePrice'] ) ); ?></span>
						<?php if ( ! empty( $combo['originalPrice'] ) ) : ?><span class="combo-price-was"><?php echo esc_html( project_child_fcc_price( $combo['originalPrice'] ) ); ?></span><?php endif; ?>
					</div>
					<div class="combo-compact-actions">
						<button class="btn-card-buy" data-open-order data-product-select="combo_<?php echo esc_attr( $combo['id'] ); ?>"><?php echo project_child_fcc_text( 'fcc_ui_m_13f5d50dabf4', 'shared' ); ?></button>
						<a href="<?php echo esc_url( project_core_fcc_url( 'catalog', '#danh-sach-combo' ) ); ?>" class="btn-card-view"><?php echo project_child_fcc_text( 'fcc_ui_m_d377ac936d0f', 'shared' ); ?></a>
					</div>
				</div>
			</div>
		<?php endif; ?>
	<?php endforeach; ?>
</div>
