<?php
/** Bootstrap a new, isolated local release fixture. Never replaces an existing configuration/database. */
if ( 'cli' !== PHP_SAPI ) { exit( 1 ); }
$name = getenv( 'FCC_QA_NAME' );
if ( ! is_string( $name ) || ! preg_match( '/^fcc-release-qa-\d+(?:-upgrade)?$/', $name ) ) { throw new RuntimeException( 'Invalid fixture name.' ); }
$root = 'C:/xampp/htdocs/' . $name;
if ( 'configure' === ( $argv[1] ?? '' ) ) {
	require dirname( __DIR__ ) . '/wp-load.php';
	if ( ! is_dir( $root ) || file_exists( $root . '/wp-config.php' ) ) { throw new RuntimeException( 'New fixture directory required.' ); }
	$db = str_replace( '-', '_', $name );
	if ( false === $wpdb->query( "CREATE DATABASE `{$db}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci" ) ) { throw new RuntimeException( 'Could not create new fixture database.' ); }
	$settings = array( 'DB_NAME' => $db, 'DB_USER' => DB_USER, 'DB_PASSWORD' => DB_PASSWORD, 'DB_HOST' => DB_HOST, 'DB_CHARSET' => 'utf8mb4', 'DB_COLLATE' => '', 'WP_HOME' => 'http://localhost/' . $name, 'WP_SITEURL' => 'http://localhost/' . $name, 'WP_ENVIRONMENT_TYPE' => 'local', 'WP_DEBUG' => true, 'WP_DEBUG_LOG' => true, 'WP_DEBUG_DISPLAY' => false, 'DISABLE_WP_CRON' => true, 'DISALLOW_FILE_EDIT' => true );
	foreach ( array( 'AUTH_KEY', 'SECURE_AUTH_KEY', 'LOGGED_IN_KEY', 'NONCE_KEY', 'AUTH_SALT', 'SECURE_AUTH_SALT', 'LOGGED_IN_SALT', 'NONCE_SALT' ) as $key ) { $settings[ $key ] = wp_generate_password( 64, true, true ); }
	$config = "<?php\n";
	foreach ( $settings as $key => $value ) { $config .= 'define( ' . var_export( $key, true ) . ', ' . var_export( $value, true ) . " );\n"; }
	$config .= "\$table_prefix = 'fccqa_';\nif ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }\nrequire_once ABSPATH . 'wp-settings.php';\n";
	if ( false === file_put_contents( $root . '/wp-config.php', $config ) ) { throw new RuntimeException( 'Could not write fixture config.' ); }
	echo 'Created isolated fixture config and database: ', $name, PHP_EOL;
	exit;
}
if ( 'install' !== ( $argv[1] ?? '' ) || str_replace( '\\', '/', getenv( 'PROJECT_WORDPRESS_ROOT' ) ) !== $root ) { throw new RuntimeException( 'Explicit fixture root required.' ); }
define( 'WP_INSTALLING', true );
require $root . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/upgrade.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';
if ( is_blog_installed() || DB_NAME !== str_replace( '-', '_', $name ) ) { throw new RuntimeException( 'New isolated database required.' ); }
wp_install( 'Fractional release QA', 'fcc-release-qa', 'qa@example.test', false, '', wp_generate_password( 40, true, true ) );
update_option( 'project_core_fcc_test_install', true );
update_option( 'blog_public', 0 );
activate_plugin( 'advanced-custom-fields/acf.php' );
activate_plugin( 'project-core/project-core.php' );
switch_theme( 'project-child' );
echo 'Installed marked fixture: ', $name, PHP_EOL;
