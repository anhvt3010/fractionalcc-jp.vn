<?php
/** Repeatable, scoped photo refresh; keep customer changes and historical orders. @package Project_Core */
defined( 'ABSPATH' ) || exit;

function project_core_fcc_media_matches_source( $id, $path, $data ) {
	$expected = $data['media'][ $path ]['sha256'] ?? ( $data['mediaUpdate']['previousMedia'][ $path ]['sha256'] ?? '' );
	if ( ! $id || get_post_meta( $id, '_fcc_asset_source', true ) !== $path || ! $expected ) { return false; }
	$file = get_attached_file( $id );
	return $file && is_readable( $file ) && hash_file( 'sha256', $file ) === $expected;
}

function project_core_fcc_upgrade_media( $data ) {
	$update = $data['mediaUpdate'] ?? array();
	if ( ! $update || version_compare( (string) get_option( 'project_core_fcc_media_version', '0.0.0' ), $update['version'], '>=' ) ) { return true; }
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'upload_files' ) ) { return new WP_Error( 'fcc_media_permissions', 'Không có quyền cập nhật bộ ảnh.' ); }
	$products = array_column( $data['products'], null, 'id' );
	foreach ( $update['products'] as $change ) {
		$id = project_core_fcc_legacy_product_id( $change['id'] );
		if ( ! $id ) { continue; }
		$product = $products[ $change['id'] ];
		$primary = absint( get_post_meta( $id, 'fcc_image', true ) );
		$customized = (bool) get_post_meta( $id, '_fcc_product_media_customized', true );
		$replace_primary = ! $customized && project_core_fcc_media_matches_source( $primary, $change['previousImage'], $data );
		$gallery = project_core_fcc_gallery_ids( $id );
		$replace_gallery = ! $customized && count( $gallery ) === count( $change['previousGallery'] );
		foreach ( $gallery as $index => $image_id ) {
			if ( ! isset( $change['previousGallery'][ $index ] ) || ! project_core_fcc_media_matches_source( $image_id, $change['previousGallery'][ $index ], $data ) ) { $replace_gallery = false; break; }
		}
		$ids = array();
		foreach ( $product['gallery'] as $path ) {
			$image_id = project_core_fcc_seed_media( $path );
			if ( is_wp_error( $image_id ) ) { return $image_id; }
			$ids[] = $image_id;
		}
		if ( $replace_gallery ) {
			$content = project_core_fcc_gallery_content( (string) get_post_field( 'post_content', $id ), $ids );
			$result = wp_update_post( wp_slash( array( 'ID' => $id, 'post_content' => $content ) ), true );
			if ( is_wp_error( $result ) ) { return $result; }
		}
		if ( $replace_primary ) {
			$image_id = project_core_fcc_seed_media( $product['image'] );
			if ( is_wp_error( $image_id ) ) { return $image_id; }
			update_post_meta( $id, 'fcc_image', $image_id );
		}
	}
	update_option( 'project_core_fcc_media_version', $update['version'], false );
	return true;
}
