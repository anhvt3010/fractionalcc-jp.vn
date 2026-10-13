<?php
/** Add missing editable values without replacing customer content. @package Project_Core */
defined( 'ABSPATH' ) || exit;

function project_core_fcc_initialize_content_management() {
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'upload_files' ) ) { return new WP_Error( 'fcc_permissions', 'Không có quyền khởi tạo nội dung quản lý.' ); }
	if ( ! function_exists( 'acf_get_field' ) || 'project-child' !== get_stylesheet() ) { return new WP_Error( 'fcc_dependencies', 'Cần ACF và Project Child để quản lý nội dung.' ); }
	$data = project_core_fcc_default_document();
	if ( is_wp_error( $data ) || empty( $data['contentManagement']['version'] ) || empty( $data['contentManagement']['pages'] ) ) { return new WP_Error( 'fcc_management_defaults', 'Thiếu dữ liệu quản lý nội dung.' ); }
	$ids = get_option( 'project_core_fcc_pages', array() );
	// Validate the complete migration before writing any value or importing media.
	foreach ( $data['contentManagement']['pages'] as $scope => $names ) {
		$id = isset( $ids[ $scope ] ) ? absint( $ids[ $scope ] ) : 0;
		if ( ! $id || ! current_user_can( 'edit_post', $id ) || ! is_array( $names ) ) { return new WP_Error( 'fcc_management_page', 'Thiếu trang hoặc quyền chỉnh sửa nội dung.' ); }
		foreach ( $names as $name ) {
			if ( ! is_string( $name ) || ! preg_match( '/^fcc_[a-zA-Z0-9_]+$/', $name ) ) { return new WP_Error( 'fcc_management_field', 'Tên trường quản lý nội dung không hợp lệ.' ); }
			$field = isset( $data['pages'][ $scope ]['fields'][ $name ] ) ? $data['pages'][ $scope ]['fields'][ $name ] : array();
			if ( ! is_string( $name ) || ! preg_match( '/^fcc_[a-zA-Z0-9_]+$/', $name ) || ! acf_get_field( 'field_' . $name ) || ! isset( $field['type'] ) || ! array_key_exists( 'value', $field ) || ! is_scalar( $field['value'] ) || ! in_array( $field['type'], array( 'text', 'textarea', 'url', 'image' ), true ) ) { return new WP_Error( 'fcc_management_field', 'Trường quản lý nội dung không hợp lệ.' ); }
			if ( 'image' === $field['type'] && $field['value'] ) {
				$path = $field['value'];
				if ( ! preg_match( '#^assets/images/[a-zA-Z0-9/_-]+\.(png|jpe?g|webp)$#', $path ) || ! is_readable( get_stylesheet_directory() . '/assets/fractional/' . $path ) ) { return new WP_Error( 'fcc_management_image', 'Thiếu ảnh quản lý nội dung.' ); }
			}
		}
	}
	foreach ( $data['contentManagement']['pages'] as $scope => $names ) {
		foreach ( $names as $name ) {
			if ( metadata_exists( 'post', $ids[ $scope ], $name ) ) { continue; }
			$field = $data['pages'][ $scope ]['fields'][ $name ];
			$value = $field['value'];
			if ( 'image' === $field['type'] ) {
				$value = $value ? project_core_fcc_seed_media( $value ) : 0;
				if ( is_wp_error( $value ) ) { return $value; }
			}
			project_core_fcc_seed_meta( $ids[ $scope ], $name, $value, 'field_' . $name );
		}
	}
	foreach ( $data['combos'] as $combo ) {
		$matches = get_posts( array( 'post_type' => 'fcc_combo', 'post_status' => array_values( get_post_stati() ), 'posts_per_page' => 1, 'fields' => 'ids', 'meta_key' => '_fcc_source_key', 'meta_value' => 'combo_' . $combo['id'] ) );
		if ( ! $matches ) { continue; }
		if ( ! current_user_can( 'edit_post', $matches[0] ) ) { return new WP_Error( 'fcc_permissions', 'Không có quyền chỉnh nội dung combo.' ); }
		foreach ( array( 'rating', 'reviewCount' ) as $name ) {
			if ( isset( $combo[ $name ] ) ) { project_core_fcc_seed_meta( $matches[0], 'fcc_' . $name, $combo[ $name ], 'field_fcc_combo_' . $name ); }
		}
	}
	update_option( 'project_core_fcc_content_management_version', (string) $data['contentManagement']['version'], false );
	return true;
}

function project_core_fcc_maybe_upgrade_content_management() {
	if ( ! current_user_can( 'manage_options' ) || ! get_option( 'project_core_fcc_pages' ) ) { return; }
	$data = project_core_fcc_default_document();
	if ( is_wp_error( $data ) || empty( $data['contentManagement']['version'] ) || (string) $data['contentManagement']['version'] === get_option( 'project_core_fcc_content_management_version' ) ) { return; }
	$result = project_core_fcc_initialize_content_management();
	if ( is_wp_error( $result ) ) {
		add_action( 'admin_notices', static function () use ( $result ) { echo '<div class="notice notice-error"><p>' . esc_html( $result->get_error_message() ) . '</p></div>'; } );
	}
}
add_action( 'admin_init', 'project_core_fcc_maybe_upgrade_content_management', 25 );
