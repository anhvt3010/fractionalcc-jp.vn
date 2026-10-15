<?php
/** @package Project_Child */
defined( 'ABSPATH' ) || exit;
$product   = $args['product'];
$has_hover = ! empty( $product['hoverImage'] );
?>
<div class="product-card" data-id="<?php echo esc_attr( $product['id'] ); ?>" data-type="<?php echo esc_attr( $product['categoryType'] ); ?>" data-concerns="<?php echo esc_attr( implode( ' ', $product['skinConcerns'] ) ); ?>" data-search="<?php echo esc_attr( mb_strtolower( $product['name'] . ' ' . $product['shortName'] . ' ' . $product['tagline'] ) ); ?>">
	<?php get_template_part( 'template-parts/fractional/components/product-tags', null, array( 'tags' => $product['cardTags'] ?? array() ) ); ?>
	<a class="product-thumb<?php echo $has_hover ? ' has-hover-image' : ''; ?>" href="<?php echo esc_url( $product['url'] ); ?>" aria-label="<?php echo esc_attr( $product['name'] ); ?>">
		<?php echo project_child_fcc_picture_html( $product['image'], $product['imageAlt'], 'product-thumb-primary', array( 'loading' => 'lazy', 'sizes' => '(min-width: 992px) 400px, 50vw' ) ); ?>
		<?php if ( $has_hover ) : ?>
			<?php echo project_child_fcc_picture_html( $product['hoverImage'], $product['hoverImageAlt'] ?: $product['imageAlt'], 'product-thumb-hover', array( 'loading' => 'lazy', 'sizes' => '(min-width: 992px) 400px, 50vw' ) ); ?>
		<?php endif; ?>
		<span class="product-cbmp-tag"><?php echo project_child_fcc_text( 'fcc_ui_m_d43decf568f2', 'shared' ); ?> <?php echo esc_html( $product['cbmp'] ); ?></span>
	</a>
	<div class="product-info">
		<span class="product-category-name"><?php echo esc_html( $product['spiculeCount'] ?: $product['origin'] ); ?></span>
		<?php if ( null !== $product['rating'] ) : ?>
		<div class="card-rating-row"><span class="card-rating-stars">★★★★★</span><span><?php echo esc_html( project_child_fcc_rating_text( $product ) ); ?></span></div>
		<?php endif; ?>
		<h3 class="product-title" title="<?php echo esc_attr( $product['name'] ); ?>"><a href="<?php echo esc_url( $product['url'] ); ?>"><?php echo esc_html( $product['name'] ); ?></a></h3>
		<p class="product-desc"><?php echo esc_html( $product['tagline'] ); ?></p>
		<div class="product-price-row"><span class="product-price-current"><?php echo esc_html( project_child_fcc_price( $product['salePrice'] ) ); ?></span><?php if ( $product['originalPrice'] ) : ?><span class="product-price-original"><?php echo esc_html( project_child_fcc_price( $product['originalPrice'] ) ); ?></span><?php endif; ?></div>
		<div class="product-actions"><a href="<?php echo esc_url( $product['url'] ); ?>" class="btn-card-view"><?php echo project_child_fcc_text( 'fcc_ui_m_33b5b579e52f', 'shared' ); ?></a><button class="btn-card-buy" data-open-order data-product-select="prod_<?php echo esc_attr( $product['id'] ); ?>"><?php echo project_child_fcc_text( 'fcc_ui_m_b2d24c22f0ff', 'shared' ); ?></button></div>
	</div>
</div>
