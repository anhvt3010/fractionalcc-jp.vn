<?php
/** Reusable dynamic components, rendered before JavaScript. @package Project_Child */
defined( 'ABSPATH' ) || exit;

function project_child_fcc_fragment( $name ) {
	if ( 'catalog' === $name ) {
		if ( function_exists( 'project_core_fcc_combos' ) ) {
			foreach ( project_core_fcc_combos() as $combo ) {
				get_template_part( 'template-parts/fractional/components/combo-card', null, array( 'combo' => $combo ) );
			}
		}
		foreach ( project_core_fcc_posts( 'fcc_product' ) as $post ) {
			get_template_part( 'template-parts/fractional/components/product-card', null, array( 'product' => project_core_fcc_product( $post->ID, false ) ) );
		}
		return;
	}
	$product = project_core_fcc_product( get_queried_object_id(), in_array( $name, array( 'gallery', 'actives' ), true ) );
	if ( ! $product ) { return; }
	get_template_part( 'template-parts/fractional/components/product-detail', null, array( 'fragment' => $name, 'product' => $product ) );
}

function project_child_fcc_check_icon() {
	echo '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6L9 17l-5-5"></path></svg>';
}

function project_child_fcc_clean_bullet( $value ) {
	return trim( preg_replace( '/^[\s✅✓✔☑•*–—\-]+/u', '', (string) $value ) );
}
