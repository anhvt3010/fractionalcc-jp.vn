<?php
/** @package Project_Child */
defined( 'ABSPATH' ) || exit;
$item = $args['item'];
?>
<div class="duo-spotlight-card" data-featured-id="<?php echo esc_attr( $item['id'] ); ?>">
	<div class="duo-card-main-split">
		<div class="duo-card-visual">
			<?php if ( $item['image'] ) : ?><img src="<?php echo esc_url( $item['image'] ); ?>" alt="<?php echo esc_attr( $item['alt'] ); ?>" loading="lazy"><?php endif; ?>
		</div>
		<div class="duo-card-primary-info">
			<h3 class="duo-title heading-serif"><?php echo esc_html( $item['spotlightTitle'] ); ?></h3>
			<?php if ( $item['spotlightRating'] ) : ?><div class="card-rating-row" style="margin-bottom: 8px;"><span class="card-rating-stars" aria-hidden="true">★★★★★</span><span><?php echo esc_html( $item['spotlightRating'] ); ?></span></div><?php endif; ?>
			<p class="duo-subtitle"><?php echo esc_html( $item['spotlightDescription'] ); ?></p>
		</div>
	</div>
	<div class="duo-card-details-row">
		<?php if ( $item['spotlightSpecs'] ) : ?><div class="duo-ingredients-header"><div class="duo-spec-pill"><?php project_child_fcc_spotlight_line( $item['spotlightSpecs'] ); ?></div></div><?php endif; ?>
		<?php if ( $item['spotlightHighlights'] ) : ?>
		<ul class="duo-ingredients-list">
			<?php foreach ( project_core_fcc_lines( $item['spotlightHighlights'] ) as $line ) : ?><li><?php project_child_fcc_spotlight_line( $line ); ?></li><?php endforeach; ?>
		</ul>
		<?php endif; ?>
		<?php if ( $item['spotlightGift'] ) : ?>
		<div class="combo-gift-box" style="margin-bottom: 12px; padding: 10px 14px; font-size: 13px;">
			<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px; margin-right: 4px;" aria-hidden="true"><path d="M20 12v10H4V12"></path><path d="M2 7h20v5H2z"></path><path d="M12 22V7"></path><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path></svg>
			<strong>QUÀ TẶNG:</strong> <?php echo esc_html( $item['spotlightGift'] ); ?>
		</div>
		<?php endif; ?>
		<div class="duo-card-footer">
			<div class="combo-price-wrap" style="border: none; padding: 0; margin: 0;"><div class="combo-sale-price-row">
				<span class="combo-sale-price<?php echo null === $item['salePrice'] ? ' duo-contact-price' : ''; ?>"<?php if ( null !== $item['salePrice'] ) : ?> style="font-size: 22px;"<?php endif; ?>><?php echo esc_html( project_child_fcc_price( $item['salePrice'] ) ); ?></span>
				<?php if ( null !== $item['salePrice'] && $item['originalPrice'] > $item['salePrice'] ) : ?><span class="combo-original-price"><?php echo esc_html( project_child_fcc_price( $item['originalPrice'] ) ); ?></span><?php endif; ?>
			</div></div>
			<div style="display: flex; gap: 8px; flex-wrap: wrap;">
				<a href="<?php echo esc_url( $item['url'] ); ?>" class="btn btn-outline" style="padding: 10px 18px; font-size: 13px;"><?php echo esc_html( $item['spotlightDetailLabel'] ); ?></a>
				<button class="btn btn-primary" data-open-order data-product-select="<?php echo esc_attr( $item['order_code'] ); ?>" style="padding: 10px 22px; font-size: 13px;"><?php echo esc_html( $item['spotlightBuyLabel'] ); ?></button>
			</div>
		</div>
	</div>
</div>
