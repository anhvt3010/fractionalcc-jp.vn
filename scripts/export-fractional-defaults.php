<?php
/** Export explicitly approved CMS content; never export accounts, orders or credentials. */
if ( 'cli' !== PHP_SAPI ) { exit; }
$args = getopt( '', array( 'user:', 'version:', 'approve' ) );
if ( ! isset( $args['user'], $args['version'], $args['approve'] ) || ! preg_match( '/^\d+\.\d+\.\d+$/', $args['version'] ) ) { exit( "Usage: php scripts/export-fractional-defaults.php --user=<administrator-id> --version=1.0.0 --approve\n" ); }
require ( getenv( 'PROJECT_WORDPRESS_ROOT' ) ?: dirname( __DIR__ ) ) . '/wp-load.php';
wp_set_current_user( absint( $args['user'] ) );
if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'upload_files' ) || 'project-child' !== get_stylesheet() ) { throw new RuntimeException( 'Administrator and Project Child required.' ); }
$data = project_core_fcc_default_document();
if ( is_wp_error( $data ) ) { throw new RuntimeException( 'Defaults unavailable.' ); }
$media = array(); $media_ids = array(); $sources = array();
$theme = get_stylesheet_directory() . '/assets/fractional/';
$hash_paths = array();
foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $theme . 'assets/images', FilesystemIterator::SKIP_DOTS ) ) as $file ) {
	if ( $file->isFile() && preg_match( '/\.(png|jpe?g|webp)$/i', $file->getFilename() ) ) { $hash_paths[ hash_file( 'sha256', $file->getPathname() ) ] = str_replace( '\\', '/', substr( $file->getPathname(), strlen( $theme ) ) ); }
}
function fcc_export_media( $id ) {
	global $media, $media_ids, $theme, $hash_paths;
	$id = absint( $id );
	if ( ! $id ) { return ''; }
	if ( isset( $media_ids[ $id ] ) ) { return $media_ids[ $id ]; }
	$file = get_attached_file( $id );
	if ( ! wp_attachment_is_image( $id ) || ! is_readable( $file ) ) { throw new RuntimeException( 'Missing CMS image: ' . $id ); }
	$hash = hash_file( 'sha256', $file );
	$source = (string) get_post_meta( $id, '_fcc_asset_source', true );
	if ( ! preg_match( '#^assets/images/[a-zA-Z0-9/_-]+\.(png|jpe?g|webp)$#', $source ) || ! is_readable( $theme . $source ) || hash_file( 'sha256', $theme . $source ) !== $hash ) {
		$source = $hash_paths[ $hash ] ?? 'assets/images/approved/' . substr( $hash, 0, 16 ) . '-' . sanitize_file_name( basename( $file ) );
		if ( ! is_readable( $theme . $source ) ) { wp_mkdir_p( dirname( $theme . $source ) ); if ( ! copy( $file, $theme . $source ) ) { throw new RuntimeException( 'Image export failed.' ); } }
	}
	$post = get_post( $id );
	$media[ $source ] = array( 'sha256' => $hash, 'title' => $post->post_title, 'alt' => (string) get_post_meta( $id, '_wp_attachment_image_alt', true ), 'caption' => $post->post_excerpt, 'description' => $post->post_content );
	return $media_ids[ $id ] = $source;
}
foreach ( get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'posts_per_page' => -1 ) ) as $attachment ) { if ( wp_attachment_is_image( $attachment->ID ) ) { fcc_export_media( $attachment->ID ); } }
function fcc_export_value( $value ) {
	global $media_ids;
	if ( is_array( $value ) ) { return array_map( 'fcc_export_value', $value ); }
	if ( ! is_string( $value ) ) { return $value; }
	foreach ( $media_ids as $id => $path ) {
		$value = str_replace( wp_get_attachment_url( $id ), '{{fcc_media_url:' . $path . '}}', $value );
		$value = preg_replace( '/wp-image-' . $id . '(?!\d)/', 'wp-image-{{fcc_media_id:' . $path . '}}', $value );
		$value = preg_replace( '/("id"\s*:\s*)' . $id . '(?!\d)/', '$1"{{fcc_media_id:' . $path . '}}"', $value );
	}
	return str_replace( home_url(), '{{fcc_site_url}}', $value );
}
function fcc_export_field( $id, $name, $fallback = array() ) {
	global $sources;
	$key = get_post_meta( $id, '_' . $name, true ) ?: ( $fallback['key'] ?? 'field_' . $name );
	$field = acf_get_field( $key );
	$type = $field['type'] ?? ( $fallback['type'] ?? 'text' );
	$value = metadata_exists( 'post', $id, $name ) ? get_post_meta( $id, $name, true ) : ( $fallback['value'] ?? '' );
	if ( 'image' === $type ) { $value = is_numeric( $value ) ? fcc_export_media( $value ) : $value; }
	elseif ( 'relationship' === $type ) { $value = array_map( function ( $post_id ) use ( $sources ) { if ( ! isset( $sources[ $post_id ] ) ) { throw new RuntimeException( 'Unmapped relationship.' ); } return $sources[ $post_id ]; }, (array) $value ); }
	else { $value = fcc_export_value( $value ); }
	return array( 'type' => $type, 'key' => $key, 'value' => $value );
}
$pages = get_option( 'project_core_fcc_pages' );
foreach ( $pages as $scope => $id ) { $sources[ $id ] = 'page_' . $scope; }
foreach ( project_core_fcc_posts( array( 'fcc_product', 'fcc_combo', 'fcc_ingredient' ) ) as $post ) { $sources[ $post->ID ] = get_post_meta( $post->ID, '_fcc_source_key', true ); if ( ! $sources[ $post->ID ] ) { throw new RuntimeException( 'Content requires a stable source key: ' . $post->ID ); } }
foreach ( $data['pages'] as $scope => &$page ) {
	$id = $pages[ $scope ]; $post = get_post( $id );
	$page['title'] = $post->post_title; $page['slug'] = $post->post_name; $page['content'] = fcc_export_value( $post->post_content ); $page['excerpt'] = fcc_export_value( $post->post_excerpt ); $page['menuOrder'] = (int) $post->menu_order;
	$names = array_unique( array_merge( array_keys( $page['fields'] ), array_filter( array_keys( get_post_meta( $id ) ), function ( $name ) { return 0 === strpos( $name, 'fcc_' ) && 0 !== strpos( $name, 'fcc_regimen_' ); } ) ) );
	foreach ( $names as $name ) { $page['fields'][ $name ] = fcc_export_field( $id, $name, $page['fields'][ $name ] ?? array() ); }
}
unset( $page );
$products = array(); $used_ingredients = array();
foreach ( project_core_fcc_posts( 'fcc_product' ) as $post ) {
	$source_id = absint( get_post_meta( $post->ID, '_fcc_source_id', true ) );
	$old = array_values( array_filter( $data['products'], function ( $entry ) use ( $source_id ) { return $source_id === $entry['id']; } ) );
	$product = array( 'id' => $source_id, 'name' => $post->post_title, 'slug' => $post->post_name, 'menuOrder' => (int) $post->menu_order, 'content' => fcc_export_value( $post->post_content ), 'excerpt' => fcc_export_value( $post->post_excerpt ), 'gallery' => array(), 'actives' => array(), 'fields' => array() );
	foreach ( array_keys( $old[0] ?? project_core_fcc_product( $post->ID ) ) as $name ) {
		if ( in_array( $name, array( 'id', 'name', 'slug', 'menuOrder', 'content', 'excerpt', 'gallery', 'actives', 'fields', 'url', 'imageAlt', 'galleryAlts', 'saveAmount' ), true ) ) { continue; }
		$product[ $name ] = 'image' === $name ? fcc_export_media( get_post_meta( $post->ID, 'fcc_image', true ) ) : fcc_export_value( get_post_meta( $post->ID, 'fcc_' . $name, true ) );
	}
	foreach ( parse_blocks( $post->post_content ) as $block ) { if ( 'core/gallery' === $block['blockName'] ) { foreach ( $block['innerBlocks'] as $image ) { if ( ! empty( $image['attrs']['id'] ) ) { $product['gallery'][] = fcc_export_media( $image['attrs']['id'] ); } } } }
	foreach ( (array) get_post_meta( $post->ID, 'fcc_actives', true ) as $ingredient_id ) {
		$ingredient = get_post( $ingredient_id ); $used_ingredients[] = (int) $ingredient_id;
		$product['actives'][] = array( 'name' => $ingredient->post_title, 'slug' => $ingredient->post_name, 'source' => $sources[ $ingredient_id ], 'tag' => get_post_meta( $ingredient_id, 'fcc_tag', true ), 'desc' => get_post_meta( $ingredient_id, 'fcc_description', true ) );
	}
	foreach ( array_keys( get_post_meta( $post->ID ) ) as $name ) { if ( 0 === strpos( $name, 'fcc_' ) && ! array_key_exists( substr( $name, 4 ), $product ) ) { $product['fields'][ $name ] = fcc_export_field( $post->ID, $name ); } }
	$products[] = $product;
}
$combos = array();
foreach ( project_core_fcc_posts( 'fcc_combo' ) as $post ) {
	$combo_id = substr( $sources[ $post->ID ], strlen( 'combo_' ) );
	$old = array_values( array_filter( $data['combos'], function ( $entry ) use ( $combo_id ) { return $combo_id === (string) $entry['id']; } ) );
	$combo = array( 'id' => $combo_id, 'name' => $post->post_title, 'slug' => $post->post_name, 'menuOrder' => (int) $post->menu_order, 'content' => fcc_export_value( $post->post_content ), 'excerpt' => fcc_export_value( $post->post_excerpt ), 'fields' => array() );
	foreach ( array_keys( $old[0] ?? array() ) as $name ) { if ( in_array( $name, array( 'id', 'name', 'slug', 'menuOrder', 'content', 'excerpt', 'fields' ), true ) ) { continue; } $value = get_post_meta( $post->ID, 'fcc_' . $name, true ); if ( 'image' === $name ) { $value = fcc_export_media( $value ); } elseif ( 'productIds' === $name ) { $value = array_map( function ( $id ) { return absint( get_post_meta( $id, '_fcc_source_id', true ) ); }, (array) $value ); } $combo[ $name ] = fcc_export_value( $value ); }
	foreach ( array_keys( get_post_meta( $post->ID ) ) as $name ) { if ( 0 === strpos( $name, 'fcc_' ) && ! array_key_exists( substr( $name, 4 ), $combo ) ) { $combo['fields'][ $name ] = fcc_export_field( $post->ID, $name ); } }
	$combos[] = $combo;
}
$data['ingredients'] = array();
foreach ( project_core_fcc_posts( 'fcc_ingredient' ) as $post ) { if ( ! in_array( $post->ID, $used_ingredients, true ) ) { $data['ingredients'][] = array( 'source' => $sources[ $post->ID ], 'name' => $post->post_title, 'slug' => $post->post_name, 'tag' => get_post_meta( $post->ID, 'fcc_tag', true ), 'desc' => get_post_meta( $post->ID, 'fcc_description', true ) ); } }
$data['products'] = $products; $data['combos'] = $combos;
foreach ( $data['brand'] as $name => &$value ) { if ( metadata_exists( 'post', $pages['shared'], 'fcc_' . $name ) ) { $value = fcc_export_value( get_post_meta( $pages['shared'], 'fcc_' . $name, true ) ); } } unset( $value );
$data['pages']['shared']['fields']['fcc_content_status']['value'] = 'approved';
$data['regimens'] = array();
foreach ( project_core_fcc_regimens() as $slug => $definition ) { $data['regimens'][ $slug ] = array_map( function ( $id ) use ( $sources ) { return $sources[ $id ]; }, project_core_fcc_regimen_ids( get_post_meta( $pages['concerns'], 'fcc_' . $definition['name'], true ) ) ); }
$data['featured'] = array_map( function ( $post ) use ( $sources ) { return array( 'source' => $sources[ $post->ID ], 'fields' => array() ); }, project_core_fcc_featured_posts() );
$data['homeCatalog'] = array_map( function ( $post ) use ( $sources ) { return $sources[ $post->ID ]; }, project_core_fcc_selected_posts( 'home_catalog', 6 ) );
$data['settings'] = array();
foreach ( array( 'blogname', 'blogdescription', 'timezone_string', 'gmt_offset', 'date_format', 'time_format', 'start_of_week', 'permalink_structure' ) as $name ) { $data['settings'][ $name ] = get_option( $name ); }
$data['version'] = $args['version'];
$data['approval'] = array( 'status' => 'approved', 'date' => current_time( 'Y-m-d' ), 'source' => 'User-approved current WordPress content, media, catalog and selections.' );
$data['media'] = $media;
if ( ! empty( $data['catalogSource'] ) ) { $data['catalogSource']['snapshotOverrides'] = 'Current CMS values approved by the user; Excel remains historical provenance.'; }
$json = wp_json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . "\n";
if ( false !== strpos( $json, 'http://localhost' ) || false !== strpos( $json, 'D:\\xampp' ) ) { throw new RuntimeException( 'Local references remain in exported defaults.' ); }
if ( false === file_put_contents( WP_PLUGIN_DIR . '/project-core/defaults/fractional.json', $json ) ) { throw new RuntimeException( 'Defaults export failed.' ); }
echo 'Exported approved defaults: ' . count( $data['products'] ) . ' products, ' . count( $data['combos'] ) . ' combos, ' . count( $media ) . " media; portable relationships and URLs.\n";
