<?php
/** @package Project_Child */
defined( 'ABSPATH' ) || exit;
$image_id = absint( $args['hero_image'] );
$image = $image_id ? wp_get_attachment_image( $image_id, 'large', false, array( 'loading' => 'eager' ) ) : '';
$button_url = esc_url( $args['hero_button_url'] );
?>
<section class="project-sample__hero" aria-labelledby="project-hero-title">
	<div class="project-sample__container project-sample__hero-layout<?php echo $image ? ' project-sample__hero-layout--image' : ''; ?>">
		<div>
			<?php if ( $args['hero_eyebrow'] ) : ?><p class="project-sample__eyebrow"><?php echo esc_html( $args['hero_eyebrow'] ); ?></p><?php endif; ?>
			<h1 id="project-hero-title"><?php echo esc_html( $args['hero_title'] ?: get_the_title() ); ?></h1>
			<?php if ( $args['hero_description'] ) : ?><p class="project-sample__lead"><?php echo nl2br( esc_html( $args['hero_description'] ) ); ?></p><?php endif; ?>
			<?php if ( $args['hero_button_text'] && $button_url ) : ?><a class="project-sample__button" href="<?php echo $button_url; // Already escaped above. ?>"><?php echo esc_html( $args['hero_button_text'] ); ?></a><?php endif; ?>
		</div>
		<?php if ( $image ) : ?>
			<div class="project-sample__hero-image"><?php echo $image; // Image markup is generated and escaped by WordPress. ?></div>
		<?php endif; ?>
	</div>
</section>
