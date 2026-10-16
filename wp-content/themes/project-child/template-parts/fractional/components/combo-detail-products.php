<?php
/** Published constituent products retain their own specifications and dossiers. @package Project_Child */
defined( 'ABSPATH' ) || exit;
$contents = project_core_fcc_combo_contents( $args['combo'] );
if ( ! $contents['products'] ) { return; }
?>
<section class="pd-combos-section" aria-labelledby="combo-products-title">
	<div class="section-header">
		<h2 class="section-title heading-serif" id="combo-products-title">Sản phẩm trong combo</h2>
	</div>
	<div class="brief-product-grid">
		<?php foreach ( $contents['products'] as $product ) : ?>
			<?php get_template_part( 'template-parts/fractional/components/product-card', null, array( 'product' => $product ) ); ?>
		<?php endforeach; ?>
	</div>
</section>
