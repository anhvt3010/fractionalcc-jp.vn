<?php
/** Order integration checks on a marked isolated installation; captures mail without sending it. */
if ( 'cli' !== PHP_SAPI ) { exit( 1 ); }
$root = getenv( 'PROJECT_WORDPRESS_ROOT' );
if ( ! $root || realpath( $root ) === realpath( dirname( __DIR__ ) ) ) { fwrite( STDERR, "An isolated PROJECT_WORDPRESS_ROOT is required.\n" ); exit( 1 ); }
$ajax = ! in_array( '--post', $argv, true );
if ( $ajax ) { define( 'DOING_AJAX', true ); }
require rtrim( $root, '/\\' ) . '/wp-load.php';
if ( ! get_option( 'project_core_fcc_test_install' ) ) { throw new RuntimeException( 'Marked test installation required.' ); }
wp_set_current_user( 0 );
class FccOrderVerificationStop extends RuntimeException {}
$mail_calls = 0;
$mail_result = true;
$redirect = '';
add_filter( 'pre_wp_mail', function ( $value, $attributes ) {
	global $mail_calls, $mail_result;
	++$mail_calls;
	if ( ! is_email( $attributes['to'] ) || false === strpos( $attributes['message'], 'KIỂM TRA KỸ THUẬT' ) ) { throw new RuntimeException( 'Mail payload invalid.' ); }
	return $mail_result;
}, 999, 2 );
$stop = function () { return function () { throw new FccOrderVerificationStop(); }; };
add_filter( 'wp_die_ajax_handler', $stop );
add_filter( 'wp_die_handler', $stop );
add_filter( 'wp_redirect', function ( $url ) { global $redirect; $redirect = $url; throw new FccOrderVerificationStop(); } );
$checks = 0;
function fcc_order_check( $condition, $label ) { global $checks; if ( ! $condition ) { throw new RuntimeException( $label ); } ++$checks; }
function fcc_order_run( $data ) {
	global $ajax, $redirect;
	$_POST = $data;
	$redirect = '';
	ob_start();
	try { project_core_fcc_handle_order_submission(); } catch ( FccOrderVerificationStop $error ) {}
	$body = ob_get_clean();
	return $ajax ? json_decode( $body, true ) : array( 'success' => '' !== $redirect, 'redirect' => $redirect );
}
$base = array( '_fcc_nonce' => wp_create_nonce( 'fcc_order_action' ), 'customer_name' => 'KIỂM TRA KỸ THUẬT', 'customer_phone' => '0900000000', 'customer_address' => 'Dữ liệu kiểm thử, không giao hàng', 'customer_note' => 'KIỂM TRA KỸ THUẬT – KHÔNG PHẢI ĐƠN HÀNG', 'website_url' => '', 'agree_terms' => 'on' );
$created = array();
try {
	$product = project_core_fcc_legacy_product_id( 1 );
	$combo = project_core_fcc_combos()[0]['id'];
	foreach ( array( 'home_inline', 'product_inline', 'modal' ) as $source ) {
		foreach ( array( 'prod_' . $product, 'combo_' . $combo ) as $item ) {
			$before = $mail_calls;
			$result = fcc_order_run( $base + array( 'selected_item' => $item, 'selected_item_name' => 'Tên giả từ client', 'fcc_form_source' => $source ) );
			fcc_order_check( ! empty( $result['success'] ), 'Valid form submission failed: ' . $source );
			fcc_order_check( $before + 1 === $mail_calls, 'Must invoke mail exactly once.' );
			$order = get_posts( array( 'post_type' => 'fcc_order', 'posts_per_page' => 1, 'orderby' => 'ID', 'order' => 'DESC' ) )[0];
			$created[] = $order->ID;
			fcc_order_check( $source === get_post_meta( $order->ID, '_fcc_form_source', true ), 'Form source not stored.' );
			fcc_order_check( 'sent' === get_post_meta( $order->ID, '_fcc_mail_status', true ), 'Accepted mail status not stored.' );
			fcc_order_check( $item === get_post_meta( $order->ID, '_fcc_selected_item', true ) && 'Tên giả từ client' !== get_post_meta( $order->ID, '_fcc_selected_item_name', true ), 'Order must resolve authoritative product/combo.' );
			fcc_order_check( get_post_meta( $order->ID, '_fcc_customer_note', true ) === $base['customer_note'], 'Form note lost.' );
		}
	}
	$before = $mail_calls;
	foreach ( array( array( '_fcc_nonce' => 'invalid' ), array( 'customer_name' => '' ), array( 'customer_phone' => 'invalid' ), array( 'selected_item' => 'prod_999999' ) ) as $invalid ) {
		$result = fcc_order_run( array_replace( $base + array( 'selected_item' => 'prod_' . $product ), $invalid ) );
		fcc_order_check( empty( $result['success'] ), 'Invalid request accepted.' );
	}
	fcc_order_check( $before === $mail_calls, 'Invalid input must not send mail.' );
	$before_count = (int) wp_count_posts( 'fcc_order' )->publish;
	$result = fcc_order_run( array_replace( $base + array( 'selected_item' => 'prod_' . $product ), array( 'website_url' => 'spam' ) ) );
	fcc_order_check( $before === $mail_calls && $before_count === (int) wp_count_posts( 'fcc_order' )->publish, 'Honeypot must not create order/send mail.' );
	$mail_result = false;
	$result = fcc_order_run( $base + array( 'selected_item' => 'prod_' . $product, 'fcc_form_source' => 'home_inline' ) );
	$order = get_posts( array( 'post_type' => 'fcc_order', 'posts_per_page' => 1, 'orderby' => 'ID', 'order' => 'DESC' ) )[0];
	$created[] = $order->ID;
	fcc_order_check( ! empty( $result['success'] ) && 'failed' === get_post_meta( $order->ID, '_fcc_mail_status', true ), 'Saved order must retain failed-mail status.' );
	$before = $mail_calls;
	add_filter( 'wp_insert_post_empty_content', '__return_true' );
	$result = fcc_order_run( $base + array( 'selected_item' => 'prod_' . $product ) );
	remove_filter( 'wp_insert_post_empty_content', '__return_true' );
	fcc_order_check( empty( $result['success'] ) && $before === $mail_calls, 'Failed order storage must not send mail or report success.' );
} finally {
	remove_filter( 'wp_insert_post_empty_content', '__return_true' );
	foreach ( $created as $id ) { wp_delete_post( $id, true ); }
}
echo 'PASS: ' . $checks . ' server form checks (' . ( $ajax ? 'AJAX' : 'native POST' ) . '); email captured, no external messages sent.' . PHP_EOL;
