<?php
/** Selection/save/render regression checks; never run against the working site. */
if ( 'cli' !== PHP_SAPI ) { http_response_code( 403 ); exit; }
$root = getenv( 'PROJECT_WORDPRESS_ROOT' );
if ( ! $root || realpath( $root ) === realpath( dirname( __DIR__ ) ) ) { throw new RuntimeException( 'Isolated PROJECT_WORDPRESS_ROOT required.' ); }
require rtrim( $root, '/\\' ) . '/wp-load.php';
if ( ! get_option( 'project_core_fcc_test_install' ) ) { throw new RuntimeException( 'Marked fixture required.' ); }
$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
wp_set_current_user( $admins[0] );
add_filter( 'pre_wp_mail', '__return_true' );
set_error_handler( function ( $severity, $message ) { if ( error_reporting() & $severity ) { throw new RuntimeException( $message ); } return false; } );
$checks = 0;
function fcc_selection_check( $condition, $message ) {
	global $checks;
	if ( ! $condition ) { throw new RuntimeException( $message ); }
	++$checks;
}
function fcc_selection_save( $id, $selected, $order ) {
	$_POST['acf'] = array( 'field_fcc_home_catalog' => $selected, 'field_fcc_home_catalog_order' => $order );
	acf_save_post( $id ); unset( $_POST['acf'] );
}
function fcc_selection_counts() {
	ob_start(); project_child_fcc_fragment( 'home_catalog' ); $html = ob_get_clean();
	$dom = new DOMDocument(); @$dom->loadHTML( '<?xml encoding="UTF-8">' . $html ); $xp = new DOMXPath( $dom );
	return array( $xp->query( '//*[@data-type and @data-type!="combo"]' )->length, $xp->query( '//*[@data-type="combo"]' )->length );
}
$ids = wp_list_pluck( project_core_fcc_posts( 'fcc_product' ), 'ID' );
$original = project_core_fcc_featured_combo();
$choice = get_option( 'project_core_fcc_featured_combo_id' );
$snapshots = array();
foreach ( array_merge( $ids, array( $original['id'] ) ) as $id ) {
	foreach ( array( 'fcc_home_catalog', 'fcc_home_catalog_order', 'fcc_featured' ) as $key ) { $snapshots[ $id ][ $key ] = array( metadata_exists( 'post', $id, $key ), get_post_meta( $id, $key, true ) ); }
}
$second = wp_insert_post( array( 'post_type' => 'fcc_combo', 'post_status' => 'publish', 'post_title' => 'QA alternate featured combo' ) );
foreach ( get_post_meta( $original['id'] ) as $key => $values ) { foreach ( $values as $value ) { add_post_meta( $second, $key, maybe_unserialize( $value ) ); } }
update_post_meta( $second, '_fcc_source_key', 'combo_QA_ALTERNATE' );
update_post_meta( $second, 'fcc_code', 'QA-ALT' );
update_post_meta( $second, 'fcc_shortName', 'QA alternate combo' );
update_post_meta( $second, 'fcc_image', get_post_meta( $ids[0], 'fcc_image', true ) );
update_post_meta( $second, 'fcc_salePrice', 123456 );
update_post_meta( $second, 'fcc_originalPrice', 234567 );
try {
	foreach ( $ids as $id ) { fcc_selection_save( $id, 0, 0 ); }
	foreach ( array_slice( $ids, 0, 6 ) as $position => $id ) { fcc_selection_save( $id, 1, $position ); }
	update_post_meta( $original['id'], 'fcc_home_catalog', 1 );
	fcc_selection_check( array_slice( $ids, 0, 6 ) === wp_list_pluck( project_core_fcc_home_catalog_posts(), 'ID' ), 'Six products must be independent of the combo.' );
	acf_set_form_data( 'post_id', $ids[6] );
	fcc_selection_check( is_string( project_core_fcc_validate_home_catalog( true, 1 ) ), 'Seventh product rejected.' );
	fcc_selection_check( true === project_core_fcc_validate_home_catalog( true, 0 ), 'Deselection permitted.' );
	acf_set_form_data( 'post_id', $ids[0] );
	fcc_selection_check( true === project_core_fcc_validate_home_catalog( true, 1 ), 'Existing selection editable at the limit.' );
	wp_update_post( array( 'ID' => $ids[0], 'post_status' => 'draft' ) );
	fcc_selection_check( 5 === count( project_core_fcc_home_catalog_posts() ), 'Draft products hidden.' );
	wp_update_post( array( 'ID' => $ids[0], 'post_status' => 'publish' ) );
	fcc_selection_save( $ids[6], 1, 99 );
	fcc_selection_check( 6 === count( project_core_fcc_home_catalog_posts() ), 'Frontend cap survives direct metadata updates.' );
	fcc_selection_save( $ids[6], 0, 0 );
	$group = acf_get_field_group( 'group_fcc_home_catalog' );
	fcc_selection_check( array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'fcc_product' ) ) ) === $group['location'], 'Catalog controls only belong to products.' );
	acf_set_form_data( 'post_id', $second );
	fcc_selection_check( true === project_core_fcc_validate_featured_combo( true, 1 ), 'Published combo selectable.' );
	update_field( 'field_fcc_featured_combo', 1, $second );
	fcc_selection_check( $second === project_core_fcc_featured_combo()['id'], 'Native ACF replaces the selected combo.' );
	fcc_selection_check( ! get_field( 'field_fcc_featured_combo', $original['id'] ) && get_field( 'field_fcc_featured_combo', $second ), 'Exactly one combo toggle enabled.' );
	update_field( 'field_fcc_featured_combo', 0, $original['id'] );
	fcc_selection_check( $second === project_core_fcc_featured_combo()['id'], 'Saving another combo as off does not clear selection.' );
	fcc_selection_check( 'combo_' . $second === project_child_fcc_featured_combo_order_code(), 'Global purchase ID follows selection.' );
	fcc_selection_check( wp_get_attachment_image_url( get_post_meta( $second, 'fcc_image', true ), 'full' ) === project_child_fcc_hero_product_image_url(), 'Hero image follows selection.' );
	fcc_selection_check( in_array( $second, wp_list_pluck( project_core_fcc_featured_posts(), 'ID' ), true ) && ! in_array( $original['id'], wp_list_pluck( project_core_fcc_featured_posts(), 'ID' ), true ), 'Primary duo follows selection.' );
	fcc_selection_check( array( 6, 1 ) === fcc_selection_counts(), 'Homepage renders six products plus one combo.' );
	foreach ( array( home_url( '/' ), project_core_fcc_url( 'catalog' ) ) as $url ) {
		$r = wp_remote_get( $url );
		fcc_selection_check( ! is_wp_error( $r ) && 200 === wp_remote_retrieve_response_code( $r ), 'Affected HTTP page responds.' );
		$html = wp_remote_retrieve_body( $r );
		fcc_selection_check( ! preg_match( '/Warning:|Fatal error:|Parse error:/', $html ), 'No public PHP warnings.' );
		fcc_selection_check( false !== strpos( $html, 'QA alternate' ) && false !== strpos( $html, 'data-product-select="combo_' . $second . '"' ), 'Selected combo details and purchase ID render over HTTP.' );
		if ( home_url( '/' ) === $url ) { fcc_selection_check( false === strpos( $html, 'data-product-select="combo_' . $original['id'] . '"' ), 'No stale homepage combo purchase IDs.' ); }
		$dom = new DOMDocument(); @$dom->loadHTML( '<?xml encoding="UTF-8">' . $html ); $xp = new DOMXPath( $dom );
		$selector = home_url( '/' ) === $url ? '//*[contains(concat(" ",normalize-space(@class)," ")," spotlight-price-final ")]' : '//*[@id="danh-sach-combo"]//*[contains(concat(" ",normalize-space(@class)," ")," combo-sale-price ")]';
		$price = $xp->query( $selector )->item( 0 );
		fcc_selection_check( $price && trim( $price->textContent ) === project_child_fcc_price( 123456 ), 'Featured price must follow the selected combo on both pages.' );
	}
	wp_update_post( array( 'ID' => $second, 'post_status' => 'draft' ) );
	fcc_selection_check( array() === project_core_fcc_featured_combo() && '' === project_child_fcc_featured_combo_order_code(), 'Unpublished combo cannot be ordered from featured placements.' );
	wp_update_post( array( 'ID' => $second, 'post_status' => 'publish' ) );
	wp_set_current_user( 0 );
	fcc_selection_check( is_wp_error( project_core_fcc_set_featured_combo( $original['id'] ) ), 'Unauthorized setter denied.' );
	update_field( 'field_fcc_featured_combo', 1, $original['id'] );
	fcc_selection_check( $second === project_core_fcc_featured_combo()['id'], 'Unauthorized ACF update leaves selection unchanged.' );
	wp_set_current_user( $admins[0] );
	fcc_selection_check( is_wp_error( project_core_fcc_set_featured_combo( $ids[0] ) ), 'A product cannot be selected as a combo.' );
	update_field( 'field_fcc_featured_combo', 0, $second ); project_core_fcc_initialize_featured_combo();
	fcc_selection_check( array() === project_core_fcc_featured_combo() && '' === project_child_fcc_hero_product_image_url(), 'Explicit empty combo survives initialization without old hero fallback.' );
	fcc_selection_check( array( 6, 0 ) === fcc_selection_counts(), 'Removing combo retains all six products.' );
	ob_start(); get_template_part( 'template-parts/fractional/home/section-concerns' ); $concerns_html = ob_get_clean();
	fcc_selection_check( false === strpos( $concerns_html, 'Chọn combo này' ), 'Clearing the featured combo must hide recommendations that no longer have a combo.' );
	$native_hero = acf_prepare_field( acf_get_field( 'field_fcc_home_hero_product_image' ) );
	fcc_selection_check( 'message' === $native_hero['type'] && false === strpos( $native_hero['message'], '<a ' ), 'Native hero editor must not offer a stale combo link after clearing the selection.' );
	delete_option( 'project_core_fcc_featured_combo_id' ); project_core_fcc_initialize_featured_combo();
	fcc_selection_check( $original['id'] === project_core_fcc_featured_combo()['id'], 'Legacy highlighted combo migrates.' );
	project_core_fcc_set_featured_combo( $second ); project_core_fcc_initialize_featured_combo();
	fcc_selection_check( $second === project_core_fcc_featured_combo()['id'], 'Migration reruns preserve changed selection.' );
	$result = project_core_fcc_initialize();
	fcc_selection_check( ! is_wp_error( $result ) && $second === project_core_fcc_featured_combo()['id'] && array_slice( $ids, 0, 6 ) === wp_list_pluck( project_core_fcc_home_catalog_posts(), 'ID' ), 'Full initializer reruns preserve the selected combo and all six edited product choices.' );
	foreach ( $ids as $id ) { fcc_selection_save( $id, 0, 0 ); }
	delete_option( 'project_core_fcc_home_catalog_version' ); project_core_fcc_initialize_home_catalog();
	fcc_selection_check( array() === project_core_fcc_home_catalog_posts(), 'Explicit empty product selection survives reruns.' );
} finally {
	wp_set_current_user( $admins[0] ); wp_update_post( array( 'ID' => $ids[0], 'post_status' => 'publish' ) );
	foreach ( $snapshots as $id => $meta ) { foreach ( $meta as $key => $row ) { if ( $row[0] ) { update_post_meta( $id, $key, $row[1] ); } else { delete_post_meta( $id, $key ); } } }
	update_option( 'project_core_fcc_featured_combo_id', $choice, false ); update_option( 'project_core_fcc_home_catalog_version', '1', false );
	wp_delete_post( $second, true );
}
echo "PASS: {$checks} product limit, single-combo selection, migration, authorization and HTTP checks.\n";
