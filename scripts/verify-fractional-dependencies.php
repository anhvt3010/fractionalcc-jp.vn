<?php
/** Read-only dependency failure checks on a clean marked fixture. */
if ( 'cli' !== PHP_SAPI ) { exit( 1 ); }
$root = getenv( 'PROJECT_WORDPRESS_ROOT' );
if ( ! $root || realpath( $root ) === realpath( dirname( __DIR__ ) ) ) { throw new RuntimeException( 'Isolated fixture required.' ); }
if ( 'acf' === ( $argv[1] ?? '' ) ) {
	require_once $root . '/wp-includes/plugin.php';
	add_filter( 'option_active_plugins', static function ( $plugins ) { return array_values( array_diff( $plugins, array( 'advanced-custom-fields/acf.php' ) ) ); } );
}
require $root . '/wp-load.php';
if ( ! get_option( 'project_core_fcc_test_install' ) || get_option( 'project_core_fcc_seed_version' ) ) { throw new RuntimeException( 'Clean marked fixture required.' ); }
$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) ); wp_set_current_user( $admins[0] );
if ( 'child' === ( $argv[1] ?? '' ) ) { add_filter( 'stylesheet', static function () { return 'kadence'; } ); }
$before = array( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts}" ), $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->postmeta}" ) );
$result = project_core_fcc_initialize();
$after = array( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts}" ), $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->postmeta}" ) );
if ( ! is_wp_error( $result ) || 'fcc_dependencies' !== $result->get_error_code() || $before !== $after || get_option( 'project_core_fcc_seed_version' ) ) { throw new RuntimeException( 'Missing dependency did not block initialization safely.' ); }
echo 'PASS: Missing ', $argv[1], ' blocks initialization; posts, metadata and seed unchanged.', PHP_EOL;
