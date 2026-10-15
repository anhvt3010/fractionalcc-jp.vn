<?php
/** Versioned initialization; existing edits survive normal setup and activation. @package Project_Core */
defined( 'ABSPATH' ) || exit;

function project_core_fcc_default_document() {
	$path = dirname( __DIR__, 2 ) . '/defaults/fractional.json';
	if ( ! is_readable( $path ) ) { return new WP_Error( 'fcc_defaults_missing', 'Chưa có dữ liệu đóng gói.' ); }
	$data = json_decode( file_get_contents( $path ), true );
	return is_array( $data ) ? $data : new WP_Error( 'fcc_defaults_invalid', 'Dữ liệu đóng gói không hợp lệ.' );
}

function project_core_fcc_seed_meta( $id, $name, $value, $key = '', $replace = false ) {
	if ( ! $replace && metadata_exists( 'post', $id, $name ) ) { return; }
	if ( $key && function_exists( 'update_field' ) && acf_get_field( $key ) ) { update_field( $key, $value, $id ); }
	else { update_post_meta( $id, $name, $value ); }
}

function project_core_fcc_seed_post( $type, $source_key, $title, $slug, $order = 0, $update_title = false, $record = array() ) {
	$existing = get_posts( array( 'post_type' => $type, 'post_status' => array_values( get_post_stati() ), 'posts_per_page' => 1, 'fields' => 'ids', 'meta_key' => '_fcc_source_key', 'meta_value' => $source_key ) );
	if ( $existing ) {
		return $update_title ? wp_update_post( array( 'ID' => (int) $existing[0], 'post_title' => $title ), true ) : (int) $existing[0];
	}
	$content = project_core_fcc_resolve_default( $record['content'] ?? '' );
	$excerpt = project_core_fcc_resolve_default( $record['excerpt'] ?? '' );
	if ( is_wp_error( $content ) || is_wp_error( $excerpt ) ) { return is_wp_error( $content ) ? $content : $excerpt; }
	return wp_insert_post( wp_slash( array( 'post_type' => $type, 'post_status' => 'publish', 'post_title' => $title, 'post_name' => $slug, 'post_content' => $content, 'post_excerpt' => $excerpt, 'menu_order' => $order, 'post_author' => get_current_user_id(), 'meta_input' => array( '_fcc_source_key' => $source_key ) ) ), true );
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
	$data = project_core_fcc_default_document();
	if ( ! is_wp_error( $data ) && isset( $data['media'][ $relative_path ] ) ) {
		$media = $data['media'][ $relative_path ];
		$updated = wp_update_post( wp_slash( array( 'ID' => $id, 'post_title' => $media['title'], 'post_excerpt' => $media['caption'], 'post_content' => $media['description'] ) ), true );
		if ( is_wp_error( $updated ) ) { return $updated; }
		update_post_meta( $id, '_wp_attachment_image_alt', wp_slash( $media['alt'] ) );
	}
	return $id;
}

/** Reject incomplete packages before creating any content. */
function project_core_fcc_validate_defaults( $data ) {
	$invalid = new WP_Error( 'fcc_defaults_invalid', 'Dữ liệu mặc định không đầy đủ hoặc không hợp lệ.' );
	if ( empty( $data['version'] ) || ! is_string( $data['version'] ) || ! preg_match( '/^\d+\.\d+\.\d+(?:-[a-zA-Z0-9.-]+)?$/', $data['version'] ) ) { return $invalid; }
	foreach ( array( 'pages', 'brand', 'products', 'combos' ) as $section ) {
		if ( ! isset( $data[ $section ] ) || ! is_array( $data[ $section ] ) ) { return $invalid; }
	}
	$images = array();
	foreach ( array( 'shared', 'home', 'catalog', 'policies', 'thankyou', 'concerns' ) as $kind ) {
		$page = isset( $data['pages'][ $kind ] ) ? $data['pages'][ $kind ] : array();
		if ( ! isset( $page['title'], $page['slug'], $page['fields'] ) || ! is_string( $page['title'] ) || ! is_string( $page['slug'] ) || ! is_array( $page['fields'] ) ) { return $invalid; }
		if ( 'shared' !== $kind && ( ! isset( $page['template'] ) || 'templates/fractional-' . $kind . '.php' !== $page['template'] || ! is_readable( get_stylesheet_directory() . '/' . $page['template'] ) ) ) { return $invalid; }
		foreach ( $page['fields'] as $field ) {
			if ( ! is_array( $field ) || ! isset( $field['type'] ) || ! array_key_exists( 'value', $field ) || ( ! is_scalar( $field['value'] ) && ! is_array( $field['value'] ) ) ) { return $invalid; }
			if ( 'image' === $field['type'] && $field['value'] ) { $images[] = $field['value']; }
		}
	}
	$product_ids = array();
	foreach ( $data['products'] as $product ) {
		if ( ! isset( $product['id'], $product['name'], $product['slug'], $product['image'], $product['gallery'], $product['actives'] ) || ! is_int( $product['id'] ) || $product['id'] < 1 || isset( $product_ids[ $product['id'] ] ) || ! is_string( $product['name'] ) || ! is_string( $product['slug'] ) || ! is_array( $product['gallery'] ) || ! is_array( $product['actives'] ) ) { return $invalid; }
		$product_ids[ $product['id'] ] = true;
		if ( $product['image'] ) { $images[] = $product['image']; }
		$images = array_merge( $images, $product['gallery'] );
		foreach ( $product['actives'] as $active ) {
			foreach ( array( 'name', 'tag', 'desc' ) as $key ) {
				if ( ! isset( $active[ $key ] ) || ! is_string( $active[ $key ] ) ) { return $invalid; }
			}
		}
	}
	$combo_ids = array();
	foreach ( $data['combos'] as $combo ) {
		if ( ! isset( $combo['id'], $combo['name'], $combo['code'], $combo['image'], $combo['productIds'] ) || ! is_scalar( $combo['id'] ) || ! preg_match( '/^[a-zA-Z0-9_-]+$/', (string) $combo['id'] ) || isset( $combo_ids[ $combo['id'] ] ) || ! is_string( $combo['name'] ) || ! is_string( $combo['code'] ) || ! is_array( $combo['productIds'] ) ) { return $invalid; }
		$combo_ids[ $combo['id'] ] = true;
		if ( $combo['image'] ) { $images[] = $combo['image']; }
		foreach ( $combo['productIds'] as $id ) {
			if ( ! is_int( $id ) || ! isset( $product_ids[ $id ] ) ) { return $invalid; }
		}
	}
	$checked = array();
	foreach ( $images as $path ) {
		if ( ! is_string( $path ) || ! preg_match( '#^assets/images/[a-zA-Z0-9/_-]+\.(png|jpe?g|webp)$#', $path ) ) { return $invalid; }
		if ( isset( $checked[ $path ] ) ) { continue; }
		$source = get_stylesheet_directory() . '/assets/fractional/' . $path;
		if ( ! is_readable( $source ) ) { return new WP_Error( 'fcc_asset_missing', 'Thiếu ảnh mặc định: ' . $path ); }
		if ( ! in_array( wp_get_image_mime( $source ), array( 'image/png', 'image/jpeg', 'image/webp' ), true ) ) { return new WP_Error( 'fcc_asset_invalid', 'Ảnh mặc định không hợp lệ: ' . $path ); }
		$checked[ $path ] = true;
	}
	return project_core_fcc_validate_snapshot( $data );
}

/** Compare-and-delete prevents an expired worker from releasing a newer lock. */
function project_core_fcc_release_setup_lock( $lock ) {
	global $wpdb;
	$deleted = $wpdb->delete( $wpdb->options, array( 'option_name' => 'project_core_fcc_setup_lock', 'option_value' => maybe_serialize( $lock ) ), array( '%s', '%s' ) );
	if ( $deleted ) {
		wp_cache_delete( 'project_core_fcc_setup_lock', 'options' );
		wp_cache_delete( 'notoptions', 'options' );
	}
}

function project_core_fcc_initialize( $content_status = null, $update_catalog = false ) {
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'upload_files' ) ) { return new WP_Error( 'fcc_permissions', 'Không có quyền khởi tạo website.' ); }
	if ( $update_catalog && 'cli' !== PHP_SAPI ) { return new WP_Error( 'fcc_catalog_cli', 'Cập nhật hồ sơ hiện có chỉ được chạy bằng lệnh CLI có chỉ định rõ.' ); }
	if ( ! function_exists( 'acf_add_local_field_group' ) || 'project-child' !== get_stylesheet() || wp_get_theme()->errors() ) { return new WP_Error( 'fcc_dependencies', 'Cần cài Kadence và kích hoạt ACF, Project Child trước khi khởi tạo.' ); }
	$data = project_core_fcc_default_document();
	if ( is_wp_error( $data ) ) { return $data; }
	if ( null === $content_status ) { $content_status = $data['approval']['status'] ?? 'local_sample'; }
	$valid = project_core_fcc_validate_defaults( $data );
	if ( is_wp_error( $valid ) ) { return $valid; }
	$previous = get_option( 'project_core_fcc_setup_lock' );
	$started = is_array( $previous ) && isset( $previous['started'] ) ? (int) $previous['started'] : (int) $previous;
	if ( $previous && $started < time() - 15 * MINUTE_IN_SECONDS ) { project_core_fcc_release_setup_lock( $previous ); }
	$lock = array( 'started' => time(), 'token' => wp_generate_uuid4() );
	if ( ! add_option( 'project_core_fcc_setup_lock', $lock, '', false ) ) { return new WP_Error( 'fcc_setup_busy', 'Có lần khởi tạo đang chạy. Không chạy đồng thời.' ); }
	try {
		return project_core_fcc_import_document( $data, $content_status, $update_catalog );
	} finally { project_core_fcc_release_setup_lock( $lock ); }
}

/** Only replace an initializer-owned gallery; preserve manually authored blocks. */
function project_core_fcc_is_seeded_gallery( $id ) {
	if ( get_post_meta( $id, '_fcc_product_media_customized', true ) ) { return false; }
	if ( ! get_post_meta( $id, '_fcc_gallery_initialized', true ) ) { return false; }
	$content = get_post_field( 'post_content', $id );
	if ( '' === $content ) { return true; }
	$blocks = array_values( array_filter( parse_blocks( $content ), function ( $block ) { return ! empty( $block['blockName'] ) || '' !== trim( $block['innerHTML'] ); } ) );
	if ( 1 !== count( $blocks ) || 'core/gallery' !== $blocks[0]['blockName'] || empty( $blocks[0]['innerBlocks'] ) ) { return false; }
	foreach ( $blocks[0]['innerBlocks'] as $image ) {
		if ( 'core/image' !== $image['blockName'] || empty( $image['attrs']['id'] ) || ! get_post_meta( $image['attrs']['id'], '_fcc_asset_source', true ) ) { return false; }
	}
	return true;
}

function project_core_fcc_import_document( $data, $content_status, $update_catalog = false ) {
	$product_fields = isset( $data['catalogSource']['productFields'] ) ? $data['catalogSource']['productFields'] : array();
	$combo_fields = isset( $data['catalogSource']['comboFields'] ) ? $data['catalogSource']['comboFields'] : array();
	$ids = get_option( 'project_core_fcc_pages', array() );
	$first = ! get_option( 'project_core_fcc_seed_version' );
	// A fresh site restores the entire approved library, including currently unused images.
	// Upgrades only import media needed for missing values and preserve existing choices.
	if ( $first ) {
		foreach ( array_keys( $data['media'] ?? array() ) as $path ) {
			$media_id = project_core_fcc_seed_media( $path );
			if ( is_wp_error( $media_id ) ) { return $media_id; }
		}
	}
	foreach ( $data['pages'] as $kind => $page ) {
		$id = project_core_fcc_seed_post( 'shared' === $kind ? 'fcc_site' : 'page', 'page_' . $kind, $page['title'], $page['slug'], $page['menuOrder'] ?? 0, false, $page );
		if ( is_wp_error( $id ) ) { return $id; }
		$ids[ $kind ] = $id;
		if ( isset( $page['template'] ) ) { project_core_fcc_seed_meta( $id, '_wp_page_template', $page['template'] ); }
		$result = project_core_fcc_seed_snapshot_fields( $id, $page['fields'] );
		if ( is_wp_error( $result ) ) { return $result; }
	}
	update_option( 'project_core_fcc_pages', $ids, false );
	foreach ( $data['brand'] as $name => $value ) { if ( is_scalar( $value ) || null === $value ) { project_core_fcc_seed_meta( $ids['shared'], 'fcc_' . $name, (string) $value, 'field_fcc_' . $name ); } }
	project_core_fcc_seed_meta( $ids['shared'], 'fcc_content_status', 'approved' === $content_status ? 'approved' : 'local_sample', 'field_fcc_content_status' );
	$products = array();
	foreach ( $data['products'] as $order => $product ) {
		$id = project_core_fcc_seed_post( 'fcc_product', 'product_' . $product['id'], $product['name'], $product['slug'], $product['menuOrder'] ?? $order, $update_catalog, $product );
		if ( is_wp_error( $id ) ) { return $id; }
		$products[ $product['id'] ] = $id;
		project_core_fcc_seed_meta( $id, '_fcc_source_id', $product['id'] );
		$ingredient_ids = array();
		foreach ( $product['actives'] as $index => $ingredient ) {
			$ingredient_id = project_core_fcc_seed_post( 'fcc_ingredient', $ingredient['source'] ?? 'ingredient_' . $product['id'] . '_' . $index, $ingredient['name'], $ingredient['slug'] ?? sanitize_title( $ingredient['name'] ), 0, $update_catalog );
			if ( is_wp_error( $ingredient_id ) ) { return $ingredient_id; }
			project_core_fcc_seed_meta( $ingredient_id, 'fcc_tag', $ingredient['tag'], 'field_fcc_tag', $update_catalog );
			project_core_fcc_seed_meta( $ingredient_id, 'fcc_description', $ingredient['desc'], 'field_fcc_description', $update_catalog );
			$ingredient_ids[] = $ingredient_id;
		}
		project_core_fcc_seed_meta( $id, 'fcc_actives', $ingredient_ids, 'field_fcc_actives', $update_catalog );
		foreach ( $product as $name => $value ) {
			if ( in_array( $name, array( 'id', 'name', 'slug', 'gallery', 'actives', 'fields', 'menuOrder', 'content', 'excerpt' ), true ) ) { continue; }
			$replace = $update_catalog && in_array( $name, $product_fields, true );
			if ( ! $replace && metadata_exists( 'post', $id, 'fcc_' . $name ) ) { continue; }
			if ( 'image' === $name ) {
				$value = $value ? project_core_fcc_seed_media( $value ) : 0; if ( is_wp_error( $value ) ) { return $value; }
				if ( $value && ! isset( $data['media'] ) && '' === get_post_meta( $value, '_wp_attachment_image_alt', true ) ) { update_post_meta( $value, '_wp_attachment_image_alt', $product['name'] ); }
			}
			elseif ( is_array( $value ) && 'skinConcerns' !== $name ) { $value = implode( "\n", $value ); }
			$value = project_core_fcc_resolve_default( $value ); if ( is_wp_error( $value ) ) { return $value; }
			project_core_fcc_seed_meta( $id, 'fcc_' . $name, null === $value ? '' : $value, 'field_fcc_' . $name, $replace );
		}
		$result = project_core_fcc_seed_snapshot_fields( $id, $product['fields'] ?? array() ); if ( is_wp_error( $result ) ) { return $result; }
		$replace_gallery = $update_catalog && project_core_fcc_is_seeded_gallery( $id );
		if ( ! metadata_exists( 'post', $id, '_fcc_gallery_initialized' ) || $replace_gallery ) {
			$gallery_ids = array();
			foreach ( $product['gallery'] as $path ) {
				$media_id = project_core_fcc_seed_media( $path ); if ( is_wp_error( $media_id ) ) { return $media_id; }
				$gallery_ids[] = $media_id;
			}
			if ( '' === get_post_field( 'post_content', $id ) || $replace_gallery ) {
				$updated = wp_update_post( wp_slash( array( 'ID' => $id, 'post_content' => project_core_fcc_gallery_content( '', $gallery_ids ) ) ), true );
				if ( is_wp_error( $updated ) ) { return $updated; }
			}
			add_post_meta( $id, '_fcc_gallery_initialized', 1, true );
		}
	}
	foreach ( $data['combos'] as $combo ) {
		$id = project_core_fcc_seed_post( 'fcc_combo', 'combo_' . $combo['id'], $combo['name'], $combo['slug'] ?? sanitize_title( $combo['code'] ), $combo['menuOrder'] ?? 0, false, $combo );
		if ( is_wp_error( $id ) ) { return $id; }
		foreach ( $combo as $name => $value ) {
			if ( in_array( $name, array( 'id', 'name', 'slug', 'menuOrder', 'content', 'excerpt', 'fields' ), true ) ) { continue; }
			$replace = $update_catalog && in_array( $name, $combo_fields, true );
			if ( ! $replace && metadata_exists( 'post', $id, 'fcc_' . $name ) ) { continue; }
			if ( 'image' === $name ) { $value = $value ? project_core_fcc_seed_media( $value ) : 0; if ( is_wp_error( $value ) ) { return $value; } }
			elseif ( 'productIds' === $name ) { $value = array_map( function ( $source_id ) use ( $products ) { return $products[ $source_id ]; }, $value ); }
			elseif ( is_array( $value ) ) { $value = implode( "\n", $value ); }
			$value = project_core_fcc_resolve_default( $value ); if ( is_wp_error( $value ) ) { return $value; }
			project_core_fcc_seed_meta( $id, 'fcc_' . $name, null === $value ? '' : $value, 'field_fcc_combo_' . $name, $replace );
		}
		$result = project_core_fcc_seed_snapshot_fields( $id, $combo['fields'] ?? array() ); if ( is_wp_error( $result ) ) { return $result; }
	}
	foreach ( $data['ingredients'] ?? array() as $ingredient ) {
		$id = project_core_fcc_seed_post( 'fcc_ingredient', $ingredient['source'], $ingredient['name'], $ingredient['slug'] ); if ( is_wp_error( $id ) ) { return $id; }
		project_core_fcc_seed_meta( $id, 'fcc_tag', $ingredient['tag'], 'field_fcc_tag' ); project_core_fcc_seed_meta( $id, 'fcc_description', $ingredient['desc'], 'field_fcc_description' );
	}
	project_core_fcc_seed_snapshot_selections( $data, $ids, $first );
	project_core_fcc_initialize_featured();
	project_core_fcc_initialize_home_catalog();
	project_core_fcc_initialize_featured_combo();
	if ( $update_catalog && isset( $data['catalogSource']['sha256'] ) ) { update_option( 'project_core_fcc_catalog_source_hash', $data['catalogSource']['sha256'], false ); }
	// Select the generated homepage once; subsequent setup must preserve Reading settings.
	if ( ! get_option( 'project_core_fcc_seed_version' ) && 'posts' === get_option( 'show_on_front' ) && ! get_option( 'page_on_front' ) ) {
		update_option( 'page_on_front', $ids['home'] );
		update_option( 'show_on_front', 'page' );
	}
	$media_update = project_core_fcc_upgrade_media( $data );
	if ( is_wp_error( $media_update ) ) { return $media_update; }
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

/** Queue the user-requested defaults when the project is activated on a new site. */
function project_core_fcc_queue_initial_defaults() {
	if ( ! get_option( 'project_core_fcc_seed_version' ) ) { update_option( 'project_core_fcc_autosetup_pending', 1, false ); }
}
add_action( 'after_switch_theme', 'project_core_fcc_queue_initial_defaults' );

function project_core_fcc_maybe_initialize_defaults() {
	if ( ! get_option( 'project_core_fcc_autosetup_pending' ) || ! current_user_can( 'manage_options' ) || ! current_user_can( 'upload_files' ) ) { return; }
	if ( ! function_exists( 'acf_add_local_field_group' ) || 'project-child' !== get_stylesheet() ) { return; }
	$result = project_core_fcc_initialize();
	if ( is_wp_error( $result ) ) {
		set_transient( 'project_core_fcc_setup_error', $result->get_error_message(), HOUR_IN_SECONDS );
		return;
	}
	delete_option( 'project_core_fcc_autosetup_pending' );
	delete_transient( 'project_core_fcc_setup_error' );
}
add_action( 'admin_init', 'project_core_fcc_maybe_initialize_defaults', 20 );

function project_core_fcc_setup_notice() {
	$message = get_transient( 'project_core_fcc_setup_error' );
	if ( get_option( 'project_core_fcc_autosetup_pending' ) && ( ! function_exists( 'acf_add_local_field_group' ) || 'project-child' !== get_stylesheet() || wp_get_theme()->errors() ) ) {
		$message = 'Khởi tạo đang chờ: cần cài Kadence và kích hoạt ACF, Fractional CC – Project Child.';
	}
	if ( $message && current_user_can( 'manage_options' ) ) { echo '<div class="notice notice-error"><p>' . esc_html( $message ) . '</p></div>'; }
}
add_action( 'admin_notices', 'project_core_fcc_setup_notice' );
