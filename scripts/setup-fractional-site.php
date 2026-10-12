<?php
/**
 * Initialize approved defaults on the explicitly selected WordPress installation.
 * CLI only. Existing content is preserved unless --update-catalog is explicit.
 * Run: php scripts/setup-fractional-site.php [--set-homepage] [--content-status=approved] [--update-catalog]
 */
if ( 'cli' !== PHP_SAPI ) { http_response_code( 403 ); exit; }
$root = getenv( 'PROJECT_WORDPRESS_ROOT' ) ?: dirname( __DIR__ );
require rtrim( $root, '/\\' ) . '/wp-load.php';
$options = getopt( '', array( 'set-homepage', 'content-status:', 'update-catalog' ) );
$status = isset( $options['content-status'] ) ? $options['content-status'] : 'local_sample';
if ( ! in_array( $status, array( 'local_sample', 'approved' ), true ) ) { fwrite( STDERR, "Invalid content status.\n" ); exit( 1 ); }
$users = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
if ( ! $users || ! function_exists( 'project_core_fcc_initialize' ) ) { fwrite( STDERR, "An administrator and Project Core are required.\n" ); exit( 1 ); }
wp_set_current_user( $users[0] );
$result = project_core_fcc_initialize( $status, array_key_exists( 'update-catalog', $options ) );
if ( is_wp_error( $result ) ) { fwrite( STDERR, $result->get_error_message() . "\n" ); exit( 1 ); }
if ( array_key_exists( 'set-homepage', $options ) ) {
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $result['home'] );
}
echo 'Initialized Fractional CC defaults at ' . home_url( '/' ) . PHP_EOL;
echo 'CMS: ' . admin_url( 'admin.php?page=project-website-content' ) . PHP_EOL;
