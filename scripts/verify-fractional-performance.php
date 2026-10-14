<?php
/** Integration checks for portable image derivatives and the actual HTML sent by WordPress. */
if ( 'cli' !== PHP_SAPI ) { exit( 1 ); }
$root = getenv( 'PROJECT_WORDPRESS_ROOT' );
if ( ! $root || realpath( $root ) === realpath( dirname( __DIR__ ) ) ) { throw new RuntimeException( 'Isolated PROJECT_WORDPRESS_ROOT required.' ); }
require rtrim( $root, '/\\' ) . '/wp-load.php';
if ( ! get_option( 'project_core_fcc_test_install' ) ) { throw new RuntimeException( 'Marked fixture required.' ); }
set_exception_handler( static function ( $error ) { fwrite( STDERR, $error->getMessage() . PHP_EOL ); exit( 1 ); } );
$checks = 0;
function fcc_perf_check( $ok, $label ) { global $checks; if ( ! $ok ) { throw new RuntimeException( $label ); } ++$checks; }
$url = project_child_fcc_hero_product_image_url();
$variant = project_child_fcc_image_variant( $url );
fcc_perf_check( ! empty( $variant['srcset'] ) && false !== strpos( $variant['url'], '/themes/project-child/' ), 'Media upload resolves to packaged WebP without regenerating uploads.' );
fcc_perf_check( false !== strpos( $variant['srcset'], '480w' ) && false !== strpos( $variant['srcset'], '768w' ), 'Responsive hero candidates exist.' );
fcc_perf_check( array() === project_child_fcc_image_variant( 'https://untrusted.example.invalid/image.png' ), 'Remote URL is not rewritten or fetched.' );
fcc_perf_check( array() === project_child_fcc_image_variant( wp_get_upload_dir()['baseurl'] . '/../../wp-config.php' ), 'Path traversal cannot read a file outside uploads.' );
$uploads = wp_upload_dir();
$file = tempnam( $uploads['path'], 'fcc-performance-' );
try {
	file_put_contents( $file, base64_decode( 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jRZkAAAAASUVORK5CYII=' ) );
	$custom_url = $uploads['url'] . '/' . basename( $file );
	fcc_perf_check( array() === project_child_fcc_image_variant( $custom_url ), 'Customer image bytes never fall back to an unrelated default.' );
} finally { wp_delete_file( $file ); }
$response = wp_remote_get( home_url( '/' ) );
fcc_perf_check( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ), 'Home HTTP 200.' );
$html = wp_remote_retrieve_body( $response );
fcc_perf_check( ! preg_match( '/PHP Warning|Fatal error|fonts\.googleapis\.com|fonts\.gstatic\.com/', $html ), 'No PHP diagnostics or external font dependencies.' );
$doc = new DOMDocument(); libxml_use_internal_errors( true ); $doc->loadHTML( '<?xml encoding="utf-8" ?>' . $html );
$xpath = new DOMXPath( $doc );
$preload = $xpath->query( '//link[@rel="preload"][@as="image"]' )->item( 0 );
$source = $xpath->query( '//picture[.//img[contains(@class,"hero-banner-img")]]/source' )->item( 0 );
fcc_perf_check( $preload && $source && $preload->getAttribute( 'imagesrcset' ) === $source->getAttribute( 'srcset' ) && $preload->getAttribute( 'imagesizes' ) === $source->getAttribute( 'sizes' ), 'Preload and hero select the same candidates without double downloads.' );
fcc_perf_check( 1 === $xpath->query( '//link[@rel="stylesheet"]' )->length, 'Home has one bundled stylesheet.' );
fcc_perf_check( 1 === $xpath->query( '//script[contains(@src,"main.min.js")][@defer]' )->length, 'Interactions use deferred loading.' );
fcc_perf_check( false === strpos( $html, "setProperty('--scrollbar-offset'" ), 'Unused parent layout measurement is absent.' );
foreach ( explode( ', ', $variant['srcset'] ) as $candidate ) {
	$asset_url = preg_replace( '/ [0-9]+w$/', '', $candidate );
	$asset = wp_remote_get( $asset_url );
	fcc_perf_check( ! is_wp_error( $asset ) && 200 === wp_remote_retrieve_response_code( $asset ) && 0 === strpos( wp_remote_retrieve_header( $asset, 'content-type' ), 'image/webp' ), 'WebP candidate is publicly accessible.' );
	fcc_perf_check( false !== strpos( wp_remote_retrieve_header( $asset, 'cache-control' ), '31536000' ), 'Versioned asset has long-lived cache headers.' );
}
$theme = get_stylesheet_directory() . '/assets/fractional/';
$manifest = json_decode( file_get_contents( $theme . 'image-variants.json' ), true );
foreach ( $manifest as $digest => $entry ) {
	fcc_perf_check( $digest === hash_file( 'sha256', $theme . $entry['source'] ), 'Original image bytes match the manifest.' );
	foreach ( $entry['srcset'] as $width => $relative ) {
		$dimensions = getimagesize( $theme . $relative );
		fcc_perf_check( $dimensions && $dimensions[0] === (int) $width && 'image/webp' === $dimensions['mime'], 'Derivative width/format: ' . $relative );
	}
}
echo "PASS: {$checks} image/HTML/cache/security checks; uploaded originals and CMS content untouched.\n";
