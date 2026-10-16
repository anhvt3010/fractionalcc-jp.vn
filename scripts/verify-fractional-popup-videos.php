<?php
/** Read-only local integration checks; fixtures live only in the in-memory post cache. */
if ( 'cli' !== PHP_SAPI ) { exit; }
define( 'WP_ADMIN', true );
require ( getenv( 'PROJECT_WORDPRESS_ROOT' ) ?: dirname( __DIR__ ) ) . '/wp-load.php';
if ( wp_using_ext_object_cache() ) { throw new RuntimeException( 'Run with the native in-memory object cache.' ); }
$checks = 0;
function fcc_video_check( $condition, $message ) {
	global $checks;
	if ( ! $condition ) { throw new RuntimeException( $message ); }
	++$checks;
}
$home = project_core_fcc_page_id( 'home' );
$before = get_post_meta( $home );
$good = array(
	'https://example.test/movie.mp4?token=123' => array( 'https://example.test/movie.mp4?token=123', 'file' ),
	'https://example.test/movie.webm' => array( 'https://example.test/movie.webm', 'file' ),
	'https://example.test/movie.ogv' => array( 'https://example.test/movie.ogv', 'file' ),
	'https://youtu.be/abcdefghijk?si=123' => array( 'https://www.youtube-nocookie.com/embed/abcdefghijk', 'embed' ),
	'https://www.youtube.com/watch?v=abcdefghijk' => array( 'https://www.youtube-nocookie.com/embed/abcdefghijk', 'embed' ),
	'https://www.youtube.com/shorts/abcdefghijk' => array( 'https://www.youtube-nocookie.com/embed/abcdefghijk', 'embed' ),
	'https://www.youtube-nocookie.com/embed/abcdefghijk' => array( 'https://www.youtube-nocookie.com/embed/abcdefghijk', 'embed' ),
	'https://vimeo.com/123456789' => array( 'https://player.vimeo.com/video/123456789', 'embed' ),
	'https://player.vimeo.com/video/123456789?h=abc123' => array( 'https://player.vimeo.com/video/123456789?h=abc123', 'embed' ),
	'https://drive.google.com/file/d/abc_123/view?usp=sharing' => array( 'https://drive.google.com/file/d/abc_123/preview', 'embed' ),
	'https://drive.google.com/open?id=abc_123' => array( 'https://drive.google.com/file/d/abc_123/preview', 'embed' ),
);
foreach ( $good as $input => $expected ) {
	$result = project_child_fcc_video_url( $input );
	fcc_video_check( $result === array( 'url' => $expected[0], 'type' => $expected[1] ), 'URL normalization: ' . $input );
}
foreach ( array( 'javascript:alert(1)', 'data:text/html,test', 'ftp://example.test/movie.mp4', 'https://youtube.com.evil.test/watch?v=abcdefghijk', 'https://example.test/page', 'https://user:pass@example.test/movie.mp4', 'https://youtube.com/watch?v[]=abcdefghijk', array( 'bad' ) ) as $input ) {
	fcc_video_check( array() === project_child_fcc_video_url( $input ), 'Unsafe/unsupported URL rejected.' );
}

$fixture_id = 2000000000;
fcc_video_check( ! get_post( $fixture_id ), 'Fixture ID unused.' );
$fixture = new WP_Post( (object) array( 'ID' => $fixture_id, 'post_type' => 'attachment', 'post_status' => 'inherit', 'post_mime_type' => 'video/mp4', 'guid' => 'https://example.test/upload.mp4', 'filter' => 'raw' ) );
wp_cache_set( $fixture_id, $fixture, 'posts' );
$overrides = array();
$filter = function ( $value, $id, $key, $single ) use ( $home, &$overrides ) {
	if ( $home === (int) $id && array_key_exists( $key, $overrides ) ) { return $single ? $overrides[ $key ] : array( $overrides[ $key ] ); }
	return $value;
};
add_filter( 'get_post_metadata', $filter, 10, 4 );
$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
try {
	foreach ( array( 'brand', 'review' ) as $slot ) {
		foreach ( array( 'file', 'url' ) as $type ) {
			$field = acf_get_field( 'field_fcc_ui_' . $slot . '_video_' . $type );
			fcc_video_check( $field && $type === $field['type'], 'Native field registered.' );
			$field['value'] = '';
			ob_start(); acf_render_field_wrap( $field ); $html = ob_get_clean();
			fcc_video_check( false !== strpos( $html, 'data-key="' . $field['key'] . '"' ), 'Native field renders.' );
		}
		$file_key = 'fcc_ui_' . $slot . '_video_file';
		$url_key = 'fcc_ui_' . $slot . '_video_url';
		$overrides = array( $file_key => '', $url_key => '' );
		fcc_video_check( 'embed' === project_child_fcc_home_video( $slot )['type'], 'Original video preserved.' );
		$overrides[ $url_key ] = 'https://example.test/movie.mp4';
		fcc_video_check( 'file' === project_child_fcc_home_video( $slot )['type'], 'Direct public video selected.' );
		$overrides[ $url_key ] = 'https://youtu.be/abcdefghijk';
		fcc_video_check( 'embed' === project_child_fcc_home_video( $slot )['type'], 'Public watch URL converted.' );
		$overrides[ $file_key ] = (string) $fixture_id;
		fcc_video_check( array( 'url' => $fixture->guid, 'type' => 'file' ) === project_child_fcc_home_video( $slot ), 'Media file takes priority.' );
		wp_set_current_user( $admins[0] );
		fcc_video_check( true === apply_filters( 'acf/validate_value/key=field_' . $file_key, true, (string) $fixture_id ), 'Administrator can select a video attachment.' );
		fcc_video_check( is_string( apply_filters( 'acf/validate_value/key=field_' . $file_key, true, $home ) ), 'Non-attachment rejected.' );
		fcc_video_check( is_string( apply_filters( 'acf/validate_value/key=field_' . $file_key, true, array( $fixture_id ) ) ), 'Malformed media value rejected.' );
		fcc_video_check( is_string( apply_filters( 'acf/validate_value/key=field_' . $url_key, true, 'javascript:alert(1)' ) ), 'Native URL validation rejects unsafe input.' );
		wp_set_current_user( 0 );
		fcc_video_check( is_string( apply_filters( 'acf/validate_value/key=field_' . $file_key, true, (string) $fixture_id ) ), 'Upload permission enforced.' );
		// Render the actual homepage section using the cache-only media fixture.
		ob_start();
		get_template_part( 'template-parts/fractional/home/section-' . ( 'brand' === $slot ? '03' : '07' ) );
		$html = ob_get_clean();
		fcc_video_check( false !== strpos( $html, 'data-fcc-video-modal="' . $fixture->guid . '" data-video-type="file"' ), 'Template selects the uploaded media player.' );
	}
} finally {
	remove_filter( 'get_post_metadata', $filter, 10 );
	wp_cache_delete( $fixture_id, 'posts' );
	wp_set_current_user( 0 );
}
fcc_video_check( $before === get_post_meta( $home ), 'Homepage metadata unchanged.' );
echo 'PASS: ', $checks, ' video URL, media priority, native field, permission and template checks. No database writes.', PHP_EOL;
echo 'Manage: ', admin_url( 'post.php?post=' . $home . '&action=edit' ), PHP_EOL;
