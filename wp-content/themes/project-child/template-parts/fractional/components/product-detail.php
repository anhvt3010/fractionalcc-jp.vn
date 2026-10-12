<?php
/** @package Project_Child */
defined( 'ABSPATH' ) || exit;
$product = $args['product'];
$fragment = $args['fragment'];
?>
<?php if ( 'gallery' === $fragment ) : ?>
	<?php foreach ( $product['gallery'] as $index => $image ) : ?>
	<button type="button" class="pd-thumb-btn <?php echo 0 === $index ? 'active' : ''; ?>" data-detail-image="<?php echo esc_url( $image ); ?>" aria-label="<?php echo esc_attr( 'Xem ảnh ' . ( $index + 1 ) ); ?>"><img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $product['shortName'] . ' ảnh ' . ( $index + 1 ) ); ?>" loading="lazy"></button>
	<?php endforeach; ?>
<?php elseif ( 'usp' === $fragment || 'targets' === $fragment ) : ?>
	<?php foreach ( 'usp' === $fragment ? $product['uspBullets'] : $product['targetUsers'] as $text ) : ?>
	<li class="<?php echo 'usp' === $fragment ? 'pd-usp-item' : 'pd-target-item'; ?>"><span class="<?php echo 'usp' === $fragment ? 'pd-usp-icon' : 'pd-target-check'; ?>" aria-hidden="true"><?php project_child_fcc_check_icon(); ?></span><span<?php echo 'usp' === $fragment ? ' class="pd-usp-text"' : ''; ?>><?php echo esc_html( project_child_fcc_clean_bullet( $text ) ); ?></span></li>
	<?php endforeach; ?>
<?php elseif ( 'actives' === $fragment ) : ?>
	<?php foreach ( $product['actives'] as $active ) : ?>
	<div class="pd-active-card"><span class="pd-active-tag"><?php echo esc_html( $active['tag'] ); ?></span><h3 class="pd-active-title"><?php echo esc_html( $active['name'] ); ?></h3><p class="pd-active-desc"><?php echo esc_html( $active['desc'] ); ?></p></div>
	<?php endforeach; ?>
<?php elseif ( 'steps' === $fragment ) : ?>
	<?php foreach ( $product['howToUse'] as $index => $step ) : ?>
	<div class="pd-step-row"><div class="pd-step-num"><?php echo esc_html( $index + 1 ); ?></div><p class="pd-step-text"><?php echo esc_html( preg_replace( '/^Bước\s*\d+[:.]\s*/iu', '', $step ) ); ?></p></div>
	<?php endforeach; ?>
<?php elseif ( 'combos' === $fragment ) : ?>
	<?php $related_count = 0; foreach ( project_core_fcc_combos() as $combo ) : ?>
		<?php if ( ! in_array( $product['id'], $combo['productIds'], true ) ) { continue; } ?>
		<?php ++$related_count; ?>
		<div class="combo-card <?php echo $combo['isBestSeller'] ? 'is-featured' : ''; ?>"><?php if ( $combo['isBestSeller'] ) : ?><div class="combo-top-badge">BÁN CHẠY NHẤT</div><?php endif; ?><div class="combo-card-header"><h4 class="combo-name"><?php echo esc_html( $combo['name'] ); ?></h4><p class="combo-ideal"><?php echo esc_html( $combo['idealFor'] ); ?></p></div><div class="combo-card-body"><div class="combo-gift-box"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:4px;display:inline-block;vertical-align:text-top;"><path d="M20 12v10H4V12"></path><path d="M2 7h20v5H2z"></path><path d="M12 22V7"></path><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path></svg>Quà tặng: <?php echo esc_html( $combo['gift'] ); ?></div><div class="combo-price-wrap"><?php if ( $combo['originalPrice'] ) : ?><div class="combo-original-price"><?php echo esc_html( project_child_fcc_price( $combo['originalPrice'] ) ); ?></div><?php endif; ?><div class="combo-sale-price-row"><span class="combo-sale-price"><?php echo esc_html( project_child_fcc_price( $combo['salePrice'] ) ); ?></span></div></div><button class="btn btn-primary btn-full" data-open-order data-product-select="combo_<?php echo esc_attr( $combo['id'] ); ?>">Đặt Combo Ưu Đãi</button></div></div>
	<?php endforeach; ?>
	<?php if ( ! $related_count ) : ?><p style="color: var(--color-text-muted);">Khám phá thêm các combo ưu đãi tại trang danh mục.</p><?php endif; ?>
<?php endif; ?>
