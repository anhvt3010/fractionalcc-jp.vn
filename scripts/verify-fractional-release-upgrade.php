<?php
/** Test release updates on a marked fixture; preserve every preexisting record/value. */
if ( 'cli' !== PHP_SAPI ) { exit( 1 ); }
$root = getenv( 'PROJECT_WORDPRESS_ROOT' );
if ( ! $root || ! preg_match( '#/fcc-release-qa-\d+-upgrade$#', str_replace( '\\', '/', $root ) ) ) { throw new RuntimeException( 'Isolated upgrade fixture required.' ); }
require $root . '/wp-load.php';
if ( ! get_option( 'project_core_fcc_test_install' ) ) { throw new RuntimeException( 'Marked fixture required.' ); }
$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) ); wp_set_current_user( $admins[0] );
$snapshot_path = $argv[2] ?? '';
if ( ! $snapshot_path ) { throw new RuntimeException( 'Snapshot file argument required.' ); }
function fcc_release_upgrade_snapshot() {
	global $wpdb;
	$snapshot = array( 'records' => array(), 'settings' => array() );
	foreach ( $wpdb->get_results( "SELECT ID,post_type,post_title,post_name,post_content,post_excerpt,post_status,menu_order FROM {$wpdb->posts} ORDER BY ID", ARRAY_A ) as $post ) { $snapshot['records'][ $post['ID'] ] = array( 'post' => $post, 'meta' => get_post_meta( $post['ID'] ) ); }
	foreach ( array_merge( project_core_fcc_snapshot_setting_names(), array( 'show_on_front', 'page_on_front', 'page_for_posts' ) ) as $name ) { $snapshot['settings'][ $name ] = (string) get_option( $name ); }
	return $snapshot;
}
if ( 'prepare' === $argv[1] ) {
	if ( '2.2.1' !== project_core_fcc_default_document()['version'] || is_wp_error( project_core_fcc_initialize() ) ) { throw new RuntimeException( 'Initialize the prior 2.2.1 ZIP first.' ); }
	$id = project_core_fcc_legacy_product_id( 1 );
	wp_update_post( array( 'ID' => $id, 'post_title' => 'QA edited product title', 'post_name' => 'qa-edited-product', 'menu_order' => 77 ) );
	foreach ( array( 'fcc_image' => 0, 'fcc_hoverImage' => 0, 'fcc_salePrice' => '', 'fcc_cardTags' => array(), 'fcc_product_seo_title' => 'QA edited SEO', 'fcc_product_seo_description' => '' ) as $name => $value ) { update_post_meta( $id, $name, $value ); }
	$other = project_core_fcc_legacy_product_id( 2 );
	wp_update_post( array( 'ID' => $other, 'post_content' => '<!-- wp:paragraph --><p>QA customer body; gallery intentionally removed.</p><!-- /wp:paragraph -->' ) );
	update_post_meta( $other, '_fcc_product_media_customized', 1 );
	$combo = project_core_fcc_posts( 'fcc_combo' )[0]->ID;
	update_post_meta( $combo, 'fcc_has_gift', '0' );
	update_post_meta( $combo, 'fcc_gift', 'QA retained gift text while disabled' );
	$home = project_core_fcc_page_id( 'home' );
	update_post_meta( $home, 'fcc_ui_brand_video_url', 'https://youtu.be/abcdefghijk' );
	update_post_meta( $home, 'fcc_ui_brand_video_file', 0 );
	update_post_meta( $home, 'fcc_ui_review_video_url', '' );
	update_post_meta( project_core_fcc_page_id( 'concerns' ), 'fcc_regimen_dom_nau', array() );
	update_option( 'page_on_front', project_core_fcc_page_id( 'catalog' ) );
	update_option( 'blogname', 'QA custom site name' );
	file_put_contents( $snapshot_path, wp_json_encode( fcc_release_upgrade_snapshot() ) );
	echo 'Prepared 2.2.1 fixture with edited/cleared content, gallery, video sources, disabled gift and Reading/settings.', PHP_EOL;
	exit;
}
if ( 'verify' !== $argv[1] || '3.0.0' !== project_core_fcc_default_document()['version'] ) { throw new RuntimeException( 'Install 3.0.0 ZIPs before verification.' ); }
$before = json_decode( file_get_contents( $snapshot_path ), true ); $checks = 0; $first_count = 0;
for ( $run = 0; $run < 2; ++$run ) {
	$result = project_core_fcc_initialize();
	if ( is_wp_error( $result ) || '3.0.0' !== get_option( 'project_core_fcc_seed_version' ) ) { throw new RuntimeException( 'Upgrade initialization failed.' ); }
	$after = fcc_release_upgrade_snapshot();
	if ( $before['settings'] !== $after['settings'] ) { throw new RuntimeException( 'Existing Reading/settings changed.' ); } ++$checks;
	foreach ( $before['records'] as $id => $record ) {
		if ( $record['post'] !== ( $after['records'][ $id ]['post'] ?? null ) ) { throw new RuntimeException( 'Existing record changed: ' . $id ); } ++$checks;
		foreach ( $record['meta'] as $key => $values ) {
			if ( $values !== ( $after['records'][ $id ]['meta'][ $key ] ?? null ) ) { throw new RuntimeException( 'Existing metadata changed: ' . $id . ' ' . $key ); } ++$checks;
		}
	}
	if ( $run && $first_count !== count( $after['records'] ) ) { throw new RuntimeException( 'Rerun created duplicate records.' ); }
	$first_count = count( $after['records'] );
	if ( '' !== project_core_fcc_combos()[0]['gift'] || 'https://www.youtube-nocookie.com/embed/abcdefghijk' !== project_child_fcc_home_video( 'brand' )['url'] ) { throw new RuntimeException( 'Edited gift/video behavior changed.' ); } ++$checks;
}
echo 'PASS: ', $checks, ' upgrade/rerun preservation checks, ', count( $before['records'] ), ' existing records retained; ', $first_count, ' records after adding missing data. Version 2.2.1 -> 3.0.0.', PHP_EOL;
