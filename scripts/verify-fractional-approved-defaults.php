<?php
/** Verify the approved snapshot on a clean, marked installation only. */
if ( 'cli' !== PHP_SAPI ) { exit; }
$root = getenv( 'PROJECT_WORDPRESS_ROOT' );
if ( ! $root || realpath( $root ) === realpath( dirname( __DIR__ ) ) ) { throw new RuntimeException( 'An isolated root is required.' ); }
require $root . '/wp-load.php';
if ( ! get_option( 'project_core_fcc_test_install' ) || get_option( 'project_core_fcc_seed_version' ) ) { throw new RuntimeException( 'A clean marked fixture is required.' ); }
$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) ); wp_set_current_user( $admins[0] );
$checks = 0;
function fcc_approved_check( $ok, $label ) { global $checks; if ( ! $ok ) { throw new RuntimeException( $label ); } ++$checks; }
function fcc_approved_normalize( $value ) { return is_array( $value ) ? array_map( 'fcc_approved_normalize', $value ) : (string) $value; }
$data = project_core_fcc_default_document();
$before = count( get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'posts_per_page' => -1 ) ) );
$bad = $data; $bad['regimens']['dom-nau'] = array( 'product_missing' );
fcc_approved_check( is_wp_error( project_core_fcc_validate_defaults( $bad ) ), 'Unknown regimen references rejected before any writes.' );
$bad = $data; $bad['media'][ array_key_first( $bad['media'] ) ]['sha256'] = str_repeat( '0', 64 );
fcc_approved_check( is_wp_error( project_core_fcc_validate_defaults( $bad ) ), 'Modified packaged media rejected.' );
$bad = $data; $bad['settings']['admin_email'] = 'not-exported@example.test';
fcc_approved_check( is_wp_error( project_core_fcc_validate_defaults( $bad ) ), 'Non-presentation settings cannot be imported.' );
fcc_approved_check( ! is_wp_error( project_core_fcc_initialize() ), 'Approved initialization succeeds.' );
$pages = get_option( 'project_core_fcc_pages' );
fcc_approved_check( 'approved' === project_core_fcc_site( 'content_status' ), 'Approved status is the default.' );
fcc_approved_check( '1.0.0' === get_option( 'project_core_fcc_seed_version' ), 'First release version recorded.' );
function fcc_approved_fields( $id, $fields ) {
	foreach ( $fields as $name => $field ) {
		$actual = get_post_meta( $id, $name, true );
		fcc_approved_check( metadata_exists( 'post', $id, $name ), 'Field exists: ' . $name );
		if ( 'image' === $field['type'] ) { fcc_approved_check( $field['value'] ? get_post_meta( $actual, '_fcc_asset_source', true ) === $field['value'] : ! $actual, 'Image field uses the approved image or stays cleared: ' . $name ); }
		elseif ( 'relationship' === $field['type'] ) { fcc_approved_check( array_map( 'project_core_fcc_snapshot_source', $field['value'] ) === array_map( 'intval', $actual ), 'Portable relationship restored.' ); }
		else { fcc_approved_check( fcc_approved_normalize( project_core_fcc_resolve_default( $field['value'] ) ) === fcc_approved_normalize( $actual ), 'Exact saved field value: ' . $name ); }
	}
}
foreach ( $data['pages'] as $scope => $page ) {
	$id = $pages[ $scope ]; $post = get_post( $id );
	fcc_approved_check( $post->post_title === $page['title'] && $post->post_name === $page['slug'], 'Exact page title and slug.' );
	fcc_approved_check( project_core_fcc_resolve_default( $page['content'] ) === $post->post_content, 'Page block content restored.' );
	fcc_approved_fields( $id, $page['fields'] );
}
foreach ( array( 'products' => 'product_', 'combos' => 'combo_' ) as $list => $prefix ) {
	foreach ( $data[ $list ] as $entry ) {
		$id = project_core_fcc_snapshot_source( $prefix . $entry['id'] ); $post = get_post( $id );
		fcc_approved_check( $post && $post->post_title === $entry['name'] && $post->post_name === $entry['slug'] && (int) $post->menu_order === $entry['menuOrder'], 'Catalog identity and ordering restored.' );
		fcc_approved_check( project_core_fcc_resolve_default( $entry['content'] ) === $post->post_content, 'Exact catalog Gutenberg content restored.' );
		foreach ( $entry as $name => $value ) {
			if ( in_array( $name, array( 'id', 'name', 'slug', 'menuOrder', 'content', 'excerpt', 'fields', 'gallery' ), true ) ) { continue; }
			$actual = get_post_meta( $id, 'fcc_' . $name, true );
			if ( 'image' === $name ) { fcc_approved_check( get_post_meta( $actual, '_fcc_asset_source', true ) === $value, 'Catalog image preserved.' ); }
			elseif ( 'productIds' === $name ) { fcc_approved_check( array_map( 'project_core_fcc_legacy_product_id', $value ) === array_map( 'intval', $actual ), 'Combo uses the correct products.' ); }
			elseif ( 'actives' === $name ) {
				fcc_approved_check( array_map( 'project_core_fcc_snapshot_source', array_column( $value, 'source' ) ) === array_map( 'intval', $actual ), 'Ingredient relationships and ordering restored.' );
				foreach ( $value as $active ) { $aid = project_core_fcc_snapshot_source( $active['source'] ); fcc_approved_check( get_post_field( 'post_title', $aid ) === $active['name'] && get_post_meta( $aid, 'fcc_tag', true ) === $active['tag'] && get_post_meta( $aid, 'fcc_description', true ) === $active['desc'], 'Ingredient content restored.' ); }
			} else { fcc_approved_check( fcc_approved_normalize( $actual ) === fcc_approved_normalize( project_core_fcc_resolve_default( $value ) ), 'Catalog value: ' . $name ); }
		}
		fcc_approved_fields( $id, $entry['fields'] );
	}
}
foreach ( $data['regimens'] as $slug => $sources ) { fcc_approved_check( array_map( 'project_core_fcc_snapshot_source', $sources ) === wp_list_pluck( project_core_fcc_regimen_posts( $slug, $pages['concerns'] ), 'ID' ), 'Approved regimen order restored: ' . $slug ); }
fcc_approved_check( array_column( $data['featured'], 'source' ) === array_map( static function ( $p ) { return get_post_meta( $p->ID, '_fcc_source_key', true ); }, project_core_fcc_featured_posts() ), 'Featured product order restored.' );
fcc_approved_check( $data['homeCatalog'] === array_map( static function ( $p ) { return get_post_meta( $p->ID, '_fcc_source_key', true ); }, project_core_fcc_selected_posts( 'home_catalog', 6 ) ), 'Home catalog order restored.' );
foreach ( $data['settings'] as $name => $value ) { fcc_approved_check( (string) $value === (string) get_option( $name ), 'Presentation setting: ' . $name ); }
$media = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'posts_per_page' => -1 ) );
fcc_approved_check( count( $media ) === count( $data['media'] ) && 0 === $before, 'Exactly the approved media imported; no duplicate uploads.' );
foreach ( $media as $post ) {
	$path = get_post_meta( $post->ID, '_fcc_asset_source', true ); $expected = $data['media'][ $path ];
	fcc_approved_check( hash_file( 'sha256', get_attached_file( $post->ID ) ) === $expected['sha256'], 'Media bytes restored.' );
	fcc_approved_check( get_post_meta( $post->ID, '_wp_attachment_image_alt', true ) === $expected['alt'] && $post->post_title === $expected['title'] && $post->post_excerpt === $expected['caption'] && $post->post_content === $expected['description'], 'Media text metadata restored.' );
}
// Leave the fixture pristine for browser comparisons; reversible checks restore their own edits.
$page = $pages['concerns']; $name = 'fcc_regimen_dom_nau'; $original = get_post_meta( $page, $name, true );
update_field( 'field_' . $name, array(), $page );
project_core_fcc_initialize();
fcc_approved_check( array() === get_post_meta( $page, $name, true ) && count( get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'posts_per_page' => -1 ) ) ) === count( $media ), 'Rerun preserves cleared regimen and creates no media duplicates.' );
update_field( 'field_' . $name, $original, $page );
$old_name = get_option( 'blogname' ); update_option( 'blogname', 'Customer edit' ); project_core_fcc_initialize();
fcc_approved_check( 'Customer edit' === get_option( 'blogname' ), 'Rerun preserves customer settings.' ); update_option( 'blogname', $old_name );
wp_set_current_user( 0 ); fcc_approved_check( is_wp_error( project_core_fcc_initialize() ), 'Anonymous initialization denied.' );
echo "PASS: {$checks} approved snapshot initialization/media/content/relationship checks.\n";
