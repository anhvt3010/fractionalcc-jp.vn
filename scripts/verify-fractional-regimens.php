<?php
/** Regimen persistence checks on a marked isolated installation only. */
if ( 'cli' !== PHP_SAPI ) { http_response_code( 403 ); exit; }
$root = getenv( 'PROJECT_WORDPRESS_ROOT' );
if ( ! $root || realpath( $root ) === realpath( dirname( __DIR__ ) ) ) { throw new RuntimeException( 'An isolated PROJECT_WORDPRESS_ROOT is required.' ); }
require rtrim( $root, '/\\' ) . '/wp-load.php';
if ( ! get_option( 'project_core_fcc_test_install' ) ) { throw new RuntimeException( 'Marked test installation required.' ); }
$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
wp_set_current_user( $admins[0] );
$checks = 0;
function fcc_regimen_check( $condition, $label ) {
	global $checks;
	if ( ! $condition ) { throw new RuntimeException( $label ); }
	++$checks;
}
fcc_regimen_check( ! is_wp_error( project_core_fcc_initialize() ), 'Approved catalog initializes.' );
$page = project_core_fcc_page_id( 'concerns' );
$first = project_core_fcc_legacy_product_id( 6 );
$second = project_core_fcc_legacy_product_id( 7 );
$combo = project_core_fcc_posts( 'fcc_combo' )[0]->ID;
$draft = wp_insert_post( array( 'post_type' => 'fcc_product', 'post_status' => 'draft', 'post_title' => 'Regimen test draft' ) );
$posts_before = count( project_core_fcc_posts( array( 'fcc_product', 'fcc_combo' ) ) );
$fields = project_core_fcc_regimens();
$original_regimens = array();
foreach ( $fields as $definition ) { $original_regimens[ $definition['name'] ] = get_post_meta( $page, 'fcc_' . $definition['name'], true ); }
$original_status = get_post_status( $first );
$original_query = $GLOBALS['wp_query'];
try {
foreach ( $fields as $slug => $definition ) {
	$key = 'field_fcc_' . $definition['name'];
	$defaults = project_core_fcc_default_document();
	$expected = array_map( 'project_core_fcc_snapshot_source', $defaults['regimens'][ $slug ] ?? array() );
	fcc_regimen_check( $expected === wp_list_pluck( project_core_fcc_regimen_posts( $slug, $page ), 'ID' ), 'Fresh initialization matches the approved regimen order.' );
	$field = acf_get_field( $key );
	fcc_regimen_check( 'relationship' === $field['type'] && 6 === $field['max'] && 'id' === $field['return_format'], 'Native ACF relationship with six-item limit.' );
	foreach ( array( array( $draft ), array( $page ), array( 999999999 ), array( -$first ), array( $first, $first ), array_fill( 0, 7, $first ), 'not-an-array', array( array( $first ) ) ) as $invalid ) {
		fcc_regimen_check( is_string( apply_filters( 'acf/validate_value/key=' . $key, true, $invalid ) ), 'Invalid choices rejected.' );
	}
	fcc_regimen_check( 'Existing error' === apply_filters( 'acf/validate_value/key=' . $key, 'Existing error', array( $first ) ), 'Existing ACF error preserved.' );
	fcc_regimen_check( true === apply_filters( 'acf/validate_value/key=' . $key, true, array() ), 'Empty selection allowed.' );
	$query = apply_filters( 'acf/fields/relationship/query/key=' . $key, array() );
	fcc_regimen_check( 'publish' === $query['post_status'], 'Editor only offers published choices.' );
	$selection = array( $second, $combo, $first );
	$_POST['acf'] = array( $key => $selection );
	acf_save_post( $page );
	unset( $_POST['acf'] );
	fcc_regimen_check( $selection === wp_list_pluck( project_core_fcc_regimen_posts( $slug, $page ), 'ID' ), 'Native ACF save retains product/combo order.' );
	$_POST['acf'] = array( $key => array( $first, $second ) );
	acf_save_post( $page );
	unset( $_POST['acf'] );
	fcc_regimen_check( array( $first, $second ) === wp_list_pluck( project_core_fcc_regimen_posts( $slug, $page ), 'ID' ), 'Replacement and reorder persist.' );
	wp_update_post( array( 'ID' => $first, 'post_status' => 'draft' ) );
	fcc_regimen_check( array( $second ) === wp_list_pluck( project_core_fcc_regimen_posts( $slug, $page ), 'ID' ), 'Unpublished selection is hidden.' );
	wp_update_post( array( 'ID' => $first, 'post_status' => 'publish' ) );
	update_field( $key, array(), $page );
	fcc_regimen_check( array() === project_core_fcc_regimen_posts( $slug, $page ), 'Deselection hides all cards.' );
	project_core_fcc_initialize();
	fcc_regimen_check( metadata_exists( 'post', $page, 'fcc_' . $definition['name'] ) && array() === project_core_fcc_regimen_posts( $slug, $page ), 'Rerun preserves explicitly empty selection.' );
	update_field( $key, $selection, $page );
	project_core_fcc_initialize();
	fcc_regimen_check( $selection === wp_list_pluck( project_core_fcc_regimen_posts( $slug, $page ), 'ID' ), 'Rerun preserves edited selection and order.' );
	update_post_meta( $page, 'fcc_' . $definition['name'], array( $draft, $page, $second, $second, $combo ) );
	fcc_regimen_check( array( $second, $combo ) === wp_list_pluck( project_core_fcc_regimen_posts( $slug, $page ), 'ID' ), 'Frontend filters tampered metadata and duplicates.' );
	update_field( $key, $selection, $page );
}
fcc_regimen_check( array() === project_core_fcc_regimen_posts( 'unknown', $page ), 'Unknown concern returns no cards.' );
fcc_regimen_check( array() === project_core_fcc_regimen_posts( 'dom-nau', project_core_fcc_page_id( 'home' ) ), 'Unrelated page cannot supply a regimen.' );
fcc_regimen_check( $posts_before === count( project_core_fcc_posts( array( 'fcc_product', 'fcc_combo' ) ) ), 'Reruns do not duplicate catalog entries.' );

// Inspect actual child-theme markup with the test page as the queried object.
$GLOBALS['wp_query'] = new WP_Query( array( 'page_id' => $page ) );
$frontend = project_core_fcc_frontend_data();
$order_codes = array_merge( array_map( function ( $p ) { return 'prod_' . $p['id']; }, $frontend['products'] ), array_map( function ( $p ) { return 'combo_' . $p['id']; }, $frontend['combos'] ) );
foreach ( $fields as $slug => $definition ) {
	ob_start();
	get_template_part( 'template-parts/fractional/components/concern-regimen', null, array( 'concern' => $slug ) );
	$html = ob_get_clean();
	$dom = new DOMDocument();
	@$dom->loadHTML( '<?xml encoding="UTF-8">' . $html );
	$xpath = new DOMXPath( $dom );
	$cards = $xpath->query( '//*[@data-id]' );
	fcc_regimen_check( 3 === $cards->length && (string) $second === $cards[0]->getAttribute( 'data-id' ) && 'combo-' . $combo === $cards[1]->getAttribute( 'data-id' ) && (string) $first === $cards[2]->getAttribute( 'data-id' ), 'Mixed card markup follows configured order.' );
	foreach ( $xpath->query( '//*[@data-product-select]' ) as $button ) { fcc_regimen_check( in_array( $button->getAttribute( 'data-product-select' ), $order_codes, true ), 'Order button points to a real catalog item.' ); }
	fcc_regimen_check( false !== strpos( $html, esc_url( get_permalink( $combo ) ) ), 'Combo detail link points to the public combo page.' );
}
} finally {
	foreach ( $original_regimens as $name => $value ) { update_field( 'field_fcc_' . $name, $value, $page ); }
	wp_update_post( array( 'ID' => $first, 'post_status' => $original_status ) );
	wp_delete_post( $draft, true );
	$GLOBALS['wp_query'] = $original_query;
}
echo "PASS: {$checks} regimen validation/save/render/initialization checks.\n";
