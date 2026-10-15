<?php
/** Exercise native editor nonces/permissions on a marked isolated site only. */
if ( 'cli' !== PHP_SAPI ) { http_response_code( 403 ); exit; }
$root = getenv( 'PROJECT_WORDPRESS_ROOT' );
if ( ! $root || realpath( $root ) === realpath( dirname( __DIR__ ) ) ) { throw new RuntimeException( 'Isolated root required.' ); }
require rtrim( $root, '/\\' ) . '/wp-load.php';
if ( ! get_option( 'project_core_fcc_test_install' ) ) { throw new RuntimeException( 'Marked fixture required.' ); }
$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
wp_set_current_user( $admins[0] );
$original = get_option( 'project_core_fcc_featured_combo_id' );
$second = wp_insert_post( array( 'post_type' => 'fcc_combo', 'post_status' => 'publish', 'post_title' => 'QA native editor combo' ) );
$sessions = WP_Session_Tokens::get_instance( $admins[0] );
$expires = time() + 600;
$token = $sessions->create( $expires );
$cookies = array( new WP_Http_Cookie( array( 'name' => AUTH_COOKIE, 'value' => wp_generate_auth_cookie( $admins[0], $expires, 'auth', $token ) ) ), new WP_Http_Cookie( array( 'name' => LOGGED_IN_COOKIE, 'value' => wp_generate_auth_cookie( $admins[0], $expires, 'logged_in', $token ) ) ) );
$checks = 0;
function fcc_combo_http_check( $ok, $message ) {
	global $checks;
	if ( ! $ok ) { throw new RuntimeException( $message ); }
	++$checks;
}
function fcc_combo_http_input( $html, $name ) {
	$dom = new DOMDocument(); @$dom->loadHTML( $html ); $xp = new DOMXPath( $dom );
	$input = $xp->query( '//input[@name="' . $name . '"]' )->item( 0 );
	if ( ! $input ) { throw new RuntimeException( 'Native form input missing: ' . $name ); }
	return $input->getAttribute( 'value' );
}
function fcc_combo_http_save( $body, $authenticated = true ) {
	global $cookies;
	$r = wp_remote_post( admin_url( 'post.php' ), array( 'body' => $body, 'cookies' => $authenticated ? $cookies : array(), 'timeout' => 25 ) );
	if ( is_wp_error( $r ) ) { throw new RuntimeException( $r->get_error_message() ); }
	wp_cache_delete( 'project_core_fcc_featured_combo_id', 'options' );
	return $r;
}
try {
	$home_editor = wp_remote_get( admin_url( 'post.php?post=' . project_core_fcc_page_id( 'home' ) . '&action=edit' ), array( 'cookies' => $cookies, 'timeout' => 25 ) );
	fcc_combo_http_check( ! is_wp_error( $home_editor ) && 200 === wp_remote_retrieve_response_code( $home_editor ), 'Native homepage editor must load.' );
	$home_html = wp_remote_retrieve_body( $home_editor );
	$home_dom = new DOMDocument(); @$home_dom->loadHTML( $home_html ); $home_xp = new DOMXPath( $home_dom );
	$hero_link = $home_xp->query( '//*[@data-key="field_fcc_home_hero_product_image"]//a' )->item( 0 );
	fcc_combo_http_check( $hero_link && $hero_link->getAttribute( 'href' ) === get_edit_post_link( $original, 'raw' ), 'Homepage editor must link to the active combo for hero image changes.' );
	fcc_combo_http_check( false === strpos( $home_html, 'name="acf[field_fcc_home_hero_product_image]"' ), 'Homepage editor must not submit the retired hero image field.' );
	$r = wp_remote_get( admin_url( 'post.php?post=' . $second . '&action=edit' ), array( 'cookies' => $cookies, 'timeout' => 25 ) );
	fcc_combo_http_check( ! is_wp_error( $r ) && 200 === wp_remote_retrieve_response_code( $r ), 'Native combo editor must load.' );
	$html = wp_remote_retrieve_body( $r );
	fcc_combo_http_check( false !== strpos( $html, 'data-key="field_fcc_featured_combo"' ), 'Combo editor must expose the featured toggle.' );
	fcc_combo_http_check( false === strpos( $html, 'data-key="field_fcc_home_catalog"' ), 'Combo editor must not expose the product catalog toggle.' );
	$body = array( 'action' => 'editpost', 'post_ID' => $second, 'post_type' => 'fcc_combo', 'post_status' => 'publish', 'post_title' => 'QA native editor combo', '_wpnonce' => fcc_combo_http_input( $html, '_wpnonce' ), '_acf_screen' => 'post', '_acf_post_id' => $second, '_acf_nonce' => fcc_combo_http_input( $html, '_acf_nonce' ), 'acf' => array( 'field_fcc_featured_combo' => '1' ) );
	$invalid = $body; $invalid['_acf_nonce'] = 'invalid-combo-nonce';
	fcc_combo_http_save( $invalid );
	fcc_combo_http_check( (int) $original === (int) get_option( 'project_core_fcc_featured_combo_id' ), 'Invalid ACF nonce must not change the selection.' );
	$invalid = $body; $invalid['_wpnonce'] = 'invalid-wordpress-nonce';
	fcc_combo_http_save( $invalid );
	fcc_combo_http_check( (int) $original === (int) get_option( 'project_core_fcc_featured_combo_id' ), 'Invalid WordPress nonce must not change the selection.' );
	fcc_combo_http_save( $body, false );
	fcc_combo_http_check( (int) $original === (int) get_option( 'project_core_fcc_featured_combo_id' ), 'Anonymous HTTP saves must not change the selection.' );
	fcc_combo_http_save( $body );
	fcc_combo_http_check( $second === (int) get_option( 'project_core_fcc_featured_combo_id' ), 'Authorized native editor save must select the new combo.' );
	$body['acf']['field_fcc_featured_combo'] = '0';
	fcc_combo_http_save( $body );
	fcc_combo_http_check( 0 === (int) get_option( 'project_core_fcc_featured_combo_id' ), 'Authorized native editor save must permit clearing the combo.' );
} finally {
	update_option( 'project_core_fcc_featured_combo_id', $original, false );
	wp_delete_post( $second, true ); $sessions->destroy( $token );
}
echo "PASS: {$checks} native combo editor, authenticated save and nonce checks.\n";
