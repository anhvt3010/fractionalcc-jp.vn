<?php
/** @package Project_Child */
defined( 'ABSPATH' ) || exit;
?>
<section id="gioi-thieu" class="project-sample__section">
	<div class="project-sample__container project-sample__narrow">
		<?php if ( $args['about_title'] ) : ?><h2><?php echo esc_html( $args['about_title'] ); ?></h2><?php endif; ?>
		<?php if ( $args['about_description'] ) : ?><p class="project-sample__lead"><?php echo nl2br( esc_html( $args['about_description'] ) ); ?></p><?php endif; ?>
	</div>
</section>
