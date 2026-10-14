<?php
/** Contact save/render/clear regressions, only on a marked isolated WordPress installation. */
if ( 'cli' !== PHP_SAPI ) { exit; }
$root = getenv( 'PROJECT_WORDPRESS_ROOT' );
if ( ! $root || realpath( $root ) === realpath( dirname( __DIR__ ) ) ) { throw new RuntimeException( 'An isolated root is required.' ); }
require $root . '/wp-load.php';
if ( ! get_option( 'project_core_fcc_test_install' ) ) { throw new RuntimeException( 'A marked fixture is required.' ); }
$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
$admin = (int) $admins[0];
$id = project_core_fcc_page_id( 'shared' );
$names = array( 'hotline', 'hotlineDisplay', 'phone', 'phoneDisplay', 'addressHN', 'email', 'zaloLink', 'messengerLink' );
$original = array(); foreach ( $names as $name ) { $original[ $name ] = project_core_fcc_site( $name ); }
$checks = 0;
function fcc_contact_check( $condition, $message ) {
	global $checks;
	if ( ! $condition ) { throw new RuntimeException( $message ); }
	++$checks;
}
function fcc_contact_hash() {
	return hash( 'sha256', serialize( get_post_meta( project_core_fcc_page_id( 'shared' ) ) ) );
}
function fcc_contact_render( $kind, $post_id ) {
	$GLOBALS['wp_query'] = new WP_Query( array( 'p' => $post_id, 'post_type' => get_post_type( $post_id ) ) );
	$GLOBALS['post'] = get_post( $post_id ); setup_postdata( $GLOBALS['post'] );
	ob_start(); get_template_part( 'template-parts/fractional/' . $kind ); $html = ob_get_clean();
	libxml_use_internal_errors( true ); $document = new DOMDocument(); $document->loadHTML( '<?xml encoding="utf-8" ?>' . $html );
	return array( $html, new DOMXPath( $document ) );
}
try {
	$before = fcc_contact_hash(); wp_set_current_user( 0 );
	fcc_contact_check( is_wp_error( project_core_fcc_save_contacts( $original ) ) && $before === fcc_contact_hash(), 'Anonymous saves rejected without writes.' );
	wp_set_current_user( $admin );
	foreach ( array( array( 'email', 'not-an-email' ), array( 'hotline', 'abc0985561862' ), array( 'phone', array( 'bad' ) ), array( 'zaloLink', 'javascript:alert(1)' ), array( 'zaloLink', 'https://zalo.me.evil.invalid/123' ), array( 'messengerLink', 'https://unapproved.invalid/page' ), array( 'messengerLink', 'https://user:pass@facebook.com/page' ) ) as $bad ) {
		$input = $original; $input[ $bad[0] ] = $bad[1];
		fcc_contact_check( is_wp_error( project_core_fcc_save_contacts( $input ) ) && $before === fcc_contact_hash(), 'Invalid complete form rejected atomically: ' . $bad[0] );
	}
	foreach ( array( 'hotline', 'phone', 'email', 'zaloLink', 'messengerLink' ) as $name ) {
		$field = acf_get_field( 'field_fcc_' . $name );
		fcc_contact_check( true !== apply_filters( 'acf/validate_value', true, array( 'bad' ), $field ), 'ACF rejects malformed contacts: ' . $name );
	}
	$die_handler = static function () { return static function () { throw new RuntimeException( 'contact_nonce_rejected' ); }; };
	$previous_post = $_POST; $_POST = array( 'fcc_save_contact' => '1' ); add_filter( 'wp_die_handler', $die_handler );
	ob_start();
	try { project_core_fcc_render_contact_page(); throw new RuntimeException( 'Missing nonce accepted.' ); }
	catch ( RuntimeException $error ) { fcc_contact_check( 'contact_nonce_rejected' === $error->getMessage() && $before === fcc_contact_hash(), 'Missing nonce denied without writes.' ); }
	finally { ob_end_clean(); remove_filter( 'wp_die_handler', $die_handler ); $_POST = $previous_post; }
	$changed = array( 'hotline' => '090 123 4567', 'hotlineDisplay' => '', 'phone' => '+84 912 345 678', 'phoneDisplay' => '+84 912 345 678', 'addressHN' => 'CONTACT_QA_ADDRESS', 'email' => 'qa-contact@example.invalid', 'zaloLink' => '84985561862', 'messengerLink' => 'https://web.facebook.com/hangnhatchomoinha.vn' );
	fcc_contact_check( true === project_core_fcc_save_contacts( $changed ), 'Valid contacts saved.' );
	fcc_contact_check( '0901234567' === project_core_fcc_site( 'hotline' ) && '090 123 4567' === project_core_fcc_site( 'hotlineDisplay' ), 'Phone normalization and automatic display formatting.' );
	fcc_contact_check( '+84912345678' === project_core_fcc_site( 'phone' ) && 'tel:+84912345678' === project_child_fcc_contact_link( 'phone' ), 'International telephone prefix preserved.' );
	fcc_contact_check( 'https://zalo.me/84985561862' === project_core_fcc_site( 'zaloLink' ) && $changed['messengerLink'] === project_core_fcc_site( 'messengerLink' ), 'Approved channel URLs preserved exactly.' );
	foreach ( $names as $name ) {
		fcc_contact_check( 'field_fcc_' . $name === get_post_meta( $id, '_fcc_' . $name, true ) && get_field( 'field_fcc_' . $name, $id ) === project_core_fcc_site( $name ), 'ACF and canonical metadata agree: ' . $name );
	}
	$bound_override = static function ( $check, $post_id, $key ) {
		return 'fcc_ui_m_f412d16b3a3e' === $key ? array( 'Hotline: 0979 111 222' ) : $check;
	};
	add_filter( 'get_post_metadata', $bound_override, 10, 3 );
	fcc_contact_check( 'Hotline: 090 123 4567' === project_child_fcc_raw_text( 'fcc_ui_m_f412d16b3a3e', 'shared' ), 'Edited contact labels still follow the central number.' );
	remove_filter( 'get_post_metadata', $bound_override );
	fcc_contact_check( 'Ví dụ: 0987654321' === project_child_fcc_raw_text( 'fcc_ui_e023e68eba385b', 'shared' ), 'Customer phone placeholder stays independent.' );
	$fcc_targets = array();
	foreach ( array( 'home', 'catalog', 'concerns', 'policies', 'thankyou' ) as $kind ) { $fcc_targets[] = array( $kind, project_core_fcc_page_id( $kind ) ); }
	foreach ( project_core_fcc_posts( 'fcc_product' ) as $product ) { $fcc_targets[] = array( 'product', $product->ID ); }
	$allowed = array_map( 'project_child_fcc_contact_link', array( 'hotline', 'phone', 'email', 'zalo', 'facebook' ) );
	foreach ( $fcc_targets as $target ) {
		list( $html, $xpath ) = fcc_contact_render( $target[0], $target[1] );
		foreach ( $xpath->query( '//a[@href]' ) as $link ) {
			$href = $link->getAttribute( 'href' );
			if ( preg_match( '#^(tel:|mailto:|https?://(?:zalo\.me|m\.me|(?:web\.)?facebook\.com)/)#', $href ) ) { fcc_contact_check( in_array( $href, $allowed, true ), 'Contact link follows saved channel.' ); }
		}
		$visible_text = ''; foreach ( $xpath->query( '//text()[not(ancestor::script) and not(ancestor::style)] | //@aria-label' ) as $text ) { $visible_text .= $text->nodeValue; }
		fcc_contact_check( ! preg_match( '/0985?\s?556\s?1862|0985\s?561\s?862|096\s?518\s?0859|jpbuidangco\.ltd@gmail\.com/', $visible_text ), 'No obsolete contact copy on ' . $target[0] );
		fcc_contact_check( strpos( $html, 'CONTACT_QA_ADDRESS' ) !== false, 'All footers use the saved address.' );
	}
	fcc_contact_check( true === project_core_fcc_save_contacts( array_fill_keys( $names, '' ) ), 'All contacts can be explicitly cleared.' );
	fcc_contact_check( ! is_wp_error( project_core_fcc_initialize() ), 'Initializer rerun after clearing.' );
	foreach ( $names as $name ) { fcc_contact_check( '' === project_core_fcc_site( $name ), 'Rerun preserves cleared contact: ' . $name ); }
	foreach ( $fcc_targets as $target ) {
		list( $html, $xpath ) = fcc_contact_render( $target[0], $target[1] );
		fcc_contact_check( 0 === $xpath->query( '//a[starts-with(@href,"tel:") or starts-with(@href,"mailto:") or contains(@href,"zalo.me/") or contains(@href,"facebook.com/") or contains(@href,"m.me/")]' )->length, 'Cleared contacts produce no link or empty scheme.' );
		fcc_contact_check( 0 === $xpath->query( '//footer//a[@hidden] | //footer//p[contains(.,"Địa chỉ:")]' )->length, 'Cleared footer rows are omitted.' );
	}
} finally {
	wp_set_current_user( $admin ); project_core_fcc_save_contacts( $original );
}
echo "PASS: {$checks} contact validation/permissions/ACF/render/clear/preservation checks.\n";
