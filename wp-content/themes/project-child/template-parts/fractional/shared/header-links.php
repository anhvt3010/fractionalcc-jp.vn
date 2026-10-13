<?php
/** Shared links for desktop and mobile navigation. @package Project_Child */
defined( 'ABSPATH' ) || exit;
foreach ( $args['items'] as $item ) :
	$current = $item['current'];
	?>
	<li><a href="<?php echo esc_url( $item['url'] ); ?>" class="nav-link<?php echo $current ? ' active' : ''; ?>"<?php if ( $current ) : ?> aria-current="<?php echo esc_attr( $current ); ?>"<?php endif; ?>><?php echo project_child_fcc_text( $item['field'], 'shared', false ); ?></a></li>
<?php endforeach; ?>
