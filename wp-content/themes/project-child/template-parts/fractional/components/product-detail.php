<?php
/** @package Project_Child */
defined( 'ABSPATH' ) || exit;
$product = $args['product'];
$fragment = $args['fragment'];
?>
<?php if ( 'gallery' === $fragment ) : ?>
	<?php foreach ( $product['gallery'] as $index => $image ) : ?>
	<button type="button" class="pd-thumb-btn <?php echo $image === $product['image'] ? 'active' : ''; ?>" data-detail-image="<?php echo esc_url( $image ); ?>" data-detail-alt="<?php echo esc_attr( $product['galleryAlts'][ $image ] ); ?>" aria-pressed="<?php echo $image === $product['image'] ? 'true' : 'false'; ?>" aria-label="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_gallery_view', 'shared' ) . ' ' . ( $index + 1 ) ); ?>"><?php echo project_child_fcc_picture_html( $product['galleryThumbs'][ $image ] ?? $image, $product['galleryAlts'][ $image ], '', array( 'loading' => 'lazy', 'sizes' => '90px' ) ); ?></button>
	<?php endforeach; ?>
<?php elseif ( 'actives' === $fragment ) : ?>
	<?php foreach ( $product['actives'] as $active ) : ?>
	<div class="pd-active-card"><span class="pd-active-tag"><?php echo esc_html( $active['tag'] ); ?></span><h3 class="pd-active-title"><?php echo esc_html( $active['name'] ); ?></h3><p class="pd-active-desc"><?php echo esc_html( $active['desc'] ); ?></p></div>
	<?php endforeach; ?>
<?php elseif ( 'combos' === $fragment ) : ?>
	<?php $related_count = 0; foreach ( project_core_fcc_combos() as $combo ) : ?>
		<?php if ( ! in_array( $product['id'], $combo['productIds'], true ) ) { continue; } ?>
		<?php ++$related_count; ?>
		<div class="combo-card <?php echo $combo['isBestSeller'] ? 'is-featured' : ''; ?>"><?php if ( $combo['isBestSeller'] ) : ?><div class="combo-top-badge"><?php echo project_child_fcc_text( 'fcc_ui_combo_bestseller', 'shared' ); ?></div><?php endif; ?><div class="combo-card-header"><h4 class="combo-name"><a href="<?php echo esc_url( $combo['url'] ); ?>"><?php echo esc_html( $combo['name'] ); ?></a></h4><p class="combo-ideal"><?php echo esc_html( $combo['idealFor'] ); ?></p></div><div class="combo-card-body"><div class="combo-gift-box"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:4px;display:inline-block;vertical-align:text-top;"><path d="M20 12v10H4V12"></path><path d="M2 7h20v5H2z"></path><path d="M12 22V7"></path><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path></svg><?php echo project_child_fcc_text( 'fcc_ui_combo_gift_prefix', 'shared' ); ?> <?php echo esc_html( $combo['gift'] ); ?></div><div class="combo-price-wrap"><?php if ( $combo['originalPrice'] ) : ?><div class="combo-original-price"><?php echo esc_html( project_child_fcc_price( $combo['originalPrice'] ) ); ?></div><?php endif; ?><div class="combo-sale-price-row"><span class="combo-sale-price"><?php echo esc_html( project_child_fcc_price( $combo['salePrice'] ) ); ?></span></div></div><button class="btn btn-primary btn-full" data-open-order data-product-select="combo_<?php echo esc_attr( $combo['id'] ); ?>"><?php echo project_child_fcc_text( 'fcc_ui_combo_buy', 'shared' ); ?></button></div></div>
	<?php endforeach; ?>
<?php endif; ?>
