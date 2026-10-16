<?php
/** Compare the approved portable export with the working CMS without importing or saving anything. */
if ( 'cli' !== PHP_SAPI ) { exit( 1 ); }
require dirname( __DIR__ ) . '/wp-load.php';
$data = project_core_fcc_default_document(); $checks = 0; $media_ids = array(); $source_ids = array();
function fcc_export_check( $ok, $label ) { global $checks; if ( ! $ok ) { throw new RuntimeException( $label ); } ++$checks; }
function fcc_export_normalize( $value ) { return is_array( $value ) ? array_map( 'fcc_export_normalize', $value ) : (string) $value; }
$hashes = array();
foreach ( get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'posts_per_page' => -1 ) ) as $attachment ) {
	$file = get_attached_file( $attachment->ID );
	if ( is_readable( $file ) ) { $hashes[ hash_file( 'sha256', $file ) ] = $attachment->ID; }
}
foreach ( $data['media'] as $path => $expected ) {
	$id = $hashes[ $expected['sha256'] ] ?? 0;
	fcc_export_check( $id > 0, 'Approved media exists in the CMS: ' . $path );
	$media_ids[ $path ] = $id; $post = get_post( $id );
	fcc_export_check( $post->post_title === $expected['title'] && $post->post_excerpt === $expected['caption'] && $post->post_content === $expected['description'] && get_post_meta( $id, '_wp_attachment_image_alt', true ) === $expected['alt'], 'Media metadata matches the CMS.' );
}
function fcc_export_resolve_readonly( $value ) {
	global $media_ids;
	if ( is_array( $value ) ) { return array_map( 'fcc_export_resolve_readonly', $value ); }
	if ( ! is_string( $value ) ) { return $value; }
	$value = preg_replace_callback( '/("?)\{\{fcc_media_(id|url):([^}]+)\}\}\1/', static function ( $match ) use ( $media_ids ) {
		$id = $media_ids[ $match[3] ] ?? 0;
		fcc_export_check( $id > 0, 'Portable media reference resolves read-only.' );
		return 'id' === $match[2] ? (string) $id : $match[1] . wp_get_attachment_url( $id ) . $match[1];
	}, $value );
	return str_replace( '{{fcc_site_url}}', home_url(), $value );
}
function fcc_export_check_fields( $id, $fields ) {
	global $media_ids;
	foreach ( $fields as $name => $field ) {
		if ( ! metadata_exists( 'post', $id, $name ) ) {
			$definition = acf_get_field( $field['key'] ?? 'field_' . $name );
			fcc_export_check( fcc_export_normalize( $field['value'] ) === fcc_export_normalize( $definition['default_value'] ?? '' ), 'Unsaved field matches its editor default: ' . $name );
			continue;
		}
		$actual = get_post_meta( $id, $name, true ); $expected = $field['value'];
		if ( in_array( $field['type'], array( 'image', 'file' ), true ) ) { $expected = $expected ? $media_ids[ $expected ] : ''; $actual = $actual ?: ''; }
		elseif ( 'relationship' === $field['type'] ) { $expected = array_map( 'project_core_fcc_snapshot_source', $expected ); }
		else { $expected = fcc_export_resolve_readonly( $expected ); }
		fcc_export_check( fcc_export_normalize( $actual ) === fcc_export_normalize( $expected ), 'Saved CMS field matches: ' . $id . ' ' . $name );
	}
}
foreach ( $data['pages'] as $scope => $entry ) {
	$id = project_core_fcc_page_id( $scope ); $post = get_post( $id );
	fcc_export_check( $post->post_title === $entry['title'] && $post->post_name === $entry['slug'] && fcc_export_resolve_readonly( $entry['content'] ) === $post->post_content, 'CMS page identity/content matches.' );
	fcc_export_check_fields( $id, $entry['fields'] );
}
foreach ( array( 'products' => 'product_', 'combos' => 'combo_' ) as $group => $prefix ) {
	foreach ( $data[ $group ] as $entry ) {
		$id = project_core_fcc_snapshot_source( $prefix . $entry['id'] ); $post = get_post( $id );
		fcc_export_check( $post && $post->post_title === $entry['name'] && $post->post_name === $entry['slug'] && (int) $post->menu_order === $entry['menuOrder'] && $post->post_content === fcc_export_resolve_readonly( $entry['content'] ), 'CMS catalog identity/gallery/body matches.' );
		foreach ( $entry as $name => $value ) {
			if ( in_array( $name, array( 'id', 'name', 'slug', 'menuOrder', 'content', 'excerpt', 'fields' ), true ) ) { continue; }
			if ( 'gallery' === $name ) { fcc_export_check( array_map( static function ( $path ) use ( $media_ids ) { return $media_ids[ $path ]; }, $value ) === project_core_fcc_gallery_ids( $id ), 'Gallery order matches.' ); continue; }
			if ( 'actives' === $name ) {
				$expected = array_map( 'project_core_fcc_snapshot_source', array_column( $value, 'source' ) );
				foreach ( $value as $active ) { $aid = project_core_fcc_snapshot_source( $active['source'] ); fcc_export_check( get_post_field( 'post_title', $aid ) === $active['name'] && get_post_meta( $aid, 'fcc_tag', true ) === $active['tag'] && get_post_meta( $aid, 'fcc_description', true ) === $active['desc'], 'Ingredient content matches.' ); }
			} elseif ( 'image' === $name ) { $expected = $value ? $media_ids[ $value ] : ''; }
			elseif ( 'productIds' === $name ) { $expected = array_map( 'project_core_fcc_legacy_product_id', $value ); }
			else { $expected = fcc_export_resolve_readonly( $value ); }
			fcc_export_check( fcc_export_normalize( $expected ) === fcc_export_normalize( get_post_meta( $id, 'fcc_' . $name, true ) ), 'Catalog value matches: ' . $name );
		}
		fcc_export_check_fields( $id, $entry['fields'] );
	}
}
foreach ( $data['regimens'] as $slug => $sources ) { fcc_export_check( array_map( 'project_core_fcc_snapshot_source', $sources ) === wp_list_pluck( project_core_fcc_regimen_posts( $slug, project_core_fcc_page_id( 'concerns' ) ), 'ID' ), 'Regimen choices match.' ); }
foreach ( $data['settings'] as $name => $value ) { fcc_export_check( (string) get_option( $name ) === (string) $value, 'Public setting matches.' ); }
fcc_export_check( ! is_wp_error( project_core_fcc_validate_defaults( $data ) ), 'Snapshot validates against all packaged assets.' );
echo 'PASS: ', $checks, ' read-only CMS/export content, media bytes/metadata, gallery, ingredient, selection and settings comparisons.', PHP_EOL;
