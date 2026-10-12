<?php
/** @package Project_Child */
defined( 'ABSPATH' ) || exit;
$email = sanitize_email( $args['contact_email'] );
?>
<section id="lien-he" class="project-sample__contact">
	<div class="project-sample__container">
		<div class="project-sample__contact-panel">
			<?php if ( $args['contact_title'] ) : ?><h2><?php echo esc_html( $args['contact_title'] ); ?></h2><?php endif; ?>
			<?php if ( $args['contact_description'] ) : ?><p class="project-sample__lead"><?php echo nl2br( esc_html( $args['contact_description'] ) ); ?></p><?php endif; ?>
			<?php if ( is_email( $email ) && $args['contact_button_text'] ) : ?><a class="project-sample__button" href="<?php echo esc_url( 'mailto:' . $email ); ?>"><?php echo esc_html( $args['contact_button_text'] ); ?></a><?php endif; ?>
		</div>
	</div>
</section>
