<?php
/**
 * Bootstrap the project's Kadence child theme.
 *
 * @package Project_Child
 */

defined( 'ABSPATH' ) || exit;

require_once get_stylesheet_directory() . '/inc/sample-page.php';

/**
 * Load project styles after Kadence's existing global stylesheet.
 *
 * Kadence enqueues its own assets; do not enqueue the parent style.css again.
 *
 * @return void
 */
function project_child_enqueue_styles() {
	$relative_path = '/assets/css/main.css';
	$version       = (string) filemtime( get_stylesheet_directory() . $relative_path );

	wp_enqueue_style(
		'project-child-main',
		get_stylesheet_directory_uri() . $relative_path,
		array( 'kadence-global' ),
		$version
	);
}
add_action( 'wp_enqueue_scripts', 'project_child_enqueue_styles', 20 );
