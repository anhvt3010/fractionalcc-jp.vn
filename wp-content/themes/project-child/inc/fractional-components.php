<?php
/** Reusable dynamic components, rendered before JavaScript. @package Project_Child */
defined( 'ABSPATH' ) || exit;

function project_child_fcc_fragment( $name ) {
	if ( 'home_catalog' === $name ) {
		$combo = project_core_fcc_featured_combo();
		if ( $combo ) { get_template_part( 'template-parts/fractional/components/combo-card', null, array( 'combo' => $combo, 'home' => true ) ); }
		foreach ( project_core_fcc_home_catalog_posts() as $post ) {
			get_template_part( 'template-parts/fractional/components/product-card', null, array( 'product' => project_core_fcc_product( $post->ID, false ) ) );
		}
		return;
	}
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
	$product = project_child_fcc_detail_data( in_array( $name, array( 'gallery', 'actives' ), true ) );
	if ( ! $product ) { return; }
	get_template_part( 'template-parts/fractional/components/product-detail', null, array( 'fragment' => $name, 'product' => $product ) );
}

function project_child_fcc_clean_bullet( $value ) {
	return trim( preg_replace( '/^[\s✅✓✔☑•*–—\-]+/u', '', (string) $value ) );
}
