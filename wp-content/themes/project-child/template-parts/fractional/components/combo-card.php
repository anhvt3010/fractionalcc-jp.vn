<?php
/**
 * Combo card component for the product catalog grid.
 * Uses the selected featured combo in the homepage spotlight (#danh-muc).
 *
 * @package Project_Child
 */

defined( 'ABSPATH' ) || exit;

$combo = isset( $args['combo'] ) ? $args['combo'] : null;
if ( ! $combo ) {
	return;
}
$contents = project_core_fcc_combo_contents( $combo );

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
$concerns = array();
foreach ( $contents['products'] as $included_product ) {
	$search_parts[] = $included_product['name'];
	$search_parts[] = $included_product['shortName'];
	$concerns = array_merge( $concerns, $included_product['skinConcerns'] );
}
$search_attr = mb_strtolower( implode( ' ', array_filter( $search_parts ) ) );
$concerns_attr = implode( ' ', array_unique( $concerns ) );
$order_select  = 'combo_' . $combo['id'];
$badge_text    = ! empty( $combo['badge'] ) ? $combo['badge'] : 'COMBO TIẾT KIỆM';
$detail_url    = ! empty( $args['home'] ) ? project_core_fcc_url( 'catalog' ) . '#danh-sach-combo' : '#danh-sach-combo';
$rating        = ! empty( $args['home'] ) ? (string) get_post_meta( $combo['id'], 'fcc_spotlightRating', true ) : project_child_fcc_rating_text( $combo );
if ( '' === $rating ) { $rating = project_child_fcc_rating_text( $combo ); }
$has_hover     = ! empty( $combo['hoverImage'] );
$is_home       = ! empty( $args['home'] );

if ( $is_home ) :
	$has_savings = null !== $combo['salePrice'] && $combo['originalPrice'] > $combo['salePrice'];
	$description = (string) get_post_meta( $combo['id'], 'fcc_spotlightDescription', true );
	if ( '' === $description ) { $description = $combo['idealFor']; }
?>
<div class="product-card product-card-combo product-card-combo-spotlight" data-id="combo-<?php echo esc_attr( $combo['id'] ); ?>" data-type="combo" data-concerns="<?php echo esc_attr( $concerns_attr ); ?>" data-search="<?php echo esc_attr( $search_attr ); ?>">
	<div class="combo-spotlight-topline"></div>
	<div class="combo-spotlight-grid">
		<!-- CỘT 1: Ảnh combo -->
		<div class="combo-spotlight-media-wrap">
			<span class="combo-spotlight-badge-corner"><?php echo esc_html( $badge_text ); ?></span>
			<a class="combo-spotlight-thumb" href="<?php echo esc_url( $detail_url ); ?>" aria-label="<?php echo esc_attr( $combo['name'] ); ?>">
				<?php echo project_child_fcc_picture_html( $combo['image'], $combo['name'], 'combo-spotlight-img', array( 'loading' => 'lazy', 'sizes' => '(min-width: 992px) 380px, 100vw' ) ); ?>
			</a>
		</div>

		<!-- CỘT 2: Thông Tin Khoa Học & Bảng Giá Trị Cộng Hưởng (Synergy Formula) -->
		<div class="combo-spotlight-info-wrap">
			<?php if ( '' !== $rating ) : ?>
			<div class="combo-spotlight-meta-row">
				<div class="card-rating-row"><span class="card-rating-stars">★★★★★</span><span><?php echo esc_html( $rating ); ?></span></div>
			</div>
			<?php endif; ?>

			<h3 class="combo-spotlight-main-title">
				<a href="<?php echo esc_url( $detail_url ); ?>"><?php echo esc_html( $combo['name'] ); ?></a>
			</h3>

			<p class="combo-spotlight-desc-text">
				<?php echo esc_html( $description ); ?>
			</p>

			<!-- Bảng Giá Trị Cộng Hưởng (Synergy Value Stacking) -->
			<?php if ( $contents['products'] || $combo['gift'] ) : ?>
			<div class="combo-synergy-box">
				<div class="synergy-box-head"><?php echo project_child_fcc_text( 'fcc_ui_combo_contents_heading', 'shared' ); ?></div>
				<div class="synergy-items-list">
					<?php foreach ( $contents['products'] as $included_product ) : ?>
					<div class="synergy-list-item" data-combo-product-id="<?php echo esc_attr( $included_product['id'] ); ?>">
						<span class="synergy-bullet">✓</span>
						<span class="synergy-name"><?php echo esc_html( $included_product['name'] ); ?></span>
						<span class="synergy-orig-price"><?php echo esc_html( project_child_fcc_price( $included_product['salePrice'] ) ); ?></span>
					</div>
					<?php endforeach; ?>
					<?php if ( $combo['gift'] ) : ?>
					<div class="synergy-list-item synergy-gift-item">
						<span class="synergy-bullet gift-bullet">🎁</span>
						<span class="synergy-name"><strong>Quà tặng:</strong> <?php echo esc_html( $combo['gift'] ); ?></span>
					</div>
					<?php endif; ?>
				</div>
			</div>
			<?php endif; ?>
		</div>

		<!-- CỘT 3: Hộp Chốt Đơn Độc Quyền (Action Box) -->
		<div class="combo-spotlight-action-wrap">
			<?php if ( $has_savings ) : ?><div class="spotlight-save-badge">TIẾT KIỆM <?php echo esc_html( project_child_fcc_price( $combo['saveAmount'] ) ); ?> (-<?php echo esc_html( (string) round( 100 * $combo['saveAmount'] / $combo['originalPrice'] ) ); ?>%)</div><?php endif; ?>
			
			<div class="spotlight-price-block">
				<span class="spotlight-price-label">Giá ưu đãi Combo:</span>
				<span class="spotlight-price-final"><?php echo esc_html( project_child_fcc_price( $combo['salePrice'] ) ); ?></span>
				<?php if ( $has_savings ) : ?><div class="spotlight-price-sub">
					<span class="spotlight-price-del">Giá gốc: <del><?php echo esc_html( project_child_fcc_price( $combo['originalPrice'] ) ); ?></del></span>
				</div>
				<?php endif; ?>
			</div>

			<div class="spotlight-cta-block">
				<button class="btn btn-spotlight-order" data-open-order data-product-select="<?php echo esc_attr( $order_select ); ?>">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
					<span>ĐẶT MUA COMBO <?php echo esc_html( $combo['code'] ); ?></span>
				</button>
			</div>

		</div>
	</div>
</div>
<?php else : ?>
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
<?php endif; ?>
