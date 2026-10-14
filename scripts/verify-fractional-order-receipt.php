<?php
/** Verify order/receipt consistency on a marked isolated installation; mail is captured. */
if ( 'cli' !== PHP_SAPI ) { exit( 1 ); }
$root = getenv( 'PROJECT_WORDPRESS_ROOT' );
if ( ! $root || realpath( $root ) === realpath( dirname( __DIR__ ) ) ) { throw new RuntimeException( 'Isolated PROJECT_WORDPRESS_ROOT required.' ); }
define( 'DOING_AJAX', true );
require rtrim( $root, '/\\' ) . '/wp-load.php';
if ( ! get_option( 'project_core_fcc_test_install' ) ) { throw new RuntimeException( 'Marked fixture required.' ); }
set_exception_handler( static function ( $error ) { fwrite( STDERR, $error->getMessage() . ' at line ' . $error->getLine() . PHP_EOL ); exit( 1 ); } );
class FccReceiptVerificationStop extends RuntimeException {}
add_filter( 'wp_die_ajax_handler', static function () { return static function () { throw new FccReceiptVerificationStop(); }; } );
$mail = array();
add_filter( 'pre_wp_mail', static function ( $result, $attributes ) use ( &$mail ) { $mail[] = $attributes; return true; }, 999, 2 );
$checks = 0;
function fcc_receipt_check( $ok, $label ) { global $checks; if ( ! $ok ) { throw new RuntimeException( $label ); } ++$checks; }
function fcc_receipt_html( $url ) {
	$response = wp_remote_get( $url );
	fcc_receipt_check( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ), 'Receipt HTTP 200.' );
	$html = wp_remote_retrieve_body( $response );
	fcc_receipt_check( ! preg_match( '/Fatal error|PHP Warning/', $html ), 'No PHP diagnostics.' );
	$doc = new DOMDocument(); libxml_use_internal_errors( true ); $doc->loadHTML( '<?xml encoding="utf-8" ?>' . $html );
	return array( new DOMXPath( $doc ), $response );
}
function fcc_receipt_text( $xpath, $id ) { $node = $xpath->query( '//*[@id="' . $id . '"]' )->item( 0 ); return $node ? $node->textContent : ''; }
$_SERVER['REQUEST_METHOD'] = 'POST'; $_SERVER['REMOTE_ADDR'] = '192.0.2.45';
wp_set_current_user( 0 );
$base = array( '_fcc_nonce' => wp_create_nonce( 'fcc_order_action' ), 'customer_name' => 'RECEIPT QA', 'customer_phone' => '0900000000', 'agree_terms' => 'on', 'website_url' => '' );
$rate_key = 'fcc_order_rate_' . hash_hmac( 'sha256', inet_pton( $_SERVER['REMOTE_ADDR'] ), wp_salt( 'nonce' ) );
$created = array(); $cases = array(); $changed = array();
$keep = in_array( '--keep-for-browser', $argv, true );
function fcc_receipt_create( $reference ) {
	global $base, $created, $rate_key;
	delete_transient( $rate_key );
	$_POST = $base + array( 'selected_item' => $reference, 'selected_item_name' => 'Forged name and price' );
	ob_start(); try { project_core_fcc_handle_order_submission(); } catch ( FccReceiptVerificationStop $error ) {}
	$result = json_decode( ob_get_clean(), true );
	fcc_receipt_check( ! empty( $result['success'] ), 'Valid catalog order accepted.' );
	parse_str( wp_parse_url( $result['data']['redirectUrl'], PHP_URL_QUERY ), $query );
	fcc_receipt_check( isset( $query['key'] ) && 48 === strlen( $query['key'] ), 'Receipt URL has an unguessable key.' );
	$ids = get_posts( array( 'post_type' => 'fcc_order', 'post_status' => 'publish', 'posts_per_page' => 1, 'fields' => 'ids', 'orderby' => 'ID', 'order' => 'DESC' ) );
	$created[] = $ids[0];
	fcc_receipt_check( hash( 'sha256', $query['key'] ) === get_post_meta( $ids[0], '_fcc_receipt_key_hash', true ), 'Only key hash is stored.' );
	return array( 'id' => $ids[0], 'url' => $result['data']['redirectUrl'], 'query' => $query );
}
try {
	foreach ( array( 'prod' => 'fcc_product', 'combo' => 'fcc_combo' ) as $prefix => $type ) {
		foreach ( project_core_fcc_posts( $type ) as $post ) {
			$reference = $prefix . '_' . $post->ID;
			$expected = project_core_fcc_order_item( $reference );
			$case = fcc_receipt_create( $reference );
			$receipt = project_core_fcc_order_receipt( $case['query']['order'], $case['query']['key'] );
			fcc_receipt_check( $expected === $receipt['item'], 'Receipt matches ordered product/combo snapshot.' );
			fcc_receipt_check( $expected === get_post_meta( $case['id'], '_fcc_order_item_snapshot', true ), 'Snapshot saved without changing data types.' );
			$title = project_core_fcc_format_order_item( $expected );
			fcc_receipt_check( $title === get_post_meta( $case['id'], '_fcc_selected_item_name', true ), 'Admin name and price match receipt.' );
			fcc_receipt_check( false !== strpos( end( $GLOBALS['mail'] )['message'], esc_html( $title ) ), 'Email product and price match receipt.' );
			list( $xpath, $response ) = fcc_receipt_html( $case['url'] );
			fcc_receipt_check( $title === fcc_receipt_text( $xpath, 'order-product-val' ), 'Rendered name/price match order.' );
			$img = $xpath->query( '//*[@id="order-img-val"]' )->item( 0 );
			fcc_receipt_check( $img && $img->getAttribute( 'src' ) === $expected['image'], 'Rendered image is the selected item image.' );
			fcc_receipt_check( $expected['gift'] === fcc_receipt_text( $xpath, 'order-gift-val' ), 'Only actual item gift is rendered.' );
			fcc_receipt_check( '0900 ••• 000' === fcc_receipt_text( $xpath, 'order-phone-val' ), 'Phone remains masked.' );
			fcc_receipt_check( false !== strpos( wp_remote_retrieve_header( $response, 'cache-control' ), 'no-store' ) && 'no-referrer' === wp_remote_retrieve_header( $response, 'referrer-policy' ), 'Private receipt cannot be page cached or leak referrers.' );
			$case['expected'] = $expected; $case['title'] = $title; $cases[] = $case;
		}
	}
	$first = $cases[0]; $other = $cases[1];
	fcc_receipt_check( ! project_core_fcc_order_receipt( $first['query']['order'] ), 'Guests cannot enumerate receipts by order code.' );
	fcc_receipt_check( ! project_core_fcc_order_receipt( $first['query']['order'], $other['query']['key'] ), 'Another order key cannot show this receipt.' );
	fcc_receipt_check( ! project_core_fcc_order_receipt( array( $first['query']['order'] ), array( 'bad' ) ), 'Malformed URL parameters rejected.' );
	list( $xpath ) = fcc_receipt_html( remove_query_arg( 'key', $first['url'] ) );
	fcc_receipt_check( 0 === $xpath->query( '//*[@id="receipt-container"]' )->length, 'Missing key renders no customer/template receipt.' );
	foreach ( array( $first, end( $cases ) ) as $case ) {
		$id = $case['expected']['id'];
		$changed[ $id ] = array( 'title' => get_post_field( 'post_title', $id ), 'price' => get_post_meta( $id, 'fcc_salePrice', true ), 'image' => get_post_meta( $id, 'fcc_image', true ), 'gift' => get_post_meta( $id, 'fcc_gift', true ) );
		wp_update_post( array( 'ID' => $id, 'post_title' => 'QA catalog changed after checkout' ) );
		update_post_meta( $id, 'fcc_salePrice', 123 ); update_post_meta( $id, 'fcc_image', 0 ); update_post_meta( $id, 'fcc_gift', 'QA later gift' );
		list( $xpath ) = fcc_receipt_html( $case['url'] );
		fcc_receipt_check( $case['title'] === fcc_receipt_text( $xpath, 'order-product-val' ) && $case['expected']['gift'] === fcc_receipt_text( $xpath, 'order-gift-val' ), 'Later catalog changes do not rewrite receipt name/price/gift.' );
		fcc_receipt_check( $xpath->query( '//*[@id="order-img-val"]' )->item( 0 )->getAttribute( 'src' ) === $case['expected']['image'], 'Later image change does not rewrite receipt image.' );
	}
	$id = $first['expected']['id'];
	update_post_meta( $id, 'fcc_salePrice', 0 );
	$zero = fcc_receipt_create( 'prod_' . $id );
	list( $xpath ) = fcc_receipt_html( $zero['url'] );
	fcc_receipt_check( false !== strpos( fcc_receipt_text( $xpath, 'order-product-val' ), '(0 đ)' ) && 0 === $xpath->query( '//*[@id="order-img-val"]' )->length, 'Zero price and cleared image do not use fake defaults.' );
	update_post_meta( $id, 'fcc_salePrice', '' );
	$unknown = fcc_receipt_create( 'prod_' . $id );
	fcc_receipt_check( null === project_core_fcc_order_receipt( $unknown['query']['order'], $unknown['query']['key'] )['item']['price'], 'Unknown price stays unknown.' );
	delete_post_meta( $first['id'], '_fcc_order_item_snapshot' );
	$legacy = project_core_fcc_saved_order_item( $first['id'] );
	fcc_receipt_check( $legacy['name'] === $first['expected']['name'] && $legacy['price'] === $first['expected']['price'] && '' === $legacy['gift'], 'Legacy order keeps saved name/price and gets no template promotion.' );
	delete_post_meta( $first['id'], '_fcc_receipt_key_hash' );
	$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) ); wp_set_current_user( $admins[0] );
	fcc_receipt_check( ! empty( project_core_fcc_order_receipt( $first['query']['order'] ) ), 'Authorized manager can inspect legacy orders.' );
	wp_set_current_user( 0 ); fcc_receipt_check( ! project_core_fcc_order_receipt( $first['query']['order'] ), 'Legacy code is private to managers.' );
} finally {
	foreach ( $changed as $id => $before ) { wp_update_post( array( 'ID' => $id, 'post_title' => $before['title'] ) ); foreach ( array( 'price' => 'salePrice', 'image' => 'image', 'gift' => 'gift' ) as $key => $field ) { update_post_meta( $id, 'fcc_' . $field, $before[ $key ] ); } }
	delete_transient( $rate_key ); delete_option( $rate_key . '_lock' );
	if ( ! $keep ) { foreach ( $created as $id ) { wp_delete_post( $id, true ); } }
}
if ( $keep ) {
	// Restore a valid first-product receipt after exercising legacy handling on its earlier order.
	$fresh = fcc_receipt_create( 'prod_' . $cases[0]['expected']['id'] );
	$fresh['expected'] = project_core_fcc_order_item( 'prod_' . $cases[0]['expected']['id'] );
	$fresh['title'] = project_core_fcc_format_order_item( $fresh['expected'] );
	$cases[0] = $fresh;
	file_put_contents( sys_get_temp_dir() . '/fcc-receipt-browser-cases.json', wp_json_encode( $cases ) );
}
echo "PASS: {$checks} order snapshot/receipt/render/privacy checks across ten products and one combo; mail captured only.\n";
