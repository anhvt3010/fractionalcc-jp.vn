<?php
/** Read-only HTTP verification; optional isolated-fixture checks restore their edits. */
if ( 'cli' !== PHP_SAPI ) { exit( 1 ); }
$root = getenv( 'PROJECT_WORDPRESS_ROOT' ) ?: dirname( __DIR__ );
require rtrim( $root, '/\\' ) . '/wp-load.php';
$checks = 0;
function fcc_seo_check( $ok, $label ) {
	global $checks;
	if ( ! $ok ) { throw new RuntimeException( $label ); }
	++$checks;
}
function fcc_seo_document( $url ) {
	$response = wp_remote_get( $url, array( 'timeout' => 20 ) );
	fcc_seo_check( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ), 'HTTP 200: ' . $url );
	$html = wp_remote_retrieve_body( $response );
	fcc_seo_check( ! preg_match( '/PHP Warning|Fatal error|Notice:|Deprecated:/', $html ), 'No PHP diagnostics: ' . $url );
	$doc = new DOMDocument();
	libxml_use_internal_errors( true );
	$doc->loadHTML( '<?xml encoding="utf-8" ?>' . $html );
	return new DOMXPath( $doc );
}
$records = array();
foreach ( array( 'home', 'catalog', 'concerns', 'policies', 'thankyou' ) as $kind ) { $records[ project_core_fcc_page_id( $kind ) ] = $kind; }
foreach ( project_core_fcc_posts( 'fcc_product' ) as $product ) { $records[ $product->ID ] = 'product'; }
$titles = array();
foreach ( $records as $id => $kind ) {
	$url = get_permalink( $id );
	$x = fcc_seo_document( add_query_arg( 'utm_source', 'seo-verification', $url ) );
	fcc_seo_check( 1 === $x->query( '//head/title' )->length, 'One title: ' . $id );
	$title = $x->query( '//head/title' )->item( 0 )->textContent;
	fcc_seo_check( ! in_array( $title, $titles, true ), 'Unique title: ' . $id );
	$titles[] = $title;
	fcc_seo_check( 1 === $x->query( '//meta[@name="description"]' )->length, 'One description: ' . $id );
	$description = $x->query( '//meta[@name="description"]' )->item( 0 )->getAttribute( 'content' );
	fcc_seo_check( '' !== trim( $description ), 'Nonempty description: ' . $id );
	fcc_seo_check( 1 === $x->query( '//link[@rel="canonical"]' )->length && $url === $x->query( '//link[@rel="canonical"]' )->item( 0 )->getAttribute( 'href' ), 'Single canonical excludes UTM: ' . $id );
	fcc_seo_check( 1 === $x->query( '//meta[@property="og:title"]' )->length && $title === $x->query( '//meta[@property="og:title"]' )->item( 0 )->getAttribute( 'content' ), 'OG title matches: ' . $id );
	fcc_seo_check( 1 === $x->query( '//meta[@name="twitter:title"]' )->length && $title === $x->query( '//meta[@name="twitter:title"]' )->item( 0 )->getAttribute( 'content' ), 'Twitter title matches: ' . $id );
	fcc_seo_check( $description === $x->query( '//meta[@name="twitter:description"]' )->item( 0 )->getAttribute( 'content' ), 'Social description matches: ' . $id );
	fcc_seo_check( 0 === $x->query( '//meta[@name="keywords"]' )->length, 'No keyword stuffing meta: ' . $id );
	fcc_seo_check( 1 === $x->query( '//h1' )->length, 'Single visible H1: ' . $id );
	$robots = $x->query( '//meta[@name="robots"]' );
	fcc_seo_check( 1 === $robots->length && false !== strpos( $robots->item( 0 )->getAttribute( 'content' ), 'noindex' ), 'Local/receipt noindex preserved: ' . $id );
	$schemas = $x->query( '//script[@type="application/ld+json"]' );
	if ( 'thankyou' === $kind ) { fcc_seo_check( 0 === $schemas->length, 'Receipt has no public schema.' ); continue; }
	fcc_seo_check( 1 === $schemas->length, 'One JSON-LD graph: ' . $id );
	$schema = json_decode( $schemas->item( 0 )->textContent, true );
	fcc_seo_check( JSON_ERROR_NONE === json_last_error() && 'https://schema.org' === $schema['@context'], 'Valid JSON-LD: ' . $id );
	$graph = array_column( $schema['@graph'], null, '@type' );
	fcc_seo_check( isset( $graph['WebSite'], $graph['WebPage'] ) && $url === $graph['WebPage']['url'], 'Website/page graph: ' . $id );
	if ( 'product' === $kind ) {
		$p = project_core_fcc_product( $id, false );
		$node = $graph['Product'] ?? array();
		fcc_seo_check( $p['name'] === ( $node['name'] ?? '' ) && $p['image'] === ( $node['image'] ?? '' ), 'Product identity/image match CMS: ' . $id );
		if ( null !== $p['salePrice'] && $p['salePrice'] > 0 ) {
			fcc_seo_check( (float) $p['salePrice'] === (float) ( $node['offers']['price'] ?? -1 ) && 'VND' === $node['offers']['priceCurrency'], 'Offer price/currency match CMS: ' . $id );
		} else {
			fcc_seo_check( ! isset( $node['offers'] ), 'Missing/invalid price omits an offer: ' . $id );
		}
		fcc_seo_check( ! isset( $node['aggregateRating'] ) && ! isset( $node['review'] ) && ! isset( $node['offers']['availability'] ), 'No invented reviews or stock: ' . $id );
		fcc_seo_check( isset( $graph['BreadcrumbList'] ) && 3 === count( $graph['BreadcrumbList']['itemListElement'] ), 'Visible product breadcrumb: ' . $id );
		$has_social_image = $p['image'] || get_post_meta( $id, 'fcc_product_seo_image', true );
		fcc_seo_check( ( $has_social_image ? 1 : 0 ) === $x->query( '//meta[@property="og:image"]' )->length, 'Product social image follows the saved image selection: ' . $id );
	}
}
$provider = new WP_Sitemaps_Posts();
$pages = $provider->get_url_list( 1, 'page' );
fcc_seo_check( ! in_array( project_core_fcc_url( 'thankyou' ), array_column( $pages, 'loc' ), true ), 'WordPress sitemap excludes thank-you.' );
$products = $provider->get_url_list( 1, 'fcc_product' );
fcc_seo_check( count( project_core_fcc_posts( 'fcc_product' ) ) === count( $products ), 'Native sitemap includes published products.' );

if ( in_array( '--fixture', $argv, true ) ) {
	fcc_seo_check( realpath( $root ) !== realpath( dirname( __DIR__ ) ) && get_option( 'project_core_fcc_test_install' ), 'Isolated marked fixture required for edits.' );
	$admin = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
	wp_set_current_user( $admin[0] );
	$id = project_core_fcc_posts( 'fcc_product' )[0]->ID;
	$GLOBALS['wp_query'] = new WP_Query( array( 'p' => $id, 'post_type' => 'fcc_product' ) );
	$old_title = get_post_meta( $id, 'fcc_product_seo_title', true );
	$old_description = get_post_meta( $id, 'fcc_product_seo_description', true );
	$old_price = get_post_meta( $id, 'fcc_salePrice', true );
	$old_seed = get_option( 'project_core_fcc_seed_version' );
	$target_version = project_core_fcc_default_document()['version'];
	try {
		update_post_meta( $id, 'fcc_product_seo_title', 'Serum "A&B" <script>unsafe()</script>' );
		update_post_meta( $id, 'fcc_product_seo_description', 'Mô tả "dưỡng ẩm" & <b>chăm sóc</b>' );
		$custom_title = get_post_meta( $id, 'fcc_product_seo_title', true );
		fcc_seo_check( ! is_wp_error( project_core_fcc_initialize() ) && $custom_title === get_post_meta( $id, 'fcc_product_seo_title', true ), 'Rerun preserves edited SEO metadata.' );
		update_option( 'project_core_fcc_seed_version', '1.0.9' );
		update_post_meta( $id, 'fcc_product_seo_description', '' );
		fcc_seo_check( ! is_wp_error( project_core_fcc_initialize() ) && $target_version === get_option( 'project_core_fcc_seed_version' ) && $custom_title === get_post_meta( $id, 'fcc_product_seo_title', true ) && '' === get_post_meta( $id, 'fcc_product_seo_description', true ), 'Upgrade from seed 1.0.9 preserves edited title and cleared description.' );
		update_post_meta( $id, 'fcc_product_seo_description', 'Mô tả "dưỡng ẩm" & <b>chăm sóc</b>' );
		ob_start(); project_child_fcc_metadata(); $html = ob_get_clean();
		fcc_seo_check( false === strpos( $html, '<script>unsafe()' ) && false !== strpos( $html, '&quot;' ), 'Editable metadata escapes HTML and quotes.' );
		foreach ( array( '', 0, -1 ) as $price ) {
			update_post_meta( $id, 'fcc_salePrice', $price );
			$graph = project_child_fcc_seo_graph( project_core_fcc_product( $id, false ), '', get_permalink( $id ) );
			$types = array_column( $graph, null, '@type' );
			fcc_seo_check( ! isset( $types['Product']['offers'] ), 'Missing/invalid price never becomes a zero-price offer.' );
		}
		$owner = static function () { return true; };
		add_filter( 'project_child_fcc_external_seo', $owner );
		ob_start(); project_child_fcc_metadata(); $html = ob_get_clean();
		fcc_seo_check( '' === $html && 'External title' === project_child_fcc_title( 'External title' ), 'External SEO owner receives title/metadata ownership.' );
		remove_filter( 'project_child_fcc_external_seo', $owner );
		$_GET['preview'] = 'true';
		$GLOBALS['wp_query']->is_preview = true;
		ob_start(); project_child_fcc_metadata(); $html = ob_get_clean();
		fcc_seo_check( '' === $html, 'No private preview metadata.' );
		$GLOBALS['wp_query']->is_preview = false;
		unset( $_GET['preview'] );
	} finally {
		update_post_meta( $id, 'fcc_product_seo_title', $old_title );
		update_post_meta( $id, 'fcc_product_seo_description', $old_description );
		update_post_meta( $id, 'fcc_salePrice', $old_price );
		update_option( 'project_core_fcc_seed_version', $old_seed );
	}
	ob_start(); project_core_fcc_seo_guide(); $guide = ob_get_clean();
	fcc_seo_check( false !== strpos( $guide, 'retinol là gì' ) && false !== strpos( $guide, 'Paula' ) && false !== strpos( $guide, 'thuần chay' ), 'Admin keyword plan covers requested clusters.' );
	wp_set_current_user( 0 );
	$deny = static function () { return static function ( $message, $title, $args ) { throw new RuntimeException( 'denied:' . ( $args['response'] ?? 0 ) ); }; };
	add_filter( 'wp_die_handler', $deny );
	try {
		project_core_fcc_seo_guide();
		fcc_seo_check( false, 'Anonymous visitor must not access the keyword admin page.' );
	} catch ( RuntimeException $error ) {
		fcc_seo_check( 'denied:403' === $error->getMessage(), 'Anonymous access to SEO admin denied with HTTP 403.' );
	} finally { remove_filter( 'wp_die_handler', $deny ); }
}
echo "PASS: {$checks} SEO checks across " . count( $records ) . " pages.\n";
