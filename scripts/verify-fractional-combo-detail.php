<?php
/** Combo routing, native media saves and initializer preservation on a marked fixture. */
if ( 'cli' !== PHP_SAPI ) { exit( 1 ); }
$root = getenv( 'PROJECT_WORDPRESS_ROOT' );
if ( ! $root || realpath( $root ) === realpath( dirname( __DIR__ ) ) ) { throw new RuntimeException( 'Isolated PROJECT_WORDPRESS_ROOT required.' ); }
require $root . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/screen.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-screen.php';
require_once ABSPATH . 'wp-admin/includes/template.php';
set_exception_handler( static function ( $error ) { fwrite( STDERR, $error->getMessage() . ' at line ' . $error->getLine() . PHP_EOL ); exit( 1 ); } );
if ( ! get_option( 'project_core_fcc_test_install' ) ) { throw new RuntimeException( 'Marked fixture required.' ); }
$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
wp_set_current_user( $admins[0] );
add_filter( 'pre_wp_mail', '__return_true' );
set_error_handler( static function ( $severity, $message ) { if ( error_reporting() & $severity ) { throw new RuntimeException( $message ); } return false; } );
$checks = 0;
function fcc_combo_detail_check( $ok, $label ) {
	global $checks;
	if ( ! $ok ) { throw new RuntimeException( $label ); }
	++$checks;
}
$combo = project_core_fcc_featured_combo();
$id = $combo['id'];
$before = array( 'content' => get_post_field( 'post_content', $id ), 'meta' => get_post_meta( $id ) );
$ids = project_core_fcc_gallery_ids( $id );
fcc_combo_detail_check( 4 === count( $ids ), 'Existing representative plus three supplied images.' );
fcc_combo_detail_check( get_post_type_object( 'fcc_combo' )->public && get_post_type_object( 'fcc_combo' )->publicly_queryable, 'Published combos have public routes.' );
fcc_combo_detail_check( false !== strpos( get_permalink( $id ), '/combo/' ), 'Combo permalink has its own namespace.' );
fcc_combo_detail_check( ! project_core_fcc_combo( project_core_fcc_legacy_product_id( 1 ) ), 'Product IDs cannot resolve as combos.' );
do_action( 'add_meta_boxes_fcc_combo', get_post( $id ) );
fcc_combo_detail_check( isset( $GLOBALS['wp_meta_boxes']['fcc_combo']['normal']['high']['fcc-product-media'] ), 'Combo uses the shared native media box.' );
ob_start(); project_core_fcc_render_product_media( get_post( $id ) ); $html = ob_get_clean();
fcc_combo_detail_check( false !== strpos( $html, 'fcc_product_gallery' ) && false !== strpos( $html, 'fcc_product_media_nonce' ), 'Gallery control and nonce are rendered.' );
$nonce = wp_create_nonce( 'fcc_product_media_' . $id );
$sessions = WP_Session_Tokens::get_instance( $admins[0] );
$expires = time() + 600; $token = $sessions->create( $expires );
$cookies = array(
	new WP_Http_Cookie( array( 'name' => AUTH_COOKIE, 'value' => wp_generate_auth_cookie( $admins[0], $expires, 'auth', $token ) ) ),
	new WP_Http_Cookie( array( 'name' => LOGGED_IN_COOKIE, 'value' => wp_generate_auth_cookie( $admins[0], $expires, 'logged_in', $token ) ) ),
);
function fcc_combo_detail_submit( $id, $ids, $primary, $hover, $nonce ) {
	$_POST = array( 'fcc_product_gallery' => implode( ',', $ids ), 'fcc_product_primary' => (string) $primary, 'fcc_product_hover' => (string) $hover, 'fcc_product_media_nonce' => $nonce );
	wp_update_post( array( 'ID' => $id, 'post_title' => get_the_title( $id ) ) );
}
try {
	// Simulate an upgrade from an old image-only combo, preserving its chosen representative.
	delete_post_meta( $id, '_fcc_gallery_initialized' );
	wp_update_post( array( 'ID' => $id, 'post_content' => '' ) );
	fcc_combo_detail_check( ! is_wp_error( project_core_fcc_initialize() ) && $ids === project_core_fcc_gallery_ids( $id ), 'Upgrade initializes the previously empty gallery.' );
	fcc_combo_detail_check( $combo['image'] === project_core_fcc_combo( $id )['image'], 'Gallery upgrade preserves the existing representative.' );
	$paragraph = '<!-- wp:paragraph --><p>QA preserved combo copy</p><!-- /wp:paragraph -->';
	wp_update_post( wp_slash( array( 'ID' => $id, 'post_content' => $paragraph . get_post_field( 'post_content', $id ) ) ) );
	$reordered = array_reverse( $ids );
	fcc_combo_detail_submit( $id, $reordered, $reordered[2], $reordered[0], $nonce );
	fcc_combo_detail_check( $reordered === project_core_fcc_gallery_ids( $id ), 'Real combo save hook persists reordered thumbnails.' );
	$read = project_core_fcc_combo( $id );
	fcc_combo_detail_check( wp_get_attachment_url( $reordered[2] ) === $read['image'] && wp_get_attachment_url( $reordered[0] ) === $read['hoverImage'], 'Representative and hover are independent of gallery order.' );
	fcc_combo_detail_check( array_map( 'wp_get_attachment_url', $reordered ) === $read['gallery'], 'Read API respects the saved order.' );
	fcc_combo_detail_check( false !== strpos( get_post_field( 'post_content', $id ), 'QA preserved combo copy' ), 'Saving gallery preserves unrelated content blocks.' );
	foreach ( array( '', 'bad-nonce' ) as $bad_nonce ) {
		fcc_combo_detail_submit( $id, $ids, $ids[0], 0, $bad_nonce );
		fcc_combo_detail_check( $reordered === project_core_fcc_gallery_ids( $id ), 'Missing/invalid nonce cannot change gallery.' );
	}
	foreach ( array( '999999999', '1,-2', $ids[0] . ',' . $ids[0], '<script>', array( 'bad' ) ) as $bad_ids ) {
		$_POST = array( 'fcc_product_gallery' => $bad_ids, 'fcc_product_primary' => (string) $reordered[2], 'fcc_product_media_nonce' => $nonce );
		project_core_fcc_save_product_media( $id );
		fcc_combo_detail_check( $reordered === project_core_fcc_gallery_ids( $id ), 'Invalid image IDs/types cannot change combo gallery.' );
	}
	fcc_combo_detail_submit( $id, $ids, 999999999, 0, $nonce );
	fcc_combo_detail_check( $reordered === project_core_fcc_gallery_ids( $id ), 'Representative must belong to gallery.' );
	wp_set_current_user( 0 );
	fcc_combo_detail_submit( $id, $ids, $ids[0], 0, wp_create_nonce( 'fcc_product_media_' . $id ) );
	fcc_combo_detail_check( $reordered === project_core_fcc_gallery_ids( $id ), 'Anonymous caller cannot save images.' );
	wp_set_current_user( $admins[0] ); $_POST = array();
	project_core_fcc_initialize();
	fcc_combo_detail_check( $reordered === project_core_fcc_gallery_ids( $id ), 'Initializer rerun preserves gallery edits.' );
	$count = (int) wp_count_posts( 'attachment' )->inherit;
	fcc_combo_detail_submit( $id, array(), 0, 0, $nonce );
	$_POST = array(); project_core_fcc_initialize();
	fcc_combo_detail_check( ! project_core_fcc_gallery_ids( $id ) && ! get_post_meta( $id, 'fcc_image', true ), 'Explicitly cleared gallery stays cleared on rerun.' );
	fcc_combo_detail_check( $count === (int) wp_count_posts( 'attachment' )->inherit, 'Clearing a gallery does not remove Media or duplicate it on rerun.' );
	$editor = wp_remote_get( admin_url( 'post.php?post=' . $id . '&action=edit' ), array( 'cookies' => $cookies, 'timeout' => 25 ) );
	fcc_combo_detail_check( ! is_wp_error( $editor ) && 200 === wp_remote_retrieve_response_code( $editor ), 'Authenticated combo editor loads.' );
	$editor_html = wp_remote_retrieve_body( $editor );
	fcc_combo_detail_check( false !== strpos( $editor_html, 'product-media.js' ) && false !== strpos( $editor_html, 'fcc_product_gallery' ), 'Combo editor loads the native media controls and script.' );
	fcc_combo_detail_check( false === strpos( $editor_html, 'name="acf[field_fcc_combo_image]"' ) && false === strpos( $editor_html, 'name="acf[field_fcc_combo_hoverImage]"' ), 'One media editor owns image controls without conflicting ACF inputs.' );
	$dom = new DOMDocument(); @$dom->loadHTML( $editor_html ); $xp = new DOMXPath( $dom );
	$body = array( 'action' => 'editpost', 'post_ID' => $id, 'post_type' => 'fcc_combo', 'post_status' => 'publish', 'post_title' => get_the_title( $id ), 'fcc_product_gallery' => implode( ',', $reordered ), 'fcc_product_primary' => $reordered[2], 'fcc_product_hover' => $reordered[0] );
	foreach ( array( '_wpnonce', 'fcc_product_media_nonce' ) as $name ) {
		$input = $xp->query( '//input[@name="' . $name . '"]' )->item( 0 );
		fcc_combo_detail_check( (bool) $input, 'Native editor nonce exists: ' . $name );
		$body[ $name ] = $input->getAttribute( 'value' );
	}
	$saved = wp_remote_post( admin_url( 'post.php' ), array( 'body' => $body, 'cookies' => $cookies, 'timeout' => 25, 'redirection' => 0 ) );
	clean_post_cache( $id );
	fcc_combo_detail_check( ! is_wp_error( $saved ) && 302 === wp_remote_retrieve_response_code( $saved ) && $reordered === project_core_fcc_gallery_ids( $id ), 'Real authenticated editor POST persists selected gallery order.' );
	fcc_combo_detail_check( (int) $reordered[2] === (int) get_post_meta( $id, 'fcc_image', true ) && (int) $reordered[0] === (int) get_post_meta( $id, 'fcc_hoverImage', true ), 'Native HTTP save persists representative and hover.' );
} finally {
	$_POST = array(); wp_set_current_user( $admins[0] );
	wp_update_post( wp_slash( array( 'ID' => $id, 'post_content' => $before['content'] ) ) );
	foreach ( array( 'fcc_image', '_fcc_image', 'fcc_hoverImage', '_fcc_hoverImage', '_fcc_gallery_initialized', '_fcc_product_media_customized' ) as $key ) {
		delete_post_meta( $id, $key );
		foreach ( $before['meta'][ $key ] ?? array() as $value ) { add_post_meta( $id, $key, maybe_unserialize( $value ) ); }
	}
	delete_transient( 'fcc_media_error_' . get_current_user_id() );
	$sessions->destroy( $token );
}
$response = wp_remote_get( get_permalink( $id ) );
fcc_combo_detail_check( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ), 'Published combo route responds HTTP 200.' );
$html = wp_remote_retrieve_body( $response );
fcc_combo_detail_check( ! preg_match( '/(?:Warning:|Fatal error:|\{\{fcc_)/', $html ), 'No PHP warnings or unresolved portable content.' );
$dom = new DOMDocument(); @$dom->loadHTML( '<?xml encoding="UTF-8">' . $html ); $xp = new DOMXPath( $dom );
fcc_combo_detail_check( 1 === $xp->query( '//h1' )->length && 4 === $xp->query( '//*[@id="pd-gallery-thumbs"]//button' )->length, 'Shared detail title and all four thumbnail buttons render.' );
fcc_combo_detail_check( 1 === $xp->query( '//dialog[@id="pd-image-dialog"]' )->length, 'Shared enlarged-image dialog renders.' );
fcc_combo_detail_check( 1 === $xp->query( '//link[@rel="canonical"]' )->length && 1 === $xp->query( '//meta[@name="description"]' )->length, 'One canonical and description, using existing SEO ownership.' );
fcc_combo_detail_check( 'combo_' . $id === $xp->query( '//*[@id="pd-buy-now-btn"]' )->item( 0 )->getAttribute( 'data-product-select' ), 'Buy action selects the queried combo.' );
fcc_combo_detail_check( 2 === $xp->query( '//section[@aria-labelledby="combo-products-title"]//*[@data-type="serum"]' )->length, 'Constituent product cards render with their own detail links.' );
foreach ( project_core_fcc_gallery_ids( $id ) as $image_id ) {
	$thumb = wp_get_attachment_image_url( $image_id, 'thumbnail' );
	fcc_combo_detail_check( 200 === wp_remote_retrieve_response_code( wp_remote_get( $thumb ) ), 'Thumbnail image responds HTTP 200.' );
}
echo "PASS: {$checks} combo detail/routing/media/permission/upgrade/HTTP checks; fixture restored.\n";
