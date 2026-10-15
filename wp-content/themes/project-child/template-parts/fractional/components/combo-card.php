<?php
/**
 * Combo card component for the product catalog grid.
 * Features Mega Spotlight layout for Combo C-CX4 on homepage (#danh-muc).
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
$is_home       = ! empty( $args['home'] );

if ( $is_home ) :
	$lip_gift_img = project_child_fcc_asset( 'assets/images/thumbnail/pdrn-shot-lip-essence-01.webp' );
?>
<div class="product-card product-card-combo product-card-combo-spotlight" data-id="combo-<?php echo esc_attr( $combo['id'] ); ?>" data-type="combo" data-concerns="<?php echo esc_attr( $concerns_attr ); ?>" data-search="<?php echo esc_attr( $search_attr ); ?>">
	<div class="combo-spotlight-topline"></div>
	<div class="combo-spotlight-grid">
		<!-- CỘT 1: Media Showcase & Quà Tặng Trực Quan -->
		<div class="combo-spotlight-media-wrap">
			<span class="combo-spotlight-badge-corner">⭐ BÁN CHẠY NHẤT • GIẢI PHÁP SỐ 1</span>
			<a class="combo-spotlight-thumb" href="<?php echo esc_url( $detail_url ); ?>" aria-label="<?php echo esc_attr( $combo['name'] ); ?>">
				<?php echo project_child_fcc_picture_html( $combo['image'], $combo['name'], 'combo-spotlight-img', array( 'loading' => 'lazy', 'sizes' => '(min-width: 992px) 380px, 100vw' ) ); ?>
			</a>
			<?php if ( $lip_gift_img ) : ?>
			<div class="combo-spotlight-gift-floating">
				<img src="<?php echo esc_url( $lip_gift_img ); ?>" alt="Quà tặng Serum Dưỡng Môi PDRN 445.000đ" class="combo-gift-thumb" width="44" height="44" loading="lazy">
				<div class="combo-gift-floating-info">
					<span class="gift-floating-tag">QUÀ TẶNG KÈM THEO COMBO</span>
					<span class="gift-floating-title">Serum Môi PDRN trị giá <strong>445.000đ</strong></span>
				</div>
			</div>
			<?php endif; ?>
		</div>

		<!-- CỘT 2: Thông Tin Khoa Học & Bảng Giá Trị Cộng Hưởng (Synergy Formula) -->
		<div class="combo-spotlight-info-wrap">
			<div class="combo-spotlight-meta-row">
				<span class="combo-spotlight-cat-tag">PHÁC ĐỒ ĐIỀU TRỊ CHUYÊN SÂU • MÃ <?php echo esc_html( $combo['code'] ); ?></span>
				<div class="card-rating-row">
					<span class="card-rating-stars">★★★★★</span>
					<span class="card-rating-score">5.0</span>
					<span class="card-rating-count">(1.200+ đánh giá)</span>
				</div>
			</div>

			<h3 class="combo-spotlight-main-title">
				<a href="<?php echo esc_url( $detail_url ); ?>"><?php echo esc_html( $combo['name'] ); ?></a>
			</h3>

			<p class="combo-spotlight-desc-text">
				Liệu pháp vi kim kép chuẩn Salon Nhật Bản: <strong>Needle Serum C</strong> (150.000 vi kim Vitamin C làm sáng mờ thâm) kết hợp <strong>Needle Serum CX4</strong> (mật độ vi kim X4 tái tạo nền da mới).
			</p>

			<!-- Bảng Giá Trị Cộng Hưởng (Synergy Value Stacking) -->
			<div class="combo-synergy-box">
				<div class="synergy-box-head">Trọn bộ phác đồ &amp; Quà tặng đặc quyền:</div>
				<div class="synergy-items-list">
					<div class="synergy-list-item">
						<span class="synergy-bullet">✓</span>
						<span class="synergy-name">01 Needle Serum C (150.000 vi kim)</span>
						<span class="synergy-orig-price">860.000đ</span>
					</div>
					<div class="synergy-list-item">
						<span class="synergy-bullet">✓</span>
						<span class="synergy-name">01 Needle Serum CX4 (Mật độ vi kim X4)</span>
						<span class="synergy-orig-price">860.000đ</span>
					</div>
					<div class="synergy-list-item synergy-gift-item">
						<span class="synergy-bullet gift-bullet">🎁</span>
						<span class="synergy-name"><strong>Quà tặng:</strong> 01 Serum Dưỡng Môi PDRN Lip Shot</span>
						<span class="synergy-gift-value">TẶNG MIỄN PHÍ 445.000đ</span>
					</div>
				</div>
				<div class="synergy-total-summary">
					<span>Tổng giá trị nhận được: <del>2.165.000đ</del></span>
				</div>
			</div>
		</div>

		<!-- CỘT 3: Hộp Chốt Đơn Độc Quyền (Action Box) -->
		<div class="combo-spotlight-action-wrap">
			<div class="spotlight-save-badge">TIẾT KIỆM 482.500Đ (-28%)</div>
			
			<div class="spotlight-price-block">
				<span class="spotlight-price-label">Giá ưu đãi Combo:</span>
				<span class="spotlight-price-final"><?php echo esc_html( project_child_fcc_price( $combo['salePrice'] ) ); ?></span>
				<div class="spotlight-price-sub">
					<span class="spotlight-price-del">Giá gốc: <del>1.720.000đ</del></span>
				</div>
			</div>

			<div class="spotlight-cta-block">
				<button class="btn btn-spotlight-order" data-open-order data-product-select="<?php echo esc_attr( $order_select ); ?>">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
					<span>ĐẶT MUA COMBO C–CX4</span>
				</button>
				<a href="<?php echo esc_url( $detail_url ); ?>" class="btn-spotlight-learn-more">
					<span>Xem chi tiết liệu trình</span>
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
				</a>
			</div>

			<div class="spotlight-guarantee-row">
				<div class="spotlight-guarantee-pill"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg><span>Freeship toàn quốc</span></div>
				<div class="spotlight-guarantee-pill"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg><span>Kiểm tra khi nhận hàng</span></div>
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
