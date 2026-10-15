<?php
/** Independent selection for up to six homepage products. @package Project_Core */
defined( 'ABSPATH' ) || exit;

function project_core_fcc_home_catalog_posts( $exclude = 0 ) {
	return project_core_fcc_selected_posts( 'home_catalog', 6, $exclude, 'fcc_product' );
}

function project_core_fcc_validate_home_catalog( $valid, $value ) {
	if ( true !== $valid || ! $value ) { return $valid; }
	$id = absint( acf_get_form_data( 'post_id' ) );
	if ( ! $id ) { $id = absint( acf_maybe_get_POST( '_acf_post_id', acf_maybe_get_POST( 'post_ID', 0 ) ) ); }
	if ( 'fcc_product' !== get_post_type( $id ) || ! current_user_can( 'edit_post', $id ) ) { return 'Chỉ sản phẩm được chọn vào danh mục trang chủ.'; }
	if ( count( project_core_fcc_home_catalog_posts( $id ) ) >= 6 ) {
		return 'Danh mục trang chủ chỉ hiển thị tối đa 6 sản phẩm, không tính combo nổi bật. Hãy bỏ chọn một sản phẩm hiện tại trước khi thêm sản phẩm mới.';
	}
	return $valid;
}
add_filter( 'acf/validate_value/name=fcc_home_catalog', 'project_core_fcc_validate_home_catalog', 10, 2 );
add_filter( 'acf/update_value/name=fcc_home_catalog_order', 'project_core_fcc_featured_order' );

function project_core_fcc_home_catalog_columns( $columns ) {
	$columns['fcc_home_catalog'] = 'Danh mục trên trang chủ';
	return $columns;
}
function project_core_fcc_home_catalog_column( $column, $id ) {
	if ( 'fcc_home_catalog' === $column ) { echo get_post_meta( $id, 'fcc_home_catalog', true ) ? esc_html( 'Có · Thứ tự ' . absint( get_post_meta( $id, 'fcc_home_catalog_order', true ) ) ) : '—'; }
}
add_filter( 'manage_fcc_product_posts_columns', 'project_core_fcc_home_catalog_columns' );
add_action( 'manage_fcc_product_posts_custom_column', 'project_core_fcc_home_catalog_column', 10, 2 );

/** Seed the six existing cards only when this selection has never been configured. */
function project_core_fcc_initialize_home_catalog() {
	if ( '1' === get_option( 'project_core_fcc_home_catalog_version' ) ) { return; }
	$data = project_core_fcc_default_document();
	if ( is_wp_error( $data ) || empty( $data['homeCatalog'] ) || ! is_array( $data['homeCatalog'] ) || count( $data['homeCatalog'] ) > 6 ) { return; }
	$posts = get_posts( array( 'post_type' => 'fcc_product', 'post_status' => array_values( get_post_stati() ), 'posts_per_page' => -1, 'no_found_rows' => true ) );
	foreach ( $posts as $post ) {
		if ( metadata_exists( 'post', $post->ID, 'fcc_home_catalog' ) ) {
			// An explicit selection, including "off", must survive an upgrade.
			update_option( 'project_core_fcc_home_catalog_version', '1', false );
			return;
		}
	}
	$ids = array();
	foreach ( $data['homeCatalog'] as $source ) {
		if ( ! is_string( $source ) ) { return; }
		if ( 0 !== strpos( $source, 'product_' ) ) { continue; }
		$match = get_posts( array( 'post_type' => 'fcc_product', 'post_status' => array_values( get_post_stati() ), 'posts_per_page' => 1, 'fields' => 'ids', 'meta_key' => '_fcc_source_key', 'meta_value' => $source ) );
		if ( ! $match ) { return; }
		$ids[] = $match[0];
	}
	foreach ( $ids as $order => $id ) {
		project_core_fcc_seed_meta( $id, 'fcc_home_catalog', 1, 'field_fcc_home_catalog' );
		project_core_fcc_seed_meta( $id, 'fcc_home_catalog_order', $order + 1, 'field_fcc_home_catalog_order' );
	}
	update_option( 'project_core_fcc_home_catalog_version', '1', false );
}

function project_core_fcc_upgrade_home_catalog() {
	if ( current_user_can( 'manage_options' ) ) { project_core_fcc_initialize_home_catalog(); }
}
add_action( 'admin_init', 'project_core_fcc_upgrade_home_catalog' );
