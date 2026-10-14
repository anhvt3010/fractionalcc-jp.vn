<?php
/** Test an existing 1.0.4 fixture with representative images, gallery edits and old orders. */
if ( 'cli' !== PHP_SAPI ) { exit( 1 ); }
$root = getenv( 'PROJECT_WORDPRESS_ROOT' );
if ( ! $root || realpath( $root ) === realpath( dirname( __DIR__ ) ) ) { throw new RuntimeException( 'Isolated PROJECT_WORDPRESS_ROOT required.' ); }
require $root . '/wp-load.php';
if ( ! get_option( 'project_core_fcc_test_install' ) || get_option( 'project_core_fcc_media_version' ) ) { throw new RuntimeException( 'Marked pre-update fixture required.' ); }
set_exception_handler( static function ( $error ) { fwrite( STDERR, $error->getMessage() . ' at line ' . $error->getLine() . PHP_EOL ); exit( 1 ); } );
$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) ); wp_set_current_user( $admins[0] );
$checks = 0;
function fcc_upgrade_check( $ok, $label ) { global $checks; if ( ! $ok ) { throw new RuntimeException( $label ); } ++$checks; }
$before = array();
foreach ( project_core_fcc_posts( 'fcc_product' ) as $post ) { $before[ $post->ID ] = array( 'content' => $post->post_content, 'image' => get_post_meta( $post->ID, 'fcc_image', true ) ); }
$clear = project_core_fcc_legacy_product_id( 10 );
$custom = project_core_fcc_legacy_product_id( 9 );
update_post_meta( $clear, 'fcc_image', 0 );
wp_update_post( array( 'ID' => $clear, 'post_content' => '<!-- wp:paragraph --><p>QA customer copy</p><!-- /wp:paragraph -->' ) );
update_post_meta( $custom, '_fcc_product_media_customized', 1 );
$old_snapshot = project_core_fcc_order_item( 'prod_' . project_core_fcc_legacy_product_id( 1 ) );
$order = wp_insert_post( array( 'post_type' => 'fcc_order', 'post_status' => 'publish', 'post_title' => 'MEDIA QA order' ) );
update_post_meta( $order, '_fcc_order_item_snapshot', $old_snapshot );
try {
	fcc_upgrade_check( ! is_wp_error( project_core_fcc_initialize() ), 'Existing site photo update succeeds.' );
	$data = project_core_fcc_default_document();
	foreach ( $data['products'] as $entry ) {
		$id = project_core_fcc_legacy_product_id( $entry['id'] );
		if ( in_array( $id, array( $clear, $custom ), true ) ) { continue; }
		fcc_upgrade_check( $entry['image'] === get_post_meta( get_post_meta( $id, 'fcc_image', true ), '_fcc_asset_source', true ), 'Unedited default representative image updated.' );
		fcc_upgrade_check( $entry['gallery'] === array_map( static function ( $image_id ) { return get_post_meta( $image_id, '_fcc_asset_source', true ); }, project_core_fcc_gallery_ids( $id ) ), 'Unedited default gallery updated to ordered thumbnails.' );
	}
	fcc_upgrade_check( 0 === (int) get_post_meta( $clear, 'fcc_image', true ) && false !== strpos( get_post_field( 'post_content', $clear ), 'QA customer copy' ) && ! project_core_fcc_gallery_ids( $clear ), 'Explicitly cleared image/gallery and custom blocks preserved.' );
	fcc_upgrade_check( (int) $before[ $custom ]['image'] === (int) get_post_meta( $custom, 'fcc_image', true ) && $before[ $custom ]['content'] === get_post_field( 'post_content', $custom ), 'Explicit media choices preserved.' );
	fcc_upgrade_check( $old_snapshot === project_core_fcc_saved_order_item( $order ), 'Old order snapshot retains its original image.' );
	$count = (int) wp_count_posts( 'attachment' )->inherit;
	project_core_fcc_initialize();
	fcc_upgrade_check( $count === (int) wp_count_posts( 'attachment' )->inherit, 'Rerun imports no duplicate images.' );
	foreach ( $data['products'][9]['gallery'] as $path ) {
		$ids = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'fields' => 'ids', 'meta_key' => '_fcc_asset_source', 'meta_value' => $path ) );
		fcc_upgrade_check( 1 === count( $ids ), 'New photos are available even when the old selection was preserved.' );
	}
} finally {
	foreach ( array( $clear, $custom ) as $id ) { wp_update_post( wp_slash( array( 'ID' => $id, 'post_content' => $before[ $id ]['content'] ) ) ); update_post_meta( $id, 'fcc_image', $before[ $id ]['image'] ); delete_post_meta( $id, '_fcc_product_media_customized' ); }
	wp_delete_post( $order, true );
	delete_option( 'project_core_fcc_media_version' );
	project_core_fcc_initialize();
}
echo "PASS: {$checks} safe photo upgrade/history/edit-preservation checks.\n";
