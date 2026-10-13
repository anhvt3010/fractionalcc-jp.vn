<?php
/** Homepage selection and repeatable migration from the approved duo. @package Project_Core */
defined( 'ABSPATH' ) || exit;

function project_core_fcc_featured_posts( $exclude = 0 ) {
	return project_core_fcc_selected_posts( 'featured', 2, $exclude );
}

/** Query a homepage selection, sharing ordering and publication rules. */
function project_core_fcc_selected_posts( $selection, $limit, $exclude = 0 ) {
	return get_posts( array(
		'post_type' => array( 'fcc_product', 'fcc_combo' ), 'post_status' => 'publish',
		'posts_per_page' => $limit, 'post__not_in' => $exclude ? array( absint( $exclude ) ) : array(),
		'meta_query' => array( 'selection' => array( 'key' => 'fcc_' . $selection, 'value' => '1' ), 'position' => array( 'key' => 'fcc_' . $selection . '_order', 'compare' => 'EXISTS', 'type' => 'NUMERIC' ) ),
		'orderby' => array( 'position' => 'ASC', 'ID' => 'ASC' ), 'no_found_rows' => true,
	) );
}

function project_core_fcc_validate_featured( $valid, $value ) {
	if ( true !== $valid || ! $value ) { return $valid; }
	$id = absint( acf_get_form_data( 'post_id' ) );
	if ( ! $id ) { $id = absint( acf_maybe_get_POST( '_acf_post_id', acf_maybe_get_POST( 'post_ID', 0 ) ) ); }
	if ( ! in_array( get_post_type( $id ), array( 'fcc_product', 'fcc_combo' ), true ) || ! current_user_can( 'edit_post', $id ) ) { $id = 0; }
	if ( count( project_core_fcc_featured_posts( $id ) ) >= 2 ) {
		return 'Trang chủ chỉ hiển thị 2 sản phẩm chủ lực. Hãy bỏ chọn một sản phẩm hoặc combo hiện tại trước.';
	}
	return $valid;
}
add_filter( 'acf/validate_value/name=fcc_featured', 'project_core_fcc_validate_featured', 10, 2 );

function project_core_fcc_featured_order( $value ) {
	return absint( $value );
}
add_filter( 'acf/update_value/name=fcc_featured_order', 'project_core_fcc_featured_order' );

function project_core_fcc_featured_columns( $columns ) {
	$columns['fcc_featured'] = 'Sản phẩm chủ lực';
	return $columns;
}
function project_core_fcc_featured_column( $column, $id ) {
	if ( 'fcc_featured' === $column ) { echo get_post_meta( $id, 'fcc_featured', true ) ? esc_html( 'Có · Thứ tự ' . absint( get_post_meta( $id, 'fcc_featured_order', true ) ) ) : '—'; }
}
foreach ( array( 'fcc_product', 'fcc_combo' ) as $type ) {
	add_filter( 'manage_' . $type . '_posts_columns', 'project_core_fcc_featured_columns' );
	add_action( 'manage_' . $type . '_posts_custom_column', 'project_core_fcc_featured_column', 10, 2 );
}

/** Fill missing fields only; explicit deselection and existing edits survive reruns. */
function project_core_fcc_initialize_featured() {
	$data = project_core_fcc_default_document();
	if ( is_wp_error( $data ) || empty( $data['featured'] ) ) { return; }
	$home = project_core_fcc_page_id( 'home' );
	if ( ! $home ) { return; }
	foreach ( $data['featured'] as $order => $item ) {
		$posts = get_posts( array( 'post_type' => array( 'fcc_product', 'fcc_combo' ), 'post_status' => 'any', 'posts_per_page' => 1, 'fields' => 'ids', 'meta_key' => '_fcc_source_key', 'meta_value' => $item['source'] ) );
		if ( ! $posts ) { return; }
		$id = $posts[0];
		project_core_fcc_seed_meta( $id, 'fcc_featured', 1, 'field_fcc_featured' );
		project_core_fcc_seed_meta( $id, 'fcc_featured_order', $order + 1, 'field_fcc_featured_order' );
		foreach ( $item['fields'] as $name => $field ) {
			$value = $field['value'];
			// Carry over the current homepage values, including intentional blanks.
			if ( ! empty( $field['legacy'] ) ) {
				$parts = array();
				foreach ( $field['legacy'] as $row ) {
					$values = array();
					foreach ( $row as $key ) {
						$values[] = metadata_exists( 'post', $home, $key ) ? get_post_meta( $home, $key, true ) : ( isset( $data['pages']['home']['fields'][ $key ]['value'] ) ? $data['pages']['home']['fields'][ $key ]['value'] : '' );
					}
					$parts[] = implode( ' | ', $values );
				}
				$value = implode( "\n", $parts );
			}
			project_core_fcc_seed_meta( $id, 'fcc_' . $name, $value, 'field_fcc_' . $name );
		}
	}
	update_option( 'project_core_fcc_featured_version', '1', false );
}

function project_core_fcc_upgrade_featured() {
	if ( current_user_can( 'manage_options' ) && '1' !== get_option( 'project_core_fcc_featured_version' ) ) { project_core_fcc_initialize_featured(); }
}
add_action( 'admin_init', 'project_core_fcc_upgrade_featured' );
