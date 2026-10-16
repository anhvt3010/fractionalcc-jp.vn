<?php
/** Editable metadata and structured data for the existing Fractional CC templates.
 * @package Project_Child
 */
defined( 'ABSPATH' ) || exit;

/** A dedicated SEO plugin owns public metadata when one is active. */
function project_child_fcc_external_seo() {
	return (bool) apply_filters( 'project_child_fcc_external_seo', defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'THE_SEO_FRAMEWORK_VERSION' ) );
}

function project_child_fcc_seo_context() {
	return project_child_fcc_kind() && function_exists( 'project_core_fcc_page_id' ) && ! is_preview() && ! is_feed() && ! post_password_required();
}

/** Read the queried record, including a second page using the same template. */
function project_child_fcc_seo_value( $suffix ) {
	$value = get_post_meta( get_queried_object_id(), 'fcc_' . project_child_fcc_kind() . '_seo_' . $suffix, true );
	return is_scalar( $value ) ? trim( wp_strip_all_tags( (string) $value ) ) : '';
}

function project_child_fcc_title( $title ) {
	if ( ! project_child_fcc_seo_context() || project_child_fcc_external_seo() ) { return $title; }
	$value = project_child_fcc_seo_value( 'title' );
	if ( ! $value && 'product' === project_child_fcc_kind() ) {
		$value = get_the_title( get_queried_object_id() ) . ' | ' . project_core_fcc_site( 'name' );
	}
	return $value ?: $title;
}
add_filter( 'pre_get_document_title', 'project_child_fcc_title' );

/** Preserve WordPress pagination canonical handling and discard tracking parameters. */
function project_child_fcc_seo_url() {
	return wp_get_canonical_url( get_queried_object_id() ) ?: get_permalink( get_queried_object_id() );
}

function project_child_fcc_seo_canonical_owner() {
	if ( project_child_fcc_seo_context() && ! project_child_fcc_external_seo() ) {
		remove_action( 'wp_head', 'rel_canonical' );
	}
}
add_action( 'wp', 'project_child_fcc_seo_canonical_owner' );

function project_child_fcc_metadata() {
	if ( ! project_child_fcc_seo_context() || project_child_fcc_external_seo() ) { return; }
	$kind        = project_child_fcc_kind();
	$description = project_child_fcc_seo_value( 'description' );
	$product     = 'product' === $kind ? project_child_fcc_detail_data( false ) : array();
	if ( ! $description && $product ) { $description = wp_strip_all_tags( $product['tagline'] ); }
	$url      = project_child_fcc_seo_url();
	$title    = wp_get_document_title();
	$image_id = absint( get_post_meta( get_queried_object_id(), 'fcc_' . $kind . '_seo_image', true ) );
	if ( ! $image_id && $product ) { $image_id = absint( get_post_meta( get_queried_object_id(), 'fcc_image', true ) ); }
	$image = $image_id ? wp_get_attachment_image_src( $image_id, 'full' ) : false;
	$tags = array( 'og:type' => 'website', 'og:locale' => 'vi_VN', 'og:site_name' => project_core_fcc_site( 'name' ), 'og:title' => $title, 'og:url' => $url );
	if ( $description ) {
		echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
		$tags['og:description'] = $description;
	}
	echo '<link rel="canonical" href="' . esc_url( $url ) . '">' . "\n";
	foreach ( $tags as $property => $value ) {
		if ( '' !== $value ) { echo '<meta property="' . esc_attr( $property ) . '" content="' . esc_attr( $value ) . '">' . "\n"; }
	}
	echo '<meta name="twitter:card" content="' . ( $image ? 'summary_large_image' : 'summary' ) . '">' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
	if ( $description ) { echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '">' . "\n"; }
	if ( $image ) {
		$alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ?: get_the_title( get_queried_object_id() );
		echo '<meta property="og:image" content="' . esc_url( $image[0] ) . '"><meta property="og:image:width" content="' . absint( $image[1] ) . '"><meta property="og:image:height" content="' . absint( $image[2] ) . '"><meta property="og:image:alt" content="' . esc_attr( $alt ) . '">' . "\n";
		echo '<meta name="twitter:image" content="' . esc_url( $image[0] ) . '"><meta name="twitter:image:alt" content="' . esc_attr( $alt ) . '">' . "\n";
	}
	$graph = project_child_fcc_seo_graph( $product, $description, $url );
	if ( $graph ) {
		echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => $graph ), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
	}
}
add_action( 'wp_head', 'project_child_fcc_metadata', 2 );

/** Only describe published, visible content. Never infer reviews, stock or medical services. */
function project_child_fcc_seo_graph( $product, $description, $url ) {
	if ( 'thankyou' === project_child_fcc_kind() || 'publish' !== get_post_status( get_queried_object_id() ) ) { return array(); }
	$home = project_core_fcc_url( 'home' );
	$site = array( '@type' => 'WebSite', '@id' => $home . '#website', 'url' => $home, 'name' => project_core_fcc_site( 'name' ), 'inLanguage' => 'vi-VN' );
	$page = array( '@type' => 'WebPage', '@id' => $url . '#webpage', 'url' => $url, 'name' => wp_get_document_title(), 'inLanguage' => 'vi-VN', 'isPartOf' => array( '@id' => $site['@id'] ) );
	if ( $description ) { $page['description'] = $description; }
	$graph = array( $site );
	if ( $product ) {
		$node = array( '@type' => 'Product', '@id' => $url . '#product', 'name' => $product['name'], 'url' => $url );
		if ( $product['tagline'] ) { $node['description'] = wp_strip_all_tags( $product['tagline'] ); }
		if ( $product['image'] ) { $node['image'] = $product['image']; }
		if ( $product['brand'] ) { $node['brand'] = array( '@type' => 'Brand', 'name' => $product['brand'] ); }
		if ( $product['category'] ) { $node['category'] = $product['category']; }
		if ( null !== $product['salePrice'] && $product['salePrice'] > 0 ) {
			$node['offers'] = array( '@type' => 'Offer', 'url' => $url, 'price' => (string) $product['salePrice'], 'priceCurrency' => 'VND' );
		}
		$page['mainEntity'] = array( '@id' => $node['@id'] );
		$graph[] = $node;
		// Match the three levels already displayed in the product breadcrumb.
		$items = array(
			array( 'name' => project_child_fcc_raw_text( 'fcc_ui_db7aae96337c15', 'shared' ), 'item' => $home ),
			array( 'name' => project_child_fcc_raw_text( 'fcc_ui_e4a25aa0335570', 'shared' ), 'item' => project_core_fcc_url( 'catalog' ) ),
			array( 'name' => $product['shortName'] ?: $product['name'], 'item' => $url ),
		);
		if ( $items[0]['name'] && $items[1]['name'] ) {
			foreach ( $items as $index => &$item ) { $item = array_merge( array( '@type' => 'ListItem', 'position' => $index + 1 ), $item ); } unset( $item );
			$graph[] = array( '@type' => 'BreadcrumbList', '@id' => $url . '#breadcrumb', 'itemListElement' => $items );
			$page['breadcrumb'] = array( '@id' => $url . '#breadcrumb' );
		}
	}
	$graph[] = $page;
	return $graph;
}

/** Favicon is presentation, so it remains available with an external SEO owner. */
function project_child_fcc_favicon() {
	if ( ! project_child_fcc_kind() || ! function_exists( 'project_core_fcc_page_id' ) ) { return; }
	$favicon = project_child_fcc_image( 'fcc_site_favicon' );
	$favicon = project_child_fcc_webp_url( $favicon ) ?: $favicon;
	if ( $favicon ) { remove_action( 'wp_head', 'wp_site_icon', 99 ); echo '<link rel="icon" href="' . esc_url( $favicon ) . '">'; }
}
add_action( 'wp_head', 'project_child_fcc_favicon', 2 );

function project_child_fcc_robots( $robots ) {
	$host  = strtolower( (string) wp_parse_url( home_url(), PHP_URL_HOST ) );
	$local = 'production' !== wp_get_environment_type() || in_array( $host, array( 'localhost', '127.0.0.1', '::1', '[::1]' ), true ) || (bool) preg_match( '/\.(test|local|localhost|invalid)$/', $host );
	if ( project_child_fcc_kind() && function_exists( 'project_core_fcc_site' ) && ( $local || 'approved' !== project_core_fcc_site( 'content_status' ) || 'thankyou' === project_child_fcc_kind() ) ) {
		$robots['noindex'] = true;
		unset( $robots['index'] );
	}
	return $robots;
}
add_filter( 'wp_robots', 'project_child_fcc_robots' );

/** Reuse the WordPress sitemap; the receipt page must never be listed. */
function project_child_fcc_seo_sitemap_pages( $args, $post_type ) {
	if ( 'page' === $post_type && ! project_child_fcc_external_seo() && function_exists( 'project_core_fcc_page_id' ) ) {
		$args['post__not_in'] = array_unique( array_merge( $args['post__not_in'] ?? array(), array_filter( array( project_core_fcc_page_id( 'thankyou' ) ) ) ) );
	}
	return $args;
}
add_filter( 'wp_sitemaps_posts_query_args', 'project_child_fcc_seo_sitemap_pages', 10, 2 );
