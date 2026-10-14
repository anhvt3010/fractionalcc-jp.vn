<?php
/** Portable approved content, media and selections for a new installation. @package Project_Core */
defined( 'ABSPATH' ) || exit;

function project_core_fcc_snapshot_source( $source ) {
	$ids = get_posts( array( 'post_type' => array( 'page', 'fcc_site', 'fcc_product', 'fcc_combo', 'fcc_ingredient' ), 'post_status' => array_values( get_post_stati() ), 'posts_per_page' => 1, 'fields' => 'ids', 'meta_key' => '_fcc_source_key', 'meta_value' => $source ) );
	return $ids ? (int) $ids[0] : 0;
}

/** Values carry paths/source keys, never installation-specific IDs or hosts. */
function project_core_fcc_resolve_default( $value ) {
	if ( is_array( $value ) ) {
		foreach ( $value as &$item ) { $item = project_core_fcc_resolve_default( $item ); if ( is_wp_error( $item ) ) { return $item; } } unset( $item );
		return $value;
	}
	if ( ! is_string( $value ) ) { return $value; }
	$error = null;
	$value = preg_replace_callback( '/("?)\{\{fcc_media_(id|url):([^}]+)\}\}\1/', static function ( $match ) use ( &$error ) {
		$id = project_core_fcc_seed_media( $match[3] );
		if ( is_wp_error( $id ) ) { $error = $id; return ''; }
		return 'id' === $match[2] ? (string) $id : $match[1] . wp_get_attachment_url( $id ) . $match[1];
	}, $value );
	return $error ?: str_replace( '{{fcc_site_url}}', home_url(), $value );
}

function project_core_fcc_validate_snapshot( $data ) {
	$error = new WP_Error( 'fcc_snapshot_invalid', 'Dữ liệu nội dung đã chốt không hợp lệ.' );
	$sources = array();
	foreach ( $data['pages'] as $scope => $page ) { $sources[] = 'page_' . $scope; }
	foreach ( $data['products'] as $product ) { $sources[] = 'product_' . $product['id']; foreach ( $product['actives'] as $index => $active ) { $sources[] = $active['source'] ?? 'ingredient_' . $product['id'] . '_' . $index; } }
	foreach ( $data['combos'] as $combo ) { $sources[] = 'combo_' . $combo['id']; }
	foreach ( $data['ingredients'] ?? array() as $active ) { if ( empty( $active['source'] ) || ! isset( $active['name'], $active['tag'], $active['desc'] ) ) { return $error; } $sources[] = $active['source']; }
	foreach ( $sources as $source ) { if ( ! is_string( $source ) || ! preg_match( '/^(page|product|combo|ingredient)_[a-zA-Z0-9_-]+$/', $source ) ) { return $error; } }
	foreach ( array_merge( array_values( $data['pages'] ), $data['products'], $data['combos'] ) as $record ) {
		foreach ( $record['fields'] ?? array() as $name => $field ) {
			if ( ! preg_match( '/^fcc_[a-zA-Z0-9_]+$/', $name ) || ! isset( $field['type'], $field['value'] ) || ! is_string( $field['type'] ) || ( isset( $field['key'] ) && ! preg_match( '/^field_fcc_[a-zA-Z0-9_]+$/', $field['key'] ) ) ) { return $error; }
			if ( 'relationship' === $field['type'] ) { if ( ! is_array( $field['value'] ) || array_diff( $field['value'], $sources ) ) { return $error; } }
			elseif ( 'image' === $field['type'] && '' !== $field['value'] && 0 !== $field['value'] && ! isset( $data['media'][ $field['value'] ] ) ) { return $error; }
			elseif ( ! is_scalar( $field['value'] ) && ! is_array( $field['value'] ) ) { return $error; }
		}
		foreach ( array( 'content', 'excerpt' ) as $key ) {
			if ( ! isset( $record[ $key ] ) ) { continue; }
			if ( ! is_string( $record[ $key ] ) ) { return $error; }
			preg_match_all( '/\{\{fcc_media_(?:id|url):([^}]+)\}\}/', $record[ $key ], $matches );
			foreach ( $matches[1] as $path ) { if ( ! isset( $data['media'][ $path ] ) ) { return $error; } }
		}
	}
	if ( isset( $data['mediaUpdate'] ) ) {
		$update = $data['mediaUpdate'];
		if ( ! is_array( $update ) || empty( $update['version'] ) || ! is_string( $update['version'] ) || ! preg_match( '/^\d+\.\d+\.\d+$/', $update['version'] ) || ! isset( $update['products'], $update['previousMedia'] ) || ! is_array( $update['products'] ) || ! is_array( $update['previousMedia'] ) ) { return $error; }
		$seen = array();
		foreach ( $update['products'] as $change ) {
			if ( ! is_array( $change ) || ! isset( $change['id'], $change['previousImage'], $change['previousGallery'] ) || ! is_int( $change['id'] ) || isset( $seen[ $change['id'] ] ) || ! in_array( 'product_' . $change['id'], $sources, true ) || ! is_array( $change['previousGallery'] ) ) { return $error; }
			$seen[ $change['id'] ] = true;
			foreach ( array_merge( array( $change['previousImage'] ), $change['previousGallery'] ) as $path ) {
				if ( ! is_string( $path ) || ( ! isset( $data['media'][ $path ] ) && ! isset( $update['previousMedia'][ $path ] ) ) ) { return $error; }
			}
		}
	}
	foreach ( array_merge( $data['media'] ?? array(), $data['mediaUpdate']['previousMedia'] ?? array() ) as $path => $media ) {
		if ( ! preg_match( '#^assets/images/[a-zA-Z0-9/_-]+\.(png|jpe?g|webp)$#', $path ) || ! isset( $media['sha256'] ) || ! preg_match( '/^[a-f0-9]{64}$/', $media['sha256'] ) ) { return $error; }
		$file = get_stylesheet_directory() . '/assets/fractional/' . $path;
		if ( ! is_readable( $file ) || hash_file( 'sha256', $file ) !== $media['sha256'] || ! in_array( wp_get_image_mime( $file ), array( 'image/png', 'image/jpeg', 'image/webp' ), true ) ) { return new WP_Error( 'fcc_snapshot_media', 'Ảnh đóng gói không khớp nội dung đã chốt.' ); }
	}
	foreach ( $data['regimens'] ?? array() as $slug => $items ) { if ( ! isset( project_core_fcc_regimens()[ $slug ] ) || ! is_array( $items ) || count( $items ) > 6 || count( array_unique( $items ) ) !== count( $items ) || array_diff( $items, $sources ) ) { return $error; } foreach ( $items as $source ) { if ( ! preg_match( '/^(product|combo)_/', $source ) ) { return $error; } } }
	if ( array_diff( array_keys( $data['settings'] ?? array() ), project_core_fcc_snapshot_setting_names() ) ) { return $error; }
	foreach ( $data['settings'] ?? array() as $value ) { if ( ! is_scalar( $value ) ) { return $error; } }
	return true;
}

function project_core_fcc_snapshot_setting_names() {
	return array( 'blogname', 'blogdescription', 'timezone_string', 'gmt_offset', 'date_format', 'time_format', 'start_of_week', 'permalink_structure' );
}

function project_core_fcc_seed_snapshot_fields( $id, $fields ) {
	foreach ( $fields as $name => $field ) {
		if ( metadata_exists( 'post', $id, $name ) ) { continue; }
		$value = $field['value'];
		if ( 'image' === $field['type'] ) { $value = $value ? project_core_fcc_seed_media( $value ) : 0; }
		elseif ( 'relationship' === $field['type'] ) { $value = array_map( 'project_core_fcc_snapshot_source', $value ); }
		else { $value = project_core_fcc_resolve_default( $value ); }
		if ( is_wp_error( $value ) ) { return $value; }
		project_core_fcc_seed_meta( $id, $name, $value, $field['key'] ?? 'field_' . $name );
	}
	return true;
}

function project_core_fcc_seed_snapshot_selections( $data, $pages, $first ) {
	foreach ( $data['regimens'] ?? array() as $slug => $sources ) {
		$definition = project_core_fcc_regimens()[ $slug ];
		project_core_fcc_seed_meta( $pages['concerns'], 'fcc_' . $definition['name'], array_map( 'project_core_fcc_snapshot_source', $sources ), 'field_fcc_' . $definition['name'] );
	}
	if ( $first ) { foreach ( $data['settings'] ?? array() as $name => $value ) { update_option( $name, $value ); } }
}
