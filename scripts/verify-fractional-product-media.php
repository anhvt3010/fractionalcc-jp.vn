<?php
/** Exercise the real media save hook, permissions, seed reruns and rendered order. */
if ( 'cli' !== PHP_SAPI ) { exit( 1 ); }
$root = getenv( 'PROJECT_WORDPRESS_ROOT' );
if ( ! $root || realpath( $root ) === realpath( dirname( __DIR__ ) ) ) { throw new RuntimeException( 'Isolated PROJECT_WORDPRESS_ROOT required.' ); }
require $root . '/wp-load.php';
if ( ! get_option( 'project_core_fcc_test_install' ) ) { throw new RuntimeException( 'Marked fixture required.' ); }
set_exception_handler( static function ( $error ) { fwrite( STDERR, $error->getMessage() . ' at line ' . $error->getLine() . PHP_EOL ); exit( 1 ); } );
$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) ); wp_set_current_user( $admins[0] );
$checks = 0;
function fcc_media_check( $ok, $label ) { global $checks; if ( ! $ok ) { throw new RuntimeException( $label ); } ++$checks; }
$data = project_core_fcc_default_document();
foreach ( $data['products'] as $entry ) {
	$id = project_core_fcc_legacy_product_id( $entry['id'] );
	$gallery = project_core_fcc_gallery_ids( $id );
	fcc_media_check( 4 === count( $gallery ), 'Four supplied images available for every product.' );
	fcc_media_check( $entry['gallery'] === array_map( static function ( $image_id ) { return get_post_meta( $image_id, '_fcc_asset_source', true ); }, $gallery ), 'Product folder mapping and default gallery order match.' );
	foreach ( $gallery as $image_id ) { fcc_media_check( 'image/webp' === get_post_mime_type( $image_id ), 'Every new product photo is WebP.' ); }
}
$shared = project_core_fcc_page_id( 'shared' );
for ( $i = 1; $i <= 7; ++$i ) { fcc_media_check( 'image/webp' === get_post_mime_type( get_post_meta( $shared, 'fcc_review_avatar_' . $i, true ) ), 'Each avatar is editable, separate and WebP.' ); }
$id = project_core_fcc_legacy_product_id( 3 );
$before = array( 'content' => get_post_field( 'post_content', $id ), 'image' => get_post_meta( $id, 'fcc_image', true ), 'custom' => get_post_meta( $id, '_fcc_product_media_customized', true ) );
$gallery = project_core_fcc_gallery_ids( $id );
function fcc_media_submit( $id, $ids, $primary, $nonce = null ) {
	$_POST = array( 'fcc_product_gallery' => is_array( $ids ) ? implode( ',', $ids ) : $ids, 'fcc_product_primary' => (string) $primary );
	if ( null !== $nonce ) { $_POST['fcc_product_media_nonce'] = $nonce; }
	wp_update_post( array( 'ID' => $id, 'post_title' => get_the_title( $id ) ) );
}
try {
	$nonce = wp_create_nonce( 'fcc_product_media_' . $id );
	$reordered = array_reverse( $gallery ); $primary = $reordered[2];
	fcc_media_submit( $id, $reordered, $primary, $nonce );
	fcc_media_check( $reordered === project_core_fcc_gallery_ids( $id ) && $primary === (int) get_post_meta( $id, 'fcc_image', true ), 'Real save hook persists order independently of representative image.' );
	$product = project_core_fcc_product( $id );
	fcc_media_check( $product['image'] === wp_get_attachment_url( $primary ) && $product['gallery'] === array_map( 'wp_get_attachment_url', $reordered ), 'Read API respects saved order without moving representative to the front.' );
	$nonce_cases = array( null, 'invalid-nonce' );
	foreach ( $nonce_cases as $bad ) { fcc_media_submit( $id, $gallery, $gallery[0], $bad ); fcc_media_check( $reordered === project_core_fcc_gallery_ids( $id ), 'Missing/invalid nonce cannot alter gallery.' ); }
	foreach ( array( '1,-2', '999999999', implode( ',', array( $gallery[0], $gallery[0] ) ), '<script>', array( 'bad' ) ) as $bad ) {
		$_POST = array( 'fcc_product_gallery' => $bad, 'fcc_product_primary' => (string) $primary, 'fcc_product_media_nonce' => $nonce );
		project_core_fcc_save_product_media( $id );
		fcc_media_check( $reordered === project_core_fcc_gallery_ids( $id ) && $primary === (int) get_post_meta( $id, 'fcc_image', true ), 'Invalid IDs/type are rejected without partial photo changes.' );
	}
	fcc_media_submit( $id, $reordered, 999999999, $nonce );
	fcc_media_check( $primary === (int) get_post_meta( $id, 'fcc_image', true ), 'Representative must belong to selected gallery.' );
	$_POST = array(); project_core_fcc_initialize();
	fcc_media_check( $reordered === project_core_fcc_gallery_ids( $id ) && $primary === (int) get_post_meta( $id, 'fcc_image', true ), 'Defaults rerun preserves changed order and representative.' );
	fcc_media_check( ! project_core_fcc_is_seeded_gallery( $id ), 'Manual media choices cannot be replaced by a later catalog refresh.' );
	wp_set_current_user( 0 );
	fcc_media_submit( $id, $gallery, $gallery[0], wp_create_nonce( 'fcc_product_media_' . $id ) );
	fcc_media_check( $reordered === project_core_fcc_gallery_ids( $id ), 'Anonymous caller cannot change media even with its own nonce.' );
	wp_set_current_user( $admins[0] );
	$count = (int) wp_count_posts( 'attachment' )->inherit;
	fcc_media_submit( $id, array(), 0, wp_create_nonce( 'fcc_product_media_' . $id ) );
	$_POST = array(); project_core_fcc_initialize();
	fcc_media_check( ! project_core_fcc_gallery_ids( $id ) && ! get_post_meta( $id, 'fcc_image', true ), 'Clear all remains empty after defaults rerun.' );
	fcc_media_check( $count === (int) wp_count_posts( 'attachment' )->inherit, 'Removing an image from a product does not delete Media.' );
	$paragraph = '<!-- wp:paragraph --><p>QA preserved body</p><!-- /wp:paragraph -->';
	fcc_media_check( false !== strpos( project_core_fcc_gallery_content( $paragraph . $before['content'], $reordered ), 'QA preserved body' ), 'Non-gallery Gutenberg blocks survive gallery edits.' );
} finally {
	$_POST = array(); wp_set_current_user( $admins[0] );
	wp_update_post( wp_slash( array( 'ID' => $id, 'post_content' => $before['content'] ) ) );
	update_post_meta( $id, 'fcc_image', $before['image'] );
	if ( $before['custom'] ) { update_post_meta( $id, '_fcc_product_media_customized', $before['custom'] ); } else { delete_post_meta( $id, '_fcc_product_media_customized' ); }
	delete_transient( 'fcc_media_error_' . get_current_user_id() );
}
echo "PASS: {$checks} product photo/avatar/order/permission/clearing checks; fixture restored.\n";
