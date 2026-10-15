<?php
/** Shared optional labels for product cards and the featured product image.
 * @package Project_Child
 */
defined( 'ABSPATH' ) || exit;
$tags = project_core_fcc_normalize_card_tags( $args['tags'] ?? array() );
if ( ! $tags ) { return; }
$labels = project_core_fcc_product_card_tag_choices();
?>
<span class="fcc-product-tags">
	<?php foreach ( $tags as $tag ) : ?>
		<span class="fcc-product-tag fcc-product-tag--<?php echo esc_attr( $tag ); ?>"><?php echo esc_html( $labels[ $tag ] ); ?></span>
	<?php endforeach; ?>
</span>
