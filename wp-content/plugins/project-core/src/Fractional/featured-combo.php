<?php
/** One authoritative combo selection shared by every featured placement. @package Project_Core */
defined( 'ABSPATH' ) || exit;

function project_core_fcc_featured_combo() {
	$id = absint( get_option( 'project_core_fcc_featured_combo_id', 0 ) );
	if ( ! $id ) { return array(); }
	foreach ( project_core_fcc_combos() as $combo ) {
		if ( $id === $combo['id'] ) { return $combo; }
	}
	return array();
}

/** A single option prevents concurrent saves from selecting two combos. */
function project_core_fcc_set_featured_combo( $id ) {
	if ( ! is_int( $id ) && ! ( is_string( $id ) && ctype_digit( $id ) ) ) { return new WP_Error( 'fcc_combo_invalid', 'ID combo không hợp lệ.' ); }
	if ( (int) $id < 0 ) { return new WP_Error( 'fcc_combo_invalid', 'ID combo không hợp lệ.' ); }
	$id = absint( $id );
	$current = absint( get_option( 'project_core_fcc_featured_combo_id', 0 ) );
	if ( ! current_user_can( 'edit_pages' ) || ( $id && ! current_user_can( 'edit_post', $id ) ) || ( $current && get_post( $current ) && ! current_user_can( 'edit_post', $current ) ) ) {
		return new WP_Error( 'fcc_combo_permission', 'Bạn không có quyền thay đổi combo nổi bật.' );
	}
	if ( $id && ( 'fcc_combo' !== get_post_type( $id ) || 'publish' !== get_post_status( $id ) ) ) {
		return new WP_Error( 'fcc_combo_invalid', 'Chọn combo đã xuất bản để hiển thị nổi bật.' );
	}
	update_option( 'project_core_fcc_featured_combo_id', $id, false );
	return true;
}

/** The native ACF toggle reads/writes the option, without another postmeta selection. */
function project_core_fcc_load_featured_combo( $value, $post_id, $field ) {
	if ( 'fcc_featured_combo' !== $field['name'] ) { return $value; }
	$id = absint( $post_id );
	return $id && 'fcc_combo' === get_post_type( $id ) && $id === absint( get_option( 'project_core_fcc_featured_combo_id', 0 ) ) ? 1 : 0;
}
add_filter( 'acf/pre_load_value', 'project_core_fcc_load_featured_combo', 10, 3 );

function project_core_fcc_save_featured_combo( $check, $value, $post_id, $field ) {
	if ( 'fcc_featured_combo' !== $field['name'] ) { return $check; }
	$id = absint( $post_id );
	if ( ! in_array( $value, array( 0, 1, '0', '1', false, true, '' ), true ) || 'fcc_combo' !== get_post_type( $id ) || ! current_user_can( 'edit_post', $id ) ) { return false; }
	if ( ! $value && $id !== absint( get_option( 'project_core_fcc_featured_combo_id', 0 ) ) ) { return true; }
	return true === project_core_fcc_set_featured_combo( $value ? $id : 0 );
}
add_filter( 'acf/pre_update_value', 'project_core_fcc_save_featured_combo', 10, 4 );

function project_core_fcc_validate_featured_combo( $valid, $value ) {
	if ( true !== $valid ) { return $valid; }
	if ( ! in_array( $value, array( 0, 1, '0', '1', false, true, '' ), true ) ) { return 'Lựa chọn combo nổi bật không hợp lệ.'; }
	$id = absint( acf_get_form_data( 'post_id' ) );
	if ( ! $id ) { $id = absint( acf_maybe_get_POST( '_acf_post_id', acf_maybe_get_POST( 'post_ID', 0 ) ) ); }
	if ( ! current_user_can( 'edit_pages' ) || ! current_user_can( 'edit_post', $id ) || 'fcc_combo' !== get_post_type( $id ) ) { return 'Bạn không có quyền thay đổi combo nổi bật.'; }
	if ( $value && 'publish' !== get_post_status( $id ) && 'publish' !== acf_maybe_get_POST( 'post_status', '' ) ) { return 'Hãy xuất bản combo trước khi chọn hiển thị nổi bật.'; }
	return $valid;
}
add_filter( 'acf/validate_value/name=fcc_featured_combo', 'project_core_fcc_validate_featured_combo', 10, 2 );

/** Migrate the existing highlighted combo once; an explicit empty choice stays empty. */
function project_core_fcc_initialize_featured_combo() {
	if ( false !== get_option( 'project_core_fcc_featured_combo_id', false ) ) { return; }
	$legacy = project_core_fcc_selected_posts( 'home_catalog', 1, 0, 'fcc_combo' );
	if ( ! $legacy ) { $legacy = project_core_fcc_selected_posts( 'featured', 1, 0, 'fcc_combo' ); }
	if ( $legacy ) { add_option( 'project_core_fcc_featured_combo_id', $legacy[0]->ID, '', false ); return; }
	$combos = get_posts( array( 'post_type' => 'fcc_combo', 'post_status' => array_values( get_post_stati() ), 'posts_per_page' => -1, 'orderby' => array( 'menu_order' => 'ASC', 'ID' => 'ASC' ), 'no_found_rows' => true ) );
	$configured = false;
	foreach ( $combos as $combo ) {
		$configured = $configured || metadata_exists( 'post', $combo->ID, 'fcc_home_catalog' ) || metadata_exists( 'post', $combo->ID, 'fcc_featured' );
		if ( 'publish' === $combo->post_status && ( get_post_meta( $combo->ID, 'fcc_home_catalog', true ) || get_post_meta( $combo->ID, 'fcc_featured', true ) ) ) {
			add_option( 'project_core_fcc_featured_combo_id', $combo->ID, '', false );
			return;
		}
	}
	if ( $configured ) { add_option( 'project_core_fcc_featured_combo_id', 0, '', false ); return; }
	$data = project_core_fcc_default_document();
	if ( is_wp_error( $data ) ) { return; }
	$source = $data['featuredCombo'] ?? '';
	if ( ! array_key_exists( 'featuredCombo', $data ) ) {
		foreach ( $data['featured'] ?? array() as $item ) { if ( 0 === strpos( $item['source'], 'combo_' ) ) { $source = $item['source']; break; } }
	}
	$id = $source ? project_core_fcc_snapshot_source( $source ) : 0;
	if ( $source && ! $id ) { return; }
	add_option( 'project_core_fcc_featured_combo_id', 'fcc_combo' === get_post_type( $id ) ? $id : 0, '', false );
}

function project_core_fcc_upgrade_featured_combo() {
	if ( current_user_can( 'manage_options' ) ) { project_core_fcc_initialize_featured_combo(); }
}
add_action( 'admin_init', 'project_core_fcc_upgrade_featured_combo' );

function project_core_fcc_featured_combo_column( $column, $id ) {
	if ( 'fcc_featured_combo' === $column ) { echo absint( get_option( 'project_core_fcc_featured_combo_id', 0 ) ) === (int) $id ? esc_html( 'Có' ) : '—'; }
}
add_filter( 'manage_fcc_combo_posts_columns', static function ( $columns ) { $columns['fcc_featured_combo'] = 'Combo nổi bật'; return $columns; } );
add_action( 'manage_fcc_combo_posts_custom_column', 'project_core_fcc_featured_combo_column', 10, 2 );
