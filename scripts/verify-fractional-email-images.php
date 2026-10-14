<?php
/** Verify actual WordPress/PHPMailer output without connecting to any mail server. */
if ( 'cli' !== PHP_SAPI ) { exit( 1 ); }
$root = getenv( 'PROJECT_WORDPRESS_ROOT' );
if ( ! $root || realpath( $root ) === realpath( dirname( __DIR__ ) ) ) { throw new RuntimeException( 'Isolated PROJECT_WORDPRESS_ROOT required.' ); }
require rtrim( $root, '/\\' ) . '/wp-load.php';
if ( ! get_option( 'project_core_fcc_test_install' ) ) { throw new RuntimeException( 'Marked fixture required.' ); }
set_exception_handler( static function ( $error ) { fwrite( STDERR, $error->getMessage() . ' at line ' . $error->getLine() . PHP_EOL ); exit( 1 ); } );
require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
require_once ABSPATH . WPINC . '/class-wp-phpmailer.php';
class FccImageVerificationMailer extends WP_PHPMailer {
	public $messages = array();
	public function send() {
		if ( ! $this->preSend() ) { return false; }
		$this->messages[] = array( 'html' => $this->Body, 'mime' => $this->getSentMIMEMessage(), 'attachments' => $this->getAttachments() );
		return true;
	}
}
$GLOBALS['phpmailer'] = new FccImageVerificationMailer( true );
remove_filter( 'pre_wp_mail', '__return_true', PHP_INT_MAX );
add_filter( 'wp_mail_from', static function () { return 'qa@example.invalid'; }, PHP_INT_MAX );
$checks = 0;
function fcc_image_check( $ok, $label ) {
	global $checks;
	if ( ! $ok ) { throw new RuntimeException( $label ); }
	++$checks;
}
$assets = array();
function fcc_image_message( $expected_product = '' ) {
	global $assets;
	$message = end( $GLOBALS['phpmailer']->messages );
	fcc_image_check( ! empty( $message ), 'WordPress reached the capture mailer.' );
	fcc_image_check( ! $message['attachments'], 'No regular or embedded attachments.' );
	fcc_image_check( ! preg_match( '/Content-(ID|Disposition):|multipart\/(related|mixed)/i', $message['mime'] ), 'MIME contains no image parts or attachment disposition.' );
	fcc_image_check( false === strpos( $message['html'], 'cid:' ) && false === strpos( $message['html'], 'data:image' ), 'HTML does not depend on embedded images.' );
	$doc = new DOMDocument();
	libxml_use_internal_errors( true );
	$doc->loadHTML( '<?xml encoding="utf-8" ?>' . $message['html'] );
	$images = $doc->getElementsByTagName( 'img' );
	fcc_image_check( ( $expected_product ? 3 : 2 ) === $images->length, 'Expected two logos and optional selected product.' );
	$brand = project_core_fcc_email_brand_images();
	fcc_image_check( $brand['fractional'] === $images->item( 0 )->getAttribute( 'src' ) && $brand['buidang'] === $images->item( 1 )->getAttribute( 'src' ), 'Both PNG logos use the active theme URL.' );
	if ( $expected_product ) { fcc_image_check( $expected_product === $images->item( 2 )->getAttribute( 'src' ), 'Email uses the order-time image URL, not current catalog image.' ); }
	foreach ( $images as $image ) {
		$url = $image->getAttribute( 'src' );
		fcc_image_check( (bool) preg_match( '#^https?://#', $url ), 'Every image has an absolute HTTP(S) URL.' );
		$assets[ $url ] = true;
	}
	return $message;
}
$order = array( 'post_id' => 0, 'order_code' => 'FCC-123456', 'name' => 'EMAIL QA <script>alert(1)</script>', 'phone' => '0900000000', 'product' => '', 'address' => 'KIỂM TRA KỸ THUẬT', 'note' => 'QA <img src=x onerror=alert(1)>' );
$cases = 0;
foreach ( array( 'prod' => 'fcc_product', 'combo' => 'fcc_combo' ) as $prefix => $type ) {
	foreach ( project_core_fcc_posts( $type ) as $post ) {
		$item = project_core_fcc_order_item( $prefix . '_' . $post->ID );
		$order['item_snapshot'] = $item;
		$order['product'] = project_core_fcc_format_order_item( $item );
		fcc_image_check( project_core_fcc_send_order_email( $order ), 'Order email accepted by capture mailer.' );
		$message = fcc_image_message( $item['image'] );
		fcc_image_check( false !== strpos( $message['html'], esc_html( $order['product'] ) ), 'Product and price are unchanged.' );
		fcc_image_check( false === strpos( $message['html'], '<script>' ) && false === strpos( $message['html'], '<img src=x' ), 'Customer markup remains escaped.' );
		++$cases;
	}
}
fcc_image_check( 11 === $cases, 'All ten products and one combo exercised.' );
foreach ( array( '', 'javascript:alert(1)', 'data:image/png;base64,AAAA' ) as $url ) {
	$order['item_snapshot']['image'] = $url;
	fcc_image_check( project_core_fcc_send_order_email( $order ), 'Order with absent/unsafe image still sends.' );
	fcc_image_message();
}
$order['item_snapshot']['image'] = home_url( '/wp-content/themes/project-child/assets/fractional/assets/images/products/needle-serum-a.png?qa=1&version=2' );
fcc_image_check( project_core_fcc_send_order_email( $order ), 'Snapshot URL with query string sends.' );
$preview = fcc_image_message( $order['item_snapshot']['image'] );
file_put_contents( dirname( __DIR__ ) . '/scratch/mail-preview.html', $preview['html'] );
$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
wp_set_current_user( $admins[0] );
$_POST = array( 'fcc_test_smtp' => '1', 'test_email' => 'qa@example.invalid', 'fcc_smtp_nonce' => wp_create_nonce( 'fcc_smtp_settings_nonce' ) );
$_REQUEST = $_POST;
ob_start();
project_core_fcc_render_smtp_page();
$admin_html = ob_get_clean();
fcc_image_message();
fcc_image_check( false !== strpos( $admin_html, 'Gửi email thử nghiệm thành công' ), 'Admin SMTP test reports captured success.' );
foreach ( array_keys( $assets ) as $url ) {
	$response = wp_remote_get( $url );
	fcc_image_check( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ), 'Public image HTTP 200: ' . $url );
	fcc_image_check( 0 === strpos( wp_remote_retrieve_header( $response, 'content-type' ), 'image/' ), 'Public URL serves an image: ' . $url );
}
echo "PASS: {$checks} email checks; {$cases} catalog cases, absent/unsafe/query-string image URLs and admin SMTP test; MIME has zero attachments; all image URLs HTTP 200. No mail sent.\n";
