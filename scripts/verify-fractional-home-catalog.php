<?php
/** Selection regression checks; only run on an explicitly marked isolated installation. */
if ( 'cli' !== PHP_SAPI ) { http_response_code( 403 ); exit; }
$root = getenv( 'PROJECT_WORDPRESS_ROOT' );
if ( ! $root || realpath( $root ) === realpath( dirname( __DIR__ ) ) ) { fwrite( STDERR, "An isolated PROJECT_WORDPRESS_ROOT is required.\n" ); exit( 1 ); }
require rtrim( $root, '/\\' ) . '/wp-load.php';
if ( ! get_option( 'project_core_fcc_test_install' ) ) { throw new RuntimeException( 'Marked test installation required.' ); }
$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
wp_set_current_user( $admins[0] );
set_error_handler( function ( $severity, $message ) { if ( error_reporting() & $severity ) { throw new RuntimeException( $message ); } return false; } );
$checks = 0;
function fcc_home_check( $condition, $message ) {
	global $checks;
	if ( ! $condition ) { throw new RuntimeException( $message ); }
	++$checks;
}
function fcc_home_save( $id, $selected, $order ) {
	$_POST['acf'] = array( 'field_fcc_home_catalog' => $selected, 'field_fcc_home_catalog_order' => $order );
	acf_save_post( $id );
	unset( $_POST['acf'] );
}
$defaults = project_core_fcc_default_document();
$original = project_core_fcc_home_catalog_posts();
$featured = wp_list_pluck( project_core_fcc_featured_posts(), 'ID' );
fcc_home_check( 6 === count( $original ), 'Fresh initialization must select six cards.' );
fcc_home_check( $defaults['homeCatalog'] === array_map( function ( $post ) { return get_post_meta( $post->ID, '_fcc_source_key', true ); }, $original ), 'Original card order must be preserved.' );
$all = project_core_fcc_posts( array( 'fcc_product', 'fcc_combo' ) );
$snapshots = array();
foreach ( $all as $post ) {
	foreach ( array( 'fcc_home_catalog', 'fcc_home_catalog_order' ) as $key ) { $snapshots[ $post->ID ][ $key ] = array( metadata_exists( 'post', $post->ID, $key ), get_post_meta( $post->ID, $key, true ) ); }
}
$extra = project_core_fcc_legacy_product_id( 2 );
$combo = $original[0]->ID;
try {
	acf_set_form_data( 'post_id', $extra );
	fcc_home_check( is_string( apply_filters( 'acf/validate_value/name=fcc_home_catalog', true, 1 ) ), 'Seventh item must be rejected.' );
	fcc_home_check( true === apply_filters( 'acf/validate_value/name=fcc_home_catalog', true, 0 ), 'Deselection must be permitted.' );
	fcc_home_check( 'Existing error' === apply_filters( 'acf/validate_value/name=fcc_home_catalog', 'Existing error', 1 ), 'Existing validation errors must be retained.' );
	acf_set_form_data( 'post_id', $combo );
	fcc_home_check( true === apply_filters( 'acf/validate_value/name=fcc_home_catalog', true, 1 ), 'A selected item must still be editable when six are selected.' );
	fcc_home_save( $combo, 0, 1 );
	acf_set_form_data( 'post_id', $extra );
	fcc_home_check( true === apply_filters( 'acf/validate_value/name=fcc_home_catalog', true, 1 ), 'Replacement permitted after deselection.' );
	fcc_home_save( $extra, 1, 0 );
	fcc_home_check( $extra === project_core_fcc_home_catalog_posts()[0]->ID && 6 === count( project_core_fcc_home_catalog_posts() ), 'Native ACF save and ordering must work.' );
	wp_update_post( array( 'ID' => $extra, 'post_status' => 'draft' ) );
	fcc_home_check( 5 === count( project_core_fcc_home_catalog_posts() ), 'Draft products must not render.' );
	wp_update_post( array( 'ID' => $extra, 'post_status' => 'publish' ) );
	update_post_meta( $combo, 'fcc_home_catalog', 1 );
	fcc_home_check( 6 === count( project_core_fcc_home_catalog_posts() ), 'Frontend must cap six even if metadata bypasses ACF validation.' );
	foreach ( $all as $post ) { fcc_home_save( $post->ID, 0, 0 ); }
	project_core_fcc_initialize_home_catalog();
	fcc_home_check( array() === project_core_fcc_home_catalog_posts(), 'Explicit empty selection must survive reruns.' );
	delete_option( 'project_core_fcc_home_catalog_version' );
	project_core_fcc_initialize_home_catalog();
	fcc_home_check( array() === project_core_fcc_home_catalog_posts(), 'Upgrade must preserve preexisting off values.' );
	fcc_home_check( $featured === wp_list_pluck( project_core_fcc_featured_posts(), 'ID' ), 'Featured duo must remain independent.' );
} finally {
	wp_update_post( array( 'ID' => $extra, 'post_status' => 'publish' ) );
	foreach ( $snapshots as $id => $meta ) {
		foreach ( $meta as $key => $row ) { if ( $row[0] ) { update_post_meta( $id, $key, $row[1] ); } else { delete_post_meta( $id, $key ); } }
	}
	update_option( 'project_core_fcc_home_catalog_version', '1', false );
}
ob_start();
project_child_fcc_fragment( 'home_catalog' );
$html = ob_get_clean();
$dom = new DOMDocument();
@$dom->loadHTML( '<?xml encoding="UTF-8">' . $html );
$xpath = new DOMXPath( $dom );
$cards = $xpath->query( '//*[contains(concat(" ", normalize-space(@class), " "), " product-card ")]' );
fcc_home_check( 6 === $cards->length, 'Six server-rendered cards required.' );
$frontend = project_core_fcc_frontend_data();
$codes = array_merge( array_map( function ( $p ) { return 'prod_' . $p['id']; }, $frontend['products'] ), array_map( function ( $p ) { return 'combo_' . $p['id']; }, $frontend['combos'] ) );
foreach ( $xpath->query( '//*[@data-product-select]' ) as $button ) { fcc_home_check( in_array( $button->getAttribute( 'data-product-select' ), $codes, true ), 'Order button must select a real item.' ); }
foreach ( $xpath->query( '//a[@href]' ) as $link ) { fcc_home_check( 0 === strpos( $link->getAttribute( 'href' ), home_url( '/' ) ), 'Detail links must point to a real website page.' ); }
echo "PASS: {$checks} homepage selection/validation/save/render checks.\n";
