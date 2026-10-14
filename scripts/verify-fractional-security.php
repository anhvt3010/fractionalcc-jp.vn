<?php
/** Security regression checks on a marked isolated installation; never sends mail. */
if ( 'cli' !== PHP_SAPI ) { http_response_code( 403 ); exit; }
$root = getenv( 'PROJECT_WORDPRESS_ROOT' );
if ( ! $root || realpath( $root ) === realpath( dirname( __DIR__ ) ) ) { throw new RuntimeException( 'Isolated PROJECT_WORDPRESS_ROOT required.' ); }
define( 'DOING_AJAX', true );
require rtrim( $root, '/\\' ) . '/wp-load.php';
if ( ! get_option( 'project_core_fcc_test_install' ) ) { throw new RuntimeException( 'Marked fixture required.' ); }
set_exception_handler( static function ( $error ) {
	fwrite( STDERR, get_class( $error ) . ': ' . $error->getMessage() . ' at line ' . $error->getLine() . PHP_EOL );
	foreach ( array_slice( $error->getTrace(), 0, 5 ) as $frame ) { fwrite( STDERR, ( $frame['function'] ?? '' ) . ':' . ( $frame['line'] ?? 0 ) . PHP_EOL ); }
	exit( 1 );
} );
$_SERVER['REMOTE_ADDR'] = '192.0.2.25';
$_SERVER['REQUEST_METHOD'] = 'POST';
if ( in_array( '--rate-worker', $argv, true ) ) {
	echo is_wp_error( project_core_fcc_order_rate_limit() ) ? 'blocked' : 'accepted';
	exit;
}
class FccSecurityVerificationStop extends RuntimeException {}
$stop = static function () { return static function () { throw new FccSecurityVerificationStop(); }; };
add_filter( 'wp_die_ajax_handler', $stop );
add_filter( 'wp_die_handler', $stop );
$checks = 0;
$mails = array();
add_filter( 'pre_wp_mail', static function ( $result, $attributes ) use ( &$mails ) { $mails[] = $attributes; return true; }, 999, 2 );
function fcc_security_check( $ok, $label ) { global $checks; if ( ! $ok ) { throw new RuntimeException( $label ); } ++$checks; }
function fcc_security_order( $input ) {
	$_POST = $input;
	ob_start();
	try { project_core_fcc_handle_order_submission(); } catch ( FccSecurityVerificationStop $error ) {}
	return json_decode( ob_get_clean(), true );
}
$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
$admin = $admins[0];
$subscriber = wp_insert_user( array( 'user_login' => 'security_' . wp_generate_password( 10, false ), 'user_pass' => wp_generate_password( 32 ), 'role' => 'subscriber' ) );
fcc_security_check( ! is_wp_error( $subscriber ), 'Create test user.' );
$smtp_before = get_option( 'project_core_fcc_smtp', null );
$orders_before = get_posts( array( 'post_type' => 'fcc_order', 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids' ) );
$rate_key = 'fcc_order_rate_' . hash_hmac( 'sha256', inet_pton( $_SERVER['REMOTE_ADDR'] ), wp_salt( 'nonce' ) );
delete_transient( $rate_key ); delete_option( $rate_key . '_lock' );
try {
	foreach ( array( 0, $subscriber ) as $user ) {
		wp_set_current_user( $user );
		$_SERVER['REQUEST_URI'] = '/unrelated?rest_route=encoded';
		foreach ( array( '/wp/v2/users', '/wp/v2/users/1', '/wp/v2/users/me' ) as $route ) {
			$response = rest_do_request( new WP_REST_Request( 'GET', $route ) );
			fcc_security_check( in_array( $response->get_status(), array( 401, 403 ), true ), 'Resolved REST user route denied: ' . $route );
		}
		fcc_security_check( 200 === rest_do_request( new WP_REST_Request( 'GET', '/wp/v2/pages' ) )->get_status(), 'Public REST pages stay available.' );
	}
	wp_set_current_user( $admin );
	fcc_security_check( 200 === rest_do_request( new WP_REST_Request( 'GET', '/wp/v2/users' ) )->get_status(), 'Administrator can list users.' );
	$previous = new WP_Error( 'previous', 'Earlier handler result.' );
	fcc_security_check( $previous === project_core_fcc_restrict_rest_users( $previous, rest_get_server(), new WP_REST_Request( 'GET', '/wp/v2/users' ) ), 'Earlier REST result preserved.' );
	$batch = new WP_REST_Request( 'POST', '/batch/v1' );
	$batch->set_body_params( array( 'requests' => array( array( 'method' => 'POST', 'path' => '/wp/v2/users' ) ) ) );
	wp_set_current_user( 0 );
	$response = rest_do_request( $batch );
	$batch_data = $response->get_data();
	fcc_security_check( ! empty( $batch_data['responses'][0]['status'] ) && in_array( $batch_data['responses'][0]['status'], array( 401, 403 ), true ), 'REST batch cannot bypass user restriction.' );
	fcc_security_check( false === apply_filters( 'xmlrpc_enabled', true ), 'Authenticated XML-RPC disabled.' );
	$headers = apply_filters( 'wp_headers', array( 'X-Pingback' => 'https://example.invalid/xmlrpc.php' ) );
	fcc_security_check( ! isset( $headers['X-Pingback'] ) && 'nosniff' === $headers['X-Content-Type-Options'] && 'SAMEORIGIN' === $headers['X-Frame-Options'], 'Security headers and pingback removal.' );
	$product = project_core_fcc_legacy_product_id( 1 );
	$base = array( '_fcc_nonce' => wp_create_nonce( 'fcc_order_action' ), 'customer_name' => 'Security QA', 'customer_phone' => '0900000000', 'selected_item' => 'prod_' . $product, 'agree_terms' => 'on', 'website_url' => '' );
	$invalid = array( array( '_fcc_nonce' => 'invalid' ), array( 'agree_terms' => '' ), array( 'agree_terms' => 'false' ), array( 'customer_name' => str_repeat( 'a', 121 ) ), array( 'customer_note' => str_repeat( 'a', 2001 ) ), array( 'customer_address' => str_repeat( 'a', 501 ) ), array( 'customer_phone' => 'abc0900000000' ), array( 'selected_item' => 'prod_999999' ) );
	foreach ( array_keys( $base + array( 'customer_address' => '', 'customer_note' => '', 'fcc_form_source' => '' ) ) as $field ) { $invalid[] = array( $field => array( 'malformed' ) ); }
	foreach ( $invalid as $case ) { fcc_security_check( empty( fcc_security_order( array_replace( $base, $case ) )['success'] ), 'Reject malformed order.' ); }
	$without_agreement = $base; unset( $without_agreement['agree_terms'] );
	fcc_security_check( empty( fcc_security_order( $without_agreement )['success'] ), 'Missing agreement rejected.' );
	$_SERVER['REQUEST_METHOD'] = 'GET';
	fcc_security_check( empty( fcc_security_order( $base )['success'] ), 'GET cannot create orders.' );
	$_SERVER['REQUEST_METHOD'] = 'POST';
	fcc_security_check( ! $mails && $orders_before === get_posts( array( 'post_type' => 'fcc_order', 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids' ) ), 'Rejected submissions create no orders and send no mail.' );
	$base['customer_note'] = '<script>alert(1)</script><img src=x onerror=alert(1)> & private test';
	fcc_security_check( ! empty( fcc_security_order( $base )['success'] ), 'Valid order still accepted.' );
	fcc_security_check( 1 === count( $mails ) && false === strpos( $mails[0]['message'], '<script>' ) && false === strpos( $mails[0]['message'], '<img src=x' ), 'Customer content cannot inject email markup.' );
	delete_transient( $rate_key );
	for ( $i = 0; $i < 8; ++$i ) { fcc_security_check( true === project_core_fcc_order_rate_limit(), 'Initial rate budget available.' ); }
	$workers = array();
	for ( $i = 0; $i < 8; ++$i ) {
		$pipes = array();
		$process = proc_open( array( PHP_BINARY, __FILE__, '--rate-worker' ), array( 0 => array( 'pipe', 'r' ), 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes );
		fclose( $pipes[0] ); $workers[] = array( $process, $pipes );
	}
	$accepted = 0;
	foreach ( $workers as $worker ) {
		$output = stream_get_contents( $worker[1][1] ); $errors = stream_get_contents( $worker[1][2] );
		fclose( $worker[1][1] ); fclose( $worker[1][2] );
		fcc_security_check( 0 === proc_close( $worker[0] ) && '' === $errors, 'Parallel rate worker completed.' );
		fcc_security_check( in_array( $output, array( 'accepted', 'blocked' ), true ), 'Parallel result has expected format.' );
		if ( 'accepted' === $output ) { ++$accepted; }
	}
	// Other PHP processes changed database-backed transients; discard this process's option cache.
	wp_cache_delete( '_transient_' . $rate_key, 'options' );
	fcc_security_check( $accepted <= 2 && 8 + $accepted === get_transient( $rate_key )['count'], 'Parallel requests cannot exceed rate budget.' );
	while ( get_transient( $rate_key )['count'] < 10 ) { if ( true !== project_core_fcc_order_rate_limit() ) { throw new RuntimeException( 'Remaining rate budget unavailable.' ); } }
	$_SERVER['HTTP_X_FORWARDED_FOR'] = '203.0.113.99';
	fcc_security_check( is_wp_error( project_core_fcc_order_rate_limit() ), 'Spoofed forwarding header cannot reset rate limit.' );
	$before_mail = count( $mails );
	fcc_security_check( empty( fcc_security_order( $base )['success'] ) && $before_mail === count( $mails ), 'Rate rejection cannot send email.' );
	delete_transient( $rate_key );
	add_option( $rate_key . '_lock', array( 'token' => 'expired', 'expires' => time() - 60 ), '', false );
	fcc_security_check( true === project_core_fcc_order_rate_limit(), 'Expired rate lock recovers.' );
	$state = get_transient( $rate_key ); $state['expires'] = time() - 1; set_transient( $rate_key, $state, 60 );
	fcc_security_check( true === project_core_fcc_order_rate_limit() && 1 === get_transient( $rate_key )['count'], 'Expired rate window resets.' );
	wp_set_current_user( $admin );
	$current = project_core_fcc_default_smtp_settings(); $current['password'] = 'fixture-only-secret';
	$smtp = array( 'smtp_enabled' => '1', 'smtp_auth' => '1', 'smtp_host' => 'smtp.example.invalid', 'smtp_port' => '587', 'smtp_encryption' => 'tls', 'smtp_password' => '', 'smtp_from_email' => 'qa@example.invalid', 'notify_email' => 'qa@example.invalid' );
	$validated = project_core_fcc_validate_smtp_settings( $smtp, $current );
	fcc_security_check( ! is_wp_error( $validated ) && $validated['password'] === $current['password'], 'Blank password preserves saved credential.' );
	foreach ( array( array( 'smtp_host' => 'smtp.example.invalid;evil' ), array( 'smtp_port' => '65536' ), array( 'smtp_port' => '-1' ), array( 'smtp_port' => array( '587' ) ), array( 'smtp_encryption' => 'none' ), array( 'notify_email' => 'invalid' ), array( 'smtp_password' => array( 'invalid' ) ) ) as $case ) {
		fcc_security_check( is_wp_error( project_core_fcc_validate_smtp_settings( array_replace( $smtp, $case ), $current ) ), 'Invalid SMTP configuration rejected.' );
	}
	update_option( 'project_core_fcc_smtp', $current, false );
	$_POST = array_replace( $smtp, array( 'fcc_save_smtp' => '1', 'fcc_smtp_nonce' => wp_create_nonce( 'fcc_smtp_settings_nonce' ), 'smtp_port' => '-1' ) );
	$_REQUEST = $_POST;
	ob_start(); project_core_fcc_render_smtp_page(); $html = ob_get_clean();
	fcc_security_check( $current === get_option( 'project_core_fcc_smtp' ) && false === strpos( $html, $current['password'] ), 'Invalid SMTP save is atomic; secret not rendered.' );
	$_POST['fcc_smtp_nonce'] = 'invalid';
	$_REQUEST = $_POST;
	ob_start(); try { project_core_fcc_render_smtp_page(); fcc_security_check( false, 'Invalid SMTP nonce accepted.' ); } catch ( FccSecurityVerificationStop $error ) { fcc_security_check( true, 'Invalid SMTP nonce rejected.' ); } ob_end_clean();
	wp_set_current_user( $subscriber ); $_POST = array();
	ob_start(); try { project_core_fcc_render_smtp_page(); fcc_security_check( false, 'Subscriber SMTP access accepted.' ); } catch ( FccSecurityVerificationStop $error ) { fcc_security_check( true, 'Subscriber SMTP access denied.' ); } ob_end_clean();
	wp_set_current_user( $admin );
	$_POST = array( 'action' => 'inline-save', 'fcc_quick_edit_present' => '1', '_inline_edit' => wp_create_nonce( 'inlineeditnonce' ), 'fcc_quick_edit_nonce' => wp_create_nonce( 'fcc_product_quick_edit' ) );
	fcc_security_check( project_core_fcc_quick_edit_authorized( $product ), 'Administrator quick edit uses both nonces and post capability.' );
	$_POST['fcc_quick_edit_nonce'] = array( 'invalid' );
	fcc_security_check( ! project_core_fcc_quick_edit_authorized( $product ), 'Malformed quick edit nonce rejected.' );
	$_POST['fcc_quick_edit_nonce'] = 'invalid';
	fcc_security_check( ! project_core_fcc_quick_edit_authorized( $product ), 'Invalid quick edit nonce rejected.' );
	wp_set_current_user( $subscriber );
	$_POST['_inline_edit'] = wp_create_nonce( 'inlineeditnonce' ); $_POST['fcc_quick_edit_nonce'] = wp_create_nonce( 'fcc_product_quick_edit' );
	fcc_security_check( ! project_core_fcc_quick_edit_authorized( $product ), 'Subscriber cannot edit product despite own valid nonces.' );
	wp_set_current_user( $admin );
	fcc_security_check( ! get_post_type_object( 'fcc_order' )->public && ! get_post_type_object( 'fcc_order' )->show_in_rest, 'Orders excluded from public routes and REST.' );
	fcc_security_check( count( acf_get_local_fields() ) > 1000, 'Editor/CLI retains complete ACF schema.' );
} finally {
	wp_set_current_user( $admin );
	foreach ( get_posts( array( 'post_type' => 'fcc_order', 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids' ) ) as $id ) { if ( ! in_array( $id, $orders_before, true ) ) { wp_delete_post( $id, true ); } }
	if ( null === $smtp_before ) { delete_option( 'project_core_fcc_smtp' ); } else { update_option( 'project_core_fcc_smtp', $smtp_before, false ); }
	delete_transient( $rate_key ); delete_option( $rate_key . '_lock' );
	require_once ABSPATH . 'wp-admin/includes/user.php'; wp_delete_user( $subscriber );
}
echo "PASS: {$checks} security checks; all email captured, fixture changes restored.\n";
