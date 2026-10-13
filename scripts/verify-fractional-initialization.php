<?php
/**
 * Destructive regression checks for an explicitly marked, isolated test installation.
 * Never run against the working site. Set PROJECT_WORDPRESS_ROOT to the test root.
 */
if ( 'cli' !== PHP_SAPI ) { http_response_code( 403 ); exit; }
$root = getenv( 'PROJECT_WORDPRESS_ROOT' );
if ( ! $root || realpath( $root ) === realpath( dirname( __DIR__ ) ) ) { fwrite( STDERR, "An isolated PROJECT_WORDPRESS_ROOT is required.\n" ); exit( 1 ); }
require rtrim( $root, '/\\' ) . '/wp-load.php';
if ( ! get_option( 'project_core_fcc_test_install' ) || ! function_exists( 'project_core_fcc_initialize' ) ) { fwrite( STDERR, "A marked test installation with Project Core is required.\n" ); exit( 1 ); }
$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
if ( ! $admins ) { fwrite( STDERR, "A test administrator is required.\n" ); exit( 1 ); }
wp_set_current_user( $admins[0] );
$checks = 0;
function fcc_setup_check( $condition, $label ) {
	global $checks;
	if ( ! $condition ) { throw new RuntimeException( $label ); }
	++$checks;
}
function fcc_setup_count() {
	global $wpdb;
	return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts}" );
}
fcc_setup_check( ! get_option( 'project_core_fcc_seed_version' ), 'Test must begin before initialization.' );
$path = WP_PLUGIN_DIR . '/project-core/defaults/fractional.json';
$original = file_get_contents( $path );
$data = json_decode( $original, true );
$before = fcc_setup_count();
$invalid_cases = array();
$invalid = $data;
unset( $invalid['pages']['home'] );
$invalid_cases[] = $invalid;
$invalid = $data;
$invalid['combos'][0]['productIds'] = array( 999999 );
$invalid_cases[] = $invalid;
$invalid = $data;
$invalid['products'][0]['image'] = 'assets/images/missing-test-image.png';
$invalid_cases[] = $invalid;
$invalid_cases[] = array( 'version' => 'invalid' );
$broken_image = get_stylesheet_directory() . '/assets/fractional/assets/images/products/initialization-broken.png';
file_put_contents( $broken_image, 'This is not an image.' );
$invalid = $data;
$invalid['products'][0]['image'] = 'assets/images/products/initialization-broken.png';
$invalid_cases[] = $invalid;
try {
	foreach ( $invalid_cases as $invalid ) {
		file_put_contents( $path, wp_json_encode( $invalid ) );
		fcc_setup_check( is_wp_error( project_core_fcc_initialize() ), 'Invalid defaults must be rejected.' );
		fcc_setup_check( $before === fcc_setup_count() && ! get_option( 'project_core_fcc_seed_version' ) && ! get_option( 'project_core_fcc_setup_lock' ), 'Invalid package must not partially initialize.' );
	}
} finally { file_put_contents( $path, $original ); }
$lock = array( 'started' => time(), 'token' => 'active-test-worker' );
add_option( 'project_core_fcc_setup_lock', $lock, '', false );
$result = project_core_fcc_initialize();
fcc_setup_check( is_wp_error( $result ) && 'fcc_setup_busy' === $result->get_error_code(), 'Active lock blocks another worker.' );
project_core_fcc_release_setup_lock( array( 'started' => time() - HOUR_IN_SECONDS, 'token' => 'older-worker' ) );
fcc_setup_check( $lock === get_option( 'project_core_fcc_setup_lock' ), 'An older worker cannot release a newer lock.' );
project_core_fcc_release_setup_lock( $lock );
add_option( 'project_core_fcc_setup_lock', time() - HOUR_IN_SECONDS, '', false );
do_action( 'admin_init' );
$ids = get_option( 'project_core_fcc_pages' );
fcc_setup_check( $data['version'] === get_option( 'project_core_fcc_seed_version' ), 'Automatic setup recovers an expired legacy lock.' );
fcc_setup_check( ! get_option( 'project_core_fcc_setup_lock' ) && ! get_option( 'project_core_fcc_autosetup_pending' ), 'Successful setup clears lock and queue.' );
fcc_setup_check( 'page' === get_option( 'show_on_front' ) && (int) $ids['home'] === (int) get_option( 'page_on_front' ), 'First setup selects the generated homepage.' );
fcc_setup_check( 'templates/fractional-home.php' === get_page_template_slug( $ids['home'] ), 'Homepage has its custom template.' );
fcc_setup_check( 10 === count( project_core_fcc_posts( 'fcc_product' ) ), 'Ten products initialized.' );
$image = absint( get_post_meta( $ids['home'], 'fcc_home_hero_product_image', true ) );
fcc_setup_check( 'assets/images/banner/combo-c-cx4-gold.png' === get_post_meta( $image, '_fcc_asset_source', true ), 'Gold hero initialized.' );
fcc_setup_check( 'b65161dc5cafed72ff899677c75cc64ef11251ba5811546f2ae499630f1f9042' === hash_file( 'sha256', get_attached_file( $image ) ), 'Hero image matches the supplied PNG.' );
foreach ( $data['products'] as $product ) {
	$id = project_core_fcc_legacy_product_id( $product['id'] );
	$media = absint( get_post_meta( $id, 'fcc_image', true ) );
	fcc_setup_check( $product['image'] === get_post_meta( $media, '_fcc_asset_source', true ), 'Product image source matches packaged defaults.' );
	fcc_setup_check( hash_file( 'sha256', get_attached_file( $media ) ) === hash_file( 'sha256', get_stylesheet_directory() . '/assets/fractional/' . $product['image'] ), 'Product media bytes match the package.' );
}
$changed = $data;
$unused = 'assets/images/products/initialization-unused.png';
copy( get_attached_file( $image ), get_stylesheet_directory() . '/assets/fractional/' . $unused );
$changed['products'][0]['image'] = $unused;
$before = fcc_setup_count();
try {
	file_put_contents( $path, wp_json_encode( $changed ) );
	fcc_setup_check( ! is_wp_error( project_core_fcc_initialize() ), 'Update with a changed packaged image succeeds.' );
	fcc_setup_check( $before === fcc_setup_count(), 'Rerun does not import an unused default image over an existing field.' );
} finally { file_put_contents( $path, $original ); }
$other = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Customer selected homepage' ) );
update_option( 'page_on_front', $other );
wp_update_post( array( 'ID' => $ids['home'], 'post_title' => 'Customer edited title' ) );
project_core_fcc_seed_meta( $ids['home'], 'fcc_home_hero_product_image', 0, 'field_fcc_home_hero_product_image', true );
$before = fcc_setup_count();
fcc_setup_check( ! is_wp_error( project_core_fcc_initialize() ), 'Rerun succeeds.' );
fcc_setup_check( $before === fcc_setup_count(), 'Rerun creates no duplicate posts or media.' );
fcc_setup_check( 'Customer edited title' === get_the_title( $ids['home'] ) && 0 === (int) get_post_meta( $ids['home'], 'fcc_home_hero_product_image', true ), 'Rerun preserves edits and explicitly cleared images.' );
fcc_setup_check( $other === (int) get_option( 'page_on_front' ), 'Rerun preserves the customer homepage.' );
update_option( 'show_on_front', 'posts' );
update_option( 'page_on_front', 0 );
project_core_fcc_initialize();
fcc_setup_check( 'posts' === get_option( 'show_on_front' ), 'Rerun preserves an explicit blog selection.' );
delete_option( 'project_core_fcc_seed_version' );
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $other );
project_core_fcc_initialize();
fcc_setup_check( $other === (int) get_option( 'page_on_front' ), 'First setup respects a preselected homepage.' );
$product = project_core_fcc_legacy_product_id( 1 );
wp_trash_post( $product );
$before = fcc_setup_count();
project_core_fcc_initialize();
fcc_setup_check( $before === fcc_setup_count() && 'trash' === get_post_status( $product ), 'Rerun preserves trashed items without recreating them.' );
fcc_setup_check( $product === project_core_fcc_seed_post( 'fcc_product', 'product_1', 'Do not recreate', 'do-not-recreate' ), 'Stable source key finds the trashed record.' );
wp_untrash_post( $product );
wp_update_post( array( 'ID' => $product, 'post_status' => 'publish' ) );
add_option( 'project_core_fcc_setup_lock', array( 'started' => time() - HOUR_IN_SECONDS, 'token' => 'expired' ), '', false );
fcc_setup_check( ! is_wp_error( project_core_fcc_initialize() ), 'Expired token lock also recovers.' );
project_core_fcc_queue_initial_defaults();
fcc_setup_check( ! get_option( 'project_core_fcc_autosetup_pending' ), 'Reactivation does not queue already initialized data.' );
wp_set_current_user( 0 );
fcc_setup_check( is_wp_error( project_core_fcc_initialize() ), 'Anonymous initialization denied.' );
echo 'PASS: ' . $checks . " initialization regression checks.\n";
