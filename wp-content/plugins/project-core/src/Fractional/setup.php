<?php
/** Versioned default initialization, never run on activation. @package Project_Core */
defined( 'ABSPATH' ) || exit;

function project_core_fcc_default_document() {
	$path = dirname( __DIR__, 2 ) . '/defaults/fractional.json';
	if ( ! is_readable( $path ) ) { return new WP_Error( 'fcc_defaults_missing', 'Chưa có dữ liệu đóng gói.' ); }
	$data = json_decode( file_get_contents( $path ), true );
	return is_array( $data ) ? $data : new WP_Error( 'fcc_defaults_invalid', 'Dữ liệu đóng gói không hợp lệ.' );
}

function project_core_fcc_seed_meta( $id, $name, $value, $key = '' ) {
	if ( metadata_exists( 'post', $id, $name ) ) { return; }
	if ( $key && function_exists( 'update_field' ) && acf_get_field( $key ) ) { update_field( $key, $value, $id ); }
	else { add_post_meta( $id, $name, $value, true ); }
}

function project_core_fcc_seed_post( $type, $source_key, $title, $slug, $order = 0 ) {
	$existing = get_posts( array( 'post_type' => $type, 'post_status' => 'any', 'posts_per_page' => 1, 'fields' => 'ids', 'meta_key' => '_fcc_source_key', 'meta_value' => $source_key ) );
	if ( $existing ) { return (int) $existing[0]; }
	return wp_insert_post( array( 'post_type' => $type, 'post_status' => 'publish', 'post_title' => $title, 'post_name' => $slug, 'menu_order' => $order, 'post_author' => get_current_user_id(), 'meta_input' => array( '_fcc_source_key' => $source_key ) ), true );
}

function project_core_fcc_seed_media( $relative_path ) {
	if ( ! preg_match( '#^assets/images/[a-zA-Z0-9/_-]+\.(png|jpe?g|webp)$#', $relative_path ) ) { return new WP_Error( 'fcc_asset_path', 'Đường dẫn ảnh không hợp lệ.' ); }
	$existing = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'posts_per_page' => 1, 'fields' => 'ids', 'meta_key' => '_fcc_asset_source', 'meta_value' => $relative_path ) );
	if ( $existing ) { return (int) $existing[0]; }
	$source = get_stylesheet_directory() . '/assets/fractional/' . $relative_path;
	if ( ! is_readable( $source ) ) { return new WP_Error( 'fcc_asset_missing', 'Thiếu ảnh mặc định: ' . $relative_path ); }
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$temp = wp_tempnam( basename( $source ) );
	if ( ! $temp || ! copy( $source, $temp ) ) { return new WP_Error( 'fcc_asset_copy', 'Không thể chuẩn bị ảnh.' ); }
	$id = media_handle_sideload( array( 'name' => basename( $source ), 'tmp_name' => $temp ), 0 );
	if ( is_wp_error( $id ) ) { if ( file_exists( $temp ) ) { wp_delete_file( $temp ); } return $id; }
	add_post_meta( $id, '_fcc_asset_source', $relative_path, true );
	return $id;
}

function project_core_fcc_initialize( $content_status = 'local_sample' ) {
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'upload_files' ) ) { return new WP_Error( 'fcc_permissions', 'Không có quyền khởi tạo website.' ); }
	if ( ! function_exists( 'acf_add_local_field_group' ) || 'project-child' !== get_stylesheet() ) { return new WP_Error( 'fcc_dependencies', 'Cần kích hoạt ACF và Project Child trước khi khởi tạo.' ); }
	$data = project_core_fcc_default_document();
	if ( is_wp_error( $data ) ) { return $data; }
	if ( ! add_option( 'project_core_fcc_setup_lock', time(), '', false ) ) { return new WP_Error( 'fcc_setup_busy', 'Có lần khởi tạo đang chạy. Không chạy đồng thời.' ); }
	try {
		return project_core_fcc_import_document( $data, $content_status );
	} finally { delete_option( 'project_core_fcc_setup_lock' ); }
}

function project_core_fcc_import_document( $data, $content_status ) {
	$ids = get_option( 'project_core_fcc_pages', array() );
	foreach ( $data['pages'] as $kind => $page ) {
		$id = project_core_fcc_seed_post( 'shared' === $kind ? 'fcc_site' : 'page', 'page_' . $kind, $page['title'], $page['slug'] );
		if ( is_wp_error( $id ) ) { return $id; }
		$ids[ $kind ] = $id;
		if ( isset( $page['template'] ) ) { project_core_fcc_seed_meta( $id, '_wp_page_template', $page['template'] ); }
		foreach ( $page['fields'] as $name => $field ) {
			$value = $field['value'];
			if ( 'image' === $field['type'] ) { $value = project_core_fcc_seed_media( $value ); if ( is_wp_error( $value ) ) { return $value; } }
			project_core_fcc_seed_meta( $id, $name, $value, 'field_' . $name );
		}
	}
	update_option( 'project_core_fcc_pages', $ids, false );
	foreach ( $data['brand'] as $name => $value ) { if ( is_scalar( $value ) || null === $value ) { project_core_fcc_seed_meta( $ids['shared'], 'fcc_' . $name, (string) $value, 'field_fcc_' . $name ); } }
	project_core_fcc_seed_meta( $ids['shared'], 'fcc_content_status', 'approved' === $content_status ? 'approved' : 'local_sample', 'field_fcc_content_status' );
	$products = array();
	foreach ( $data['products'] as $order => $product ) {
		$id = project_core_fcc_seed_post( 'fcc_product', 'product_' . $product['id'], $product['name'], $product['slug'], $order );
		if ( is_wp_error( $id ) ) { return $id; }
		$products[ $product['id'] ] = $id;
		project_core_fcc_seed_meta( $id, '_fcc_source_id', $product['id'] );
		$ingredient_ids = array();
		foreach ( $product['actives'] as $index => $ingredient ) {
			$ingredient_id = project_core_fcc_seed_post( 'fcc_ingredient', 'ingredient_' . $product['id'] . '_' . $index, $ingredient['name'], sanitize_title( $ingredient['name'] ) );
			if ( is_wp_error( $ingredient_id ) ) { return $ingredient_id; }
			project_core_fcc_seed_meta( $ingredient_id, 'fcc_tag', $ingredient['tag'], 'field_fcc_tag' );
			project_core_fcc_seed_meta( $ingredient_id, 'fcc_description', $ingredient['desc'], 'field_fcc_description' );
			$ingredient_ids[] = $ingredient_id;
		}
		project_core_fcc_seed_meta( $id, 'fcc_actives', $ingredient_ids, 'field_fcc_actives' );
		foreach ( $product as $name => $value ) {
			if ( in_array( $name, array( 'id', 'name', 'slug', 'gallery', 'actives' ), true ) ) { continue; }
			if ( 'image' === $name ) { $value = project_core_fcc_seed_media( $value ); if ( is_wp_error( $value ) ) { return $value; } }
			elseif ( is_array( $value ) && 'skinConcerns' !== $name ) { $value = implode( "\n", $value ); }
			project_core_fcc_seed_meta( $id, 'fcc_' . $name, null === $value ? '' : $value, 'field_fcc_' . $name );
		}
		if ( ! metadata_exists( 'post', $id, '_fcc_gallery_initialized' ) ) {
			$blocks = '';
			foreach ( $product['gallery'] as $path ) {
				$media_id = project_core_fcc_seed_media( $path ); if ( is_wp_error( $media_id ) ) { return $media_id; }
				$blocks .= '<!-- wp:image {"id":' . $media_id . ',"sizeSlug":"full","linkDestination":"none"} --><figure class="wp-block-image size-full"><img src="' . esc_url( wp_get_attachment_url( $media_id ) ) . '" alt="" class="wp-image-' . $media_id . '"/></figure><!-- /wp:image -->';
			}
			if ( '' === get_post_field( 'post_content', $id ) ) { wp_update_post( array( 'ID' => $id, 'post_content' => '<!-- wp:gallery {"linkTo":"none"} --><figure class="wp-block-gallery has-nested-images columns-default is-cropped">' . $blocks . '</figure><!-- /wp:gallery -->' ) ); }
			add_post_meta( $id, '_fcc_gallery_initialized', 1, true );
		}
	}
	foreach ( $data['combos'] as $combo ) {
		$id = project_core_fcc_seed_post( 'fcc_combo', 'combo_' . $combo['id'], $combo['name'], sanitize_title( $combo['code'] ) );
		if ( is_wp_error( $id ) ) { return $id; }
		foreach ( $combo as $name => $value ) {
			if ( in_array( $name, array( 'id', 'name' ), true ) ) { continue; }
			if ( 'image' === $name ) { $value = project_core_fcc_seed_media( $value ); if ( is_wp_error( $value ) ) { return $value; } }
			elseif ( 'productIds' === $name ) { $value = array_map( function ( $source_id ) use ( $products ) { return $products[ $source_id ]; }, $value ); }
			elseif ( is_array( $value ) ) { $value = implode( "\n", $value ); }
			project_core_fcc_seed_meta( $id, 'fcc_' . $name, null === $value ? '' : $value, 'field_fcc_combo_' . $name );
		}
	}
	update_option( 'project_core_fcc_seed_version', $data['version'], false );
	flush_rewrite_rules( false );
	return $ids;
}

function project_core_fcc_handle_setup() {
	if ( ! current_user_can( 'manage_options' ) ) { wp_die( esc_html__( 'Không có quyền.', 'project-core' ), '', array( 'response' => 403 ) ); }
	check_admin_referer( 'project_core_fcc_setup' );
	$result = project_core_fcc_initialize();
	if ( is_wp_error( $result ) ) { wp_die( esc_html( $result->get_error_message() ) ); }
	wp_safe_redirect( admin_url( 'admin.php?page=project-website-content&fcc_setup=success' ) );
	exit;
}
add_action( 'admin_post_project_core_fcc_setup', 'project_core_fcc_handle_setup' );
