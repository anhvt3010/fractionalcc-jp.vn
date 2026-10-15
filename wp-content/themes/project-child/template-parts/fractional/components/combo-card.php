<?php
/**
 * Combo card component for the product catalog grid.
 *
 * @package Project_Child
 */

defined( 'ABSPATH' ) || exit;

$combo = isset( $args['combo'] ) ? $args['combo'] : null;
if ( ! $combo ) {
	return;
}

// Build comprehensive search tokens (names, code, products included, idealFor, etc.)
$search_parts = array(
	'combo',
	'bộ đôi',
	$combo['name'],
	$combo['shortName'],
	$combo['code'],
	$combo['idealFor'],
	$combo['gift'],
);
if ( ! empty( $combo['productsIncluded'] ) && is_array( $combo['productsIncluded'] ) ) {
	$search_parts = array_merge( $search_parts, $combo['productsIncluded'] );
}
$search_attr = mb_strtolower( implode( ' ', array_filter( $search_parts ) ) );

// Map skin concerns (Combo C-CX4 addresses brightening, recovery, aging)
$concerns = array( 'brightening', 'recovery' );
if ( false !== mb_stripos( $search_attr, 'lão hóa' ) || false !== mb_stripos( $search_attr, 'nếp nhăn' ) ) {
	$concerns[] = 'aging';
}
if ( false !== mb_stripos( $search_attr, 'lỗ chân lông' ) || false !== mb_stripos( $search_attr, 'da sần' ) ) {
	$concerns[] = 'pore';
}
if ( false !== mb_stripos( $search_attr, 'môi' ) ) {
	$concerns[] = 'lip';
}
$concerns_attr = implode( ' ', array_unique( $concerns ) );
$order_select  = project_child_fcc_order_code( 'combo_' . $combo['code'] );
$badge_text    = ! empty( $combo['badge'] ) ? $combo['badge'] : 'COMBO TIẾT KIỆM';
$detail_url    = ! empty( $args['home'] ) ? project_core_fcc_url( 'catalog' ) . '#danh-sach-combo' : '#danh-sach-combo';
$rating        = ! empty( $args['home'] ) ? (string) get_post_meta( $combo['id'], 'fcc_spotlightRating', true ) : project_child_fcc_rating_text( $combo );
$has_hover     = ! empty( $combo['hoverImage'] );
?>
<div class="product-card product-card-combo" data-id="combo-<?php echo esc_attr( $combo['id'] ); ?>" data-type="combo" data-concerns="<?php echo esc_attr( $concerns_attr ); ?>" data-search="<?php echo esc_attr( $search_attr ); ?>">
	<?php get_template_part( 'template-parts/fractional/components/product-tags', null, array( 'tags' => $combo['cardTags'] ?? array() ) ); ?>
	<a class="product-thumb<?php echo $has_hover ? ' has-hover-image' : ''; ?>" href="<?php echo esc_url( $detail_url ); ?>" aria-label="<?php echo esc_attr( $combo['name'] ); ?>">
		<?php echo project_child_fcc_picture_html( $combo['image'], $combo['name'], 'product-thumb-primary', array( 'loading' => 'lazy', 'sizes' => '(min-width: 992px) 400px, 50vw' ) ); ?>
		<?php if ( $has_hover ) : ?>
			<?php echo project_child_fcc_picture_html( $combo['hoverImage'], $combo['hoverImageAlt'] ?: $combo['name'], 'product-thumb-hover', array( 'loading' => 'lazy', 'sizes' => '(min-width: 992px) 400px, 50vw' ) ); ?>
		<?php endif; ?>
		<span class="product-cbmp-tag"><?php echo esc_html( $badge_text ); ?></span>
	</a>
	<div class="product-info">
		<span class="product-category-name"><?php echo project_child_fcc_text( 'fcc_ui_m_0154b155cb8d', 'shared' ); ?> <?php echo esc_html( $combo['code'] ); ?></span>
		<?php if ( '' !== $rating ) : ?><div class="card-rating-row"><span class="card-rating-stars">★★★★★</span><span><?php echo esc_html( $rating ); ?></span></div><?php endif; ?>
		<h3 class="product-title" title="<?php echo esc_attr( $combo['name'] ); ?>">
			<a href="<?php echo esc_url( $detail_url ); ?>"><?php echo esc_html( $combo['name'] ); ?></a>
		</h3>
		<p class="product-desc">
			<?php echo esc_html( ! empty( $combo['productsIncluded'] ) ? implode( ' + ', $combo['productsIncluded'] ) : $combo['idealFor'] ); ?>
		</p>
		<div class="product-price-row">
			<span class="product-price-current"><?php echo esc_html( project_child_fcc_price( $combo['salePrice'] ) ); ?></span>
			<?php if ( ! empty( $combo['originalPrice'] ) ) : ?>
				<span class="product-price-original"><?php echo esc_html( project_child_fcc_price( $combo['originalPrice'] ) ); ?></span>
			<?php endif; ?>
		</div>
		<div class="product-actions">
			<a href="<?php echo esc_url( $detail_url ); ?>" class="btn-card-view"><?php echo project_child_fcc_text( 'fcc_ui_m_33b5b579e52f', 'shared' ); ?></a>
			<button class="btn-card-buy" data-open-order data-product-select="<?php echo esc_attr( $order_select ); ?>"><?php echo project_child_fcc_text( 'fcc_ui_m_b2d24c22f0ff', 'shared' ); ?></button>
		</div>
	</div>
</div>
