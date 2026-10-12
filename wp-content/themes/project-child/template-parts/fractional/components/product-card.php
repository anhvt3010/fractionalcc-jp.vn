<?php
/** @package Project_Child */
defined( 'ABSPATH' ) || exit;
$product = $args['product'];
?>
<div class="product-card" data-id="<?php echo esc_attr( $product['id'] ); ?>" data-type="<?php echo esc_attr( $product['categoryType'] ); ?>" data-concerns="<?php echo esc_attr( implode( ' ', $product['skinConcerns'] ) ); ?>" data-search="<?php echo esc_attr( mb_strtolower( $product['name'] . ' ' . $product['shortName'] . ' ' . $product['tagline'] ) ); ?>">
	<div class="product-thumb">
		<img src="<?php echo esc_url( $product['image'] ); ?>" alt="<?php echo esc_attr( $product['name'] ); ?>" loading="lazy">
		<span class="product-cbmp-tag">Số CBMP: <?php echo esc_html( $product['cbmp'] ); ?></span>
	</div>
	<div class="product-info">
		<span class="product-category-name"><?php echo esc_html( $product['origin'] . ' • ' . $product['spiculeCount'] ); ?></span>
		<?php if ( null !== $product['rating'] ) : ?>
		<div class="card-rating-row"><span class="card-rating-stars">★★★★★</span><span><?php echo esc_html( $product['rating'] . ' (' . $product['reviewCount'] . '+ đánh giá)' ); ?></span></div>
		<?php endif; ?>
		<h3 class="product-title" title="<?php echo esc_attr( $product['name'] ); ?>"><a href="<?php echo esc_url( $product['url'] ); ?>"><?php echo esc_html( $product['shortName'] ); ?></a></h3>
		<p style="font-size: 12.5px; color: var(--color-text-body); margin-bottom: 12px; line-height: 1.45;"><?php echo esc_html( $product['tagline'] ); ?></p>
		<div class="product-price-row"><span class="product-price-current"><?php echo esc_html( project_child_fcc_price( $product['salePrice'] ) ); ?></span><?php if ( $product['originalPrice'] ) : ?><span class="product-price-original"><?php echo esc_html( project_child_fcc_price( $product['originalPrice'] ) ); ?></span><?php endif; ?></div>
		<div class="product-actions"><a href="<?php echo esc_url( $product['url'] ); ?>" class="btn-card-view">Chi tiết</a><button class="btn-card-buy" data-open-order data-product-select="prod_<?php echo esc_attr( $product['id'] ); ?>">Đặt mua</button></div>
	</div>
</div>
