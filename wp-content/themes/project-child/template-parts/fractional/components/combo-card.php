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
?>
<div class="product-card product-card-combo" data-id="combo-<?php echo esc_attr( $combo['id'] ); ?>" data-type="combo" data-concerns="<?php echo esc_attr( $concerns_attr ); ?>" data-search="<?php echo esc_attr( $search_attr ); ?>">
	<div class="product-thumb">
		<img src="<?php echo esc_url( $combo['image'] ); ?>" alt="<?php echo esc_attr( $combo['name'] ); ?>" loading="lazy">
		<span class="product-cbmp-tag"><?php echo esc_html( $badge_text ); ?></span>
	</div>
	<div class="product-info">
		<span class="product-category-name" style="color: var(--color-accent-gold); font-weight: 700;">Bộ Đôi Chăm Sóc Da • <?php echo esc_html( $combo['code'] ); ?></span>
		<div class="card-rating-row"><span class="card-rating-stars">★★★★★</span><span>5.0 (1.200+ đánh giá)</span></div>
		<h3 class="product-title" title="<?php echo esc_attr( $combo['name'] ); ?>">
			<a href="#danh-sach-combo"><?php echo esc_html( $combo['shortName'] ); ?></a>
		</h3>
		<p style="font-size: 12.5px; color: var(--color-text-body); margin-bottom: 12px; line-height: 1.45;">
			<?php echo esc_html( ! empty( $combo['productsIncluded'] ) ? implode( ' + ', $combo['productsIncluded'] ) : $combo['idealFor'] ); ?>
		</p>
		<div class="product-price-row">
			<span class="product-price-current"><?php echo esc_html( project_child_fcc_price( $combo['salePrice'] ) ); ?></span>
			<?php if ( ! empty( $combo['originalPrice'] ) ) : ?>
				<span class="product-price-original"><?php echo esc_html( project_child_fcc_price( $combo['originalPrice'] ) ); ?></span>
			<?php endif; ?>
		</div>
		<div class="product-actions">
			<a href="#danh-sach-combo" class="btn-card-view">Chi tiết</a>
			<button class="btn-card-buy" data-open-order data-product-select="<?php echo esc_attr( $order_select ); ?>">Đặt mua</button>
		</div>
	</div>
</div>
