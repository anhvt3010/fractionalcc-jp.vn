<?php
/** Read-only release audit; never exports accounts, private settings or order data. */
if ( 'cli' !== PHP_SAPI ) { exit( 1 ); }
require ( getenv( 'PROJECT_WORDPRESS_ROOT' ) ?: dirname( __DIR__ ) ) . '/wp-load.php';
$records = get_posts( array( 'post_type' => array( 'page', 'fcc_site', 'fcc_product', 'fcc_combo', 'fcc_ingredient', 'attachment' ), 'post_status' => array_values( get_post_stati() ), 'posts_per_page' => -1, 'orderby' => 'ID', 'order' => 'ASC' ) );
$counts = array(); $sources = array(); $media_paths = array(); $media_hashes = array(); $missing = array(); $snapshot = array(); $bad_text = array();
foreach ( $records as $post ) {
	$source = get_post_meta( $post->ID, '_fcc_source_key', true );
	if ( 'attachment' !== $post->post_type && ! $source ) { continue; }
	$counts[ $post->post_type ] = ( $counts[ $post->post_type ] ?? 0 ) + 1;
	$meta = get_post_meta( $post->ID ); ksort( $meta );
	$snapshot[ $post->ID ] = array( $post->post_title, $post->post_name, $post->post_content, $post->post_excerpt, $post->post_status, $post->menu_order, $meta );
	if ( $source ) { $sources[ $source ][] = $post->ID; }
	if ( 'attachment' === $post->post_type ) {
		$file = get_attached_file( $post->ID );
		$media_paths[ str_replace( '\\', '/', $file ) ][] = $post->ID;
		if ( ! is_readable( $file ) ) { $missing[] = $post->ID; }
		else { $media_hashes[ hash_file( 'sha256', $file ) ][] = $post->ID; }
	}
	foreach ( $meta as $key => $values ) {
		if ( 0 !== strpos( $key, 'fcc_' ) ) { continue; }
		foreach ( $values as $value ) {
			if ( preg_match( '/\x{FFFD}|\p{L}\?\p{L}/u', $value ) ) { $bad_text[] = array( 'id' => $post->ID, 'field' => $key ); }
		}
	}
}
$duplicates = static function ( $items ) { return array_filter( $items, static function ( $ids ) { return count( $ids ) > 1; } ); };
$schema = project_child_fcc_schema(); $schema_count = array();
foreach ( $schema as $kind => $sections ) { $schema_count[ $kind ] = array_sum( array_map( 'count', $sections ) ); }
$home = project_core_fcc_page_id( 'home' ); $videos = array();
foreach ( array( 'brand', 'review' ) as $slot ) {
	$resolved = project_child_fcc_home_video( $slot );
	$videos[ $slot ] = array( 'file_id' => absint( get_post_meta( $home, 'fcc_ui_' . $slot . '_video_file', true ) ), 'url_saved' => metadata_exists( 'post', $home, 'fcc_ui_' . $slot . '_video_url' ), 'resolved_type' => $resolved['type'] ?? '', 'resolved_host' => wp_parse_url( $resolved['url'] ?? '', PHP_URL_HOST ) );
}
$data = project_core_fcc_default_document(); $valid = project_core_fcc_validate_defaults( $data );
$report = array(
	'runtime' => array( 'php' => PHP_VERSION, 'wordpress' => get_bloginfo( 'version' ), 'child' => wp_get_theme()->get( 'Version' ), 'parent' => wp_get_theme( get_template() )->get( 'Version' ), 'acf' => defined( 'ACF_VERSION' ) ? ACF_VERSION : '', 'active_plugins' => get_option( 'active_plugins' ), 'defaults' => $data['version'], 'seed' => get_option( 'project_core_fcc_seed_version' ) ),
	'content_sha256' => hash( 'sha256', wp_json_encode( $snapshot ) ), 'counts' => $counts, 'schema_fields' => $schema_count,
	'duplicate_sources' => $duplicates( $sources ), 'duplicate_media_paths' => array_values( $duplicates( $media_paths ) ), 'duplicate_media_bytes' => array_values( $duplicates( $media_hashes ) ), 'missing_media' => $missing, 'suspect_saved_text' => $bad_text, 'videos' => $videos,
	'defaults_valid' => ! is_wp_error( $valid ), 'defaults_error' => is_wp_error( $valid ) ? $valid->get_error_code() : null,
);
echo wp_json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ), PHP_EOL;
