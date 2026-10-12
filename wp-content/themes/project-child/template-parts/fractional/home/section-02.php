<?php
/** Selected products use the approved duo presentation. @package Project_Child */
defined( 'ABSPATH' ) || exit;
$featured = project_core_fcc_featured_posts();
if ( ! $featured ) { return; }
$combos = array();
if ( in_array( 'fcc_combo', wp_list_pluck( $featured, 'post_type' ), true ) ) {
	foreach ( project_core_fcc_combos() as $combo ) { $combos[ $combo['id'] ] = $combo; }
}
?>
<section class="section section-duo-hero" id="san-pham-chu-luc">
	<div class="container">
		<div class="section-header">
			<span class="section-tag"><?php echo project_child_fcc_text( 'fcc_ui_1bde33c83730c6', 'home' ); ?></span>
			<h2 class="section-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_cc3c2901a4cef8', 'home' ); ?></h2>
			<p class="section-desc"><?php echo project_child_fcc_text( 'fcc_ui_a12e257cd86c9f', 'home' ); ?></p>
		</div>
		<div class="duo-spotlight-grid">
			<?php foreach ( $featured as $post ) :
				$item = project_child_fcc_spotlight_data( $post, $combos );
				if ( $item ) { get_template_part( 'template-parts/fractional/components/spotlight-card', null, array( 'item' => $item ) ); }
			endforeach; ?>
		</div>
	</div>
</section>
