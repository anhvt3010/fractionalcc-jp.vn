<?php
/** Homepage popup video sources and native media validation. @package Project_Child */
defined( 'ABSPATH' ) || exit;

/** Resolve supported public video URLs without fetching remote content. */
function project_child_fcc_video_url( $url ) {
	if ( ! is_string( $url ) ) { return array(); }
	$url = esc_url_raw( trim( $url ), array( 'http', 'https' ) );
	$parts = wp_parse_url( $url );
	if ( ! $parts || empty( $parts['host'] ) || empty( $parts['scheme'] ) || ! in_array( strtolower( $parts['scheme'] ), array( 'http', 'https' ), true ) || isset( $parts['user'] ) || isset( $parts['pass'] ) ) { return array(); }
	$path = $parts['path'] ?? '';
	if ( preg_match( '/\.(mp4|webm|ogv|ogg)$/i', $path ) ) { return array( 'url' => $url, 'type' => 'file' ); }
	$host = strtolower( preg_replace( '/^www\./i', '', $parts['host'] ) );
	$query = array();
	parse_str( $parts['query'] ?? '', $query );
	$id = '';
	if ( 'youtu.be' === $host ) {
		$id = trim( $path, '/' );
	} elseif ( in_array( $host, array( 'youtube.com', 'm.youtube.com', 'youtube-nocookie.com' ), true ) ) {
		if ( preg_match( '#^/(?:embed|shorts|live)/([A-Za-z0-9_-]{11})/?$#', $path, $match ) ) { $id = $match[1]; }
		elseif ( '/watch' === $path && isset( $query['v'] ) && is_string( $query['v'] ) ) { $id = $query['v']; }
	}
	if ( preg_match( '/^[A-Za-z0-9_-]{11}$/', $id ) ) { return array( 'url' => 'https://www.youtube-nocookie.com/embed/' . $id, 'type' => 'embed' ); }
	if ( in_array( $host, array( 'vimeo.com', 'player.vimeo.com' ), true ) && preg_match( '#^/(?:video/)?([0-9]+)/?$#', $path, $match ) ) {
		$embed = 'https://player.vimeo.com/video/' . $match[1];
		if ( isset( $query['h'] ) && is_string( $query['h'] ) && preg_match( '/^[A-Za-z0-9]+$/', $query['h'] ) ) { $embed = add_query_arg( 'h', $query['h'], $embed ); }
		return array( 'url' => $embed, 'type' => 'embed' );
	}
	if ( 'drive.google.com' === $host ) {
		if ( preg_match( '#^/file/d/([A-Za-z0-9_-]+)(?:/(?:view|preview))?/?$#', $path, $match ) ) { $id = $match[1]; }
		elseif ( in_array( $path, array( '/open', '/uc' ), true ) && isset( $query['id'] ) && is_string( $query['id'] ) ) { $id = $query['id']; }
		if ( preg_match( '/^[A-Za-z0-9_-]+$/', $id ) ) { return array( 'url' => 'https://drive.google.com/file/d/' . $id . '/preview', 'type' => 'embed' ); }
	}
	return array();
}

/** Keep the current videos until an editor chooses a replacement. */
function project_child_fcc_home_video( $slot ) {
	if ( ! in_array( $slot, array( 'brand', 'review' ), true ) ) { return array(); }
	$home = project_core_fcc_page_id( 'home' );
	$file = get_post_meta( $home, 'fcc_ui_' . $slot . '_video_file', true );
	if ( is_scalar( $file ) && ctype_digit( (string) $file ) && in_array( get_post_mime_type( (int) $file ), array( 'video/mp4', 'video/webm', 'video/ogg' ), true ) ) {
		$url = wp_get_attachment_url( (int) $file );
		if ( $url ) { return array( 'url' => $url, 'type' => 'file' ); }
	}
	$defaults = array(
		'brand'  => 'https://drive.google.com/file/d/1OGBTCSUTvzjI4kulOUmRqrY9mgB0m8TY/preview',
		'review' => 'https://drive.google.com/file/d/1zQq81OnZVzq3EmrQOU8UP_Gz0ccaxSBo/preview',
	);
	$url = get_post_meta( $home, 'fcc_ui_' . $slot . '_video_url', true );
	return project_child_fcc_video_url( $url ?: $defaults[ $slot ] );
}

/** ACF's native save supplies its permission and nonce checks. */
function project_child_fcc_validate_video_file( $valid, $value ) {
	if ( true !== $valid || empty( $value ) ) { return $valid; }
	if ( ! current_user_can( 'upload_files' ) ) { return 'Bạn không có quyền chọn video.'; }
	if ( ! is_scalar( $value ) || ! ctype_digit( (string) $value ) || 'attachment' !== get_post_type( (int) $value ) || ! in_array( get_post_mime_type( (int) $value ), array( 'video/mp4', 'video/webm', 'video/ogg' ), true ) ) { return 'Chọn video MP4, WebM hoặc OGV trong Thư viện Media.'; }
	return $valid;
}

function project_child_fcc_validate_video_url( $valid, $value ) {
	if ( true !== $valid || empty( $value ) ) { return $valid; }
	return project_child_fcc_video_url( $value ) ? true : 'Nhập URL video MP4, WebM, OGV hoặc liên kết YouTube, Vimeo, Google Drive công khai.';
}

foreach ( array( 'brand', 'review' ) as $project_child_video_slot ) {
	add_filter( 'acf/validate_value/key=field_fcc_ui_' . $project_child_video_slot . '_video_file', 'project_child_fcc_validate_video_file', 10, 2 );
	add_filter( 'acf/validate_value/key=field_fcc_ui_' . $project_child_video_slot . '_video_url', 'project_child_fcc_validate_video_url', 10, 2 );
}
unset( $project_child_video_slot );
