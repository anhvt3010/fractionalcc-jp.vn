<?php
/**
 * Fractional CC content model and read API. Presentation stays in the theme.
 *
 * @package Project_Core
 */

defined( 'ABSPATH' ) || exit;

function project_core_fcc_product_category_choices() {
	return array( 'peel' => 'Tẩy tế bào chết', 'serum' => 'Serum', 'mask' => 'Mặt nạ', 'lip' => 'Dưỡng môi' );
}

function project_core_fcc_product_card_tag_choices() {
	return array( 'sale' => 'Sale', 'new' => 'New', 'best' => 'Best', 'sold_out' => 'Sold Out' );
}

/** Only supported tag keys, in a consistent order, may reach product cards. */
function project_core_fcc_normalize_card_tags( $value ) {
	if ( ! is_array( $value ) ) { return array(); }
	return array_values( array_intersect( array_keys( project_core_fcc_product_card_tag_choices() ), array_filter( $value, 'is_string' ) ) );
}

function project_core_fcc_register_content() {
	$types = array(
		'fcc_product'    => array( 'Sản phẩm', 'Sản phẩm', true ),
		'fcc_combo'      => array( 'Combo', 'Combo', false ),
		'fcc_ingredient' => array( 'Hoạt chất', 'Hoạt chất', false ),
		'fcc_site'       => array( 'Thông tin chung', 'Thông tin chung', false ),
	);
	foreach ( $types as $type => $definition ) {
		register_post_type(
			$type,
			array(
				'labels' => array( 'name' => $definition[0], 'singular_name' => $definition[1], 'add_new_item' => 'Thêm ' . mb_strtolower( $definition[1] ), 'edit_item' => 'Sửa ' . mb_strtolower( $definition[1] ) ),
				'public' => $definition[2], 'show_ui' => true, 'show_in_rest' => 'fcc_product' === $type,
				'show_in_menu' => 'project-website-content', 'has_archive' => false,
				'rewrite' => $definition[2] ? array( 'slug' => 'san-pham', 'with_front' => false ) : false,
				// Product copy uses ACF; the unified media box edits the existing gallery blocks.
				'supports' => array( 'title', 'page-attributes', 'revisions' ),
				'capability_type' => 'page', 'map_meta_cap' => true,
			)
		);
	}
}
add_action( 'init', 'project_core_fcc_register_content' );

function project_core_fcc_page_id( $kind ) {
	$ids = get_option( 'project_core_fcc_pages', array() );
	return isset( $ids[ $kind ] ) ? absint( $ids[ $kind ] ) : 0;
}

function project_core_fcc_site( $name, $fallback = '' ) {
	$id = project_core_fcc_page_id( 'shared' );
	if ( ! $id || ! metadata_exists( 'post', $id, 'fcc_' . $name ) ) {
		return $fallback;
	}
	$value = get_post_meta( $id, 'fcc_' . $name, true );
	return is_scalar( $value ) ? (string) $value : $fallback;
}

function project_core_fcc_posts( $type ) {
	return get_posts( array( 'post_type' => $type, 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => array( 'menu_order' => 'ASC', 'ID' => 'ASC' ), 'no_found_rows' => true ) );
}

function project_core_fcc_lines( $value ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $value ) ) ) );
}

/** Gallery blocks remain the single ordered source for product images. */
function project_core_fcc_gallery_ids( $post_id ) {
	$ids = array();
	foreach ( parse_blocks( (string) get_post_field( 'post_content', $post_id ) ) as $block ) {
		if ( 'core/gallery' !== $block['blockName'] ) { continue; }
		foreach ( $block['innerBlocks'] as $image ) {
			$id = absint( $image['attrs']['id'] ?? 0 );
			if ( $id && wp_attachment_is_image( $id ) ) { $ids[] = $id; }
		}
	}
	return array_values( array_unique( $ids ) );
}

/** Shared by the initializer and the native media editor; other blocks survive. */
function project_core_fcc_gallery_content( $content, $ids ) {
	$images = '';
	foreach ( $ids as $id ) {
		$images .= '<!-- wp:image {"id":' . absint( $id ) . ',"sizeSlug":"full","linkDestination":"none"} --><figure class="wp-block-image size-full"><img src="' . esc_url( wp_get_attachment_url( $id ) ) . '" alt="' . esc_attr( get_post_meta( $id, '_wp_attachment_image_alt', true ) ) . '" class="wp-image-' . absint( $id ) . '"/></figure><!-- /wp:image -->';
	}
	$gallery = $images ? '<!-- wp:gallery {"linkTo":"none"} --><figure class="wp-block-gallery has-nested-images columns-default is-cropped">' . $images . '</figure><!-- /wp:gallery -->' : '';
	$result = ''; $inserted = false;
	foreach ( parse_blocks( $content ) as $block ) {
		if ( 'core/gallery' === $block['blockName'] ) {
			if ( ! $inserted ) { $result .= $gallery; $inserted = true; }
		} else { $result .= serialize_block( $block ); }
	}
	return $result . ( $inserted ? '' : $gallery );
}

function project_core_fcc_product( $post_id, $with_details = true ) {
	$post = get_post( $post_id );
	if ( ! $post || 'fcc_product' !== $post->post_type || 'publish' !== $post->post_status ) {
		return array();
	}
	$data = array( 'id' => $post->ID, 'name' => get_the_title( $post ), 'slug' => $post->post_name, 'url' => get_permalink( $post ) );
	$fields = array( 'brand', 'ingredientsText', 'shortName', 'category', 'categoryType', 'tagline', 'spiculeCount', 'volume', 'cbmp', 'legalStatus', 'notes', 'origin' );
	foreach ( $fields as $field ) {
		$data[ $field ] = (string) get_post_meta( $post->ID, 'fcc_' . $field, true );
	}
	foreach ( array( 'salePrice', 'originalPrice', 'rating', 'reviewCount' ) as $field ) {
		$value = get_post_meta( $post->ID, 'fcc_' . $field, true );
		$data[ $field ] = '' === $value ? null : (float) $value;
	}
	$data['saveAmount'] = null !== $data['salePrice'] && null !== $data['originalPrice'] ? max( 0, $data['originalPrice'] - $data['salePrice'] ) : 0;
	foreach ( array( 'uspBullets', 'targetUsers' ) as $field ) {
		$data[ $field ] = project_core_fcc_lines( get_post_meta( $post->ID, 'fcc_' . $field, true ) );
	}
	$data['skinConcerns'] = (array) get_post_meta( $post->ID, 'fcc_skinConcerns', true );
	$data['cardTags'] = project_core_fcc_normalize_card_tags( get_post_meta( $post->ID, 'fcc_cardTags', true ) );
	$image_id = absint( get_post_meta( $post->ID, 'fcc_image', true ) );
	$data['image'] = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';
	$data['imageAlt'] = $image_id ? (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true ) : '';
	if ( '' === $data['imageAlt'] ) { $data['imageAlt'] = $data['name']; }
	$hover_image_id = absint( get_post_meta( $post->ID, 'fcc_hoverImage', true ) );
	$data['hoverImage'] = $hover_image_id ? wp_get_attachment_image_url( $hover_image_id, 'full' ) : '';
	$data['hoverImageAlt'] = $hover_image_id ? ( (string) get_post_meta( $hover_image_id, '_wp_attachment_image_alt', true ) ?: $data['name'] ) : '';
	if ( ! $with_details ) { return $data; }
	$data['gallery'] = array();
	$data['galleryAlts'] = array();
	$data['galleryThumbs'] = array();
	foreach ( project_core_fcc_gallery_ids( $post_id ) as $id ) {
		$url = wp_get_attachment_image_url( $id, 'full' );
		if ( $url ) {
			$data['gallery'][] = $url;
			$alt = (string) get_post_meta( $id, '_wp_attachment_image_alt', true );
			$data['galleryAlts'][ $url ] = $alt ?: $data['name'];
			$data['galleryThumbs'][ $url ] = wp_get_attachment_image_url( $id, 'thumbnail' ) ?: $url;
		}
	}
	if ( $data['image'] && ! in_array( $data['image'], $data['gallery'], true ) ) { array_unshift( $data['gallery'], $data['image'] ); }
	if ( $data['image'] ) { $data['galleryAlts'][ $data['image'] ] = $data['imageAlt']; $data['galleryThumbs'][ $data['image'] ] = wp_get_attachment_image_url( $image_id, 'thumbnail' ) ?: $data['image']; }
	$data['gallery'] = array_values( array_unique( $data['gallery'] ) );
	$data['actives'] = array();
	$ingredient_ids = array_filter( array_map( 'absint', (array) get_post_meta( $post->ID, 'fcc_actives', true ) ) );
	$ingredients = $ingredient_ids ? get_posts( array( 'post_type' => 'fcc_ingredient', 'post_status' => 'publish', 'post__in' => $ingredient_ids, 'posts_per_page' => count( $ingredient_ids ), 'orderby' => 'post__in', 'no_found_rows' => true ) ) : array();
	foreach ( $ingredients as $ingredient ) {
		if ( $ingredient ) {
			$data['actives'][] = array( 'name' => get_the_title( $ingredient ), 'tag' => (string) get_post_meta( $ingredient->ID, 'fcc_tag', true ), 'desc' => (string) get_post_meta( $ingredient->ID, 'fcc_description', true ) );
		}
	}
	return $data;
}

/** Resolve the selected published products in their configured order. */
function project_core_fcc_combo_contents( $combo ) {
	$ids = array_values( array_unique( array_filter( array_map( 'absint', (array) ( $combo['productIds'] ?? array() ) ) ) ) );
	$result = array( 'products' => array() );
	if ( ! $ids ) { return $result; }
	$posts = get_posts( array( 'post_type' => 'fcc_product', 'post_status' => 'publish', 'post__in' => $ids, 'posts_per_page' => count( $ids ), 'orderby' => 'post__in', 'no_found_rows' => true ) );
	foreach ( $posts as $post ) {
		$result['products'][] = project_core_fcc_product( $post->ID, false );
	}
	return $result;
}

function project_core_fcc_combos() {
	$data = array();
	foreach ( project_core_fcc_posts( 'fcc_combo' ) as $combo ) {
		$item = array( 'id' => $combo->ID, 'name' => get_the_title( $combo ), 'productIds' => array_map( 'absint', (array) get_post_meta( $combo->ID, 'fcc_productIds', true ) ) );
		foreach ( array( 'code', 'shortName', 'badge', 'idealFor', 'routine', 'gift' ) as $name ) { $item[ $name ] = (string) get_post_meta( $combo->ID, 'fcc_' . $name, true ); }
		foreach ( array( 'originalPrice', 'salePrice', 'rating', 'reviewCount' ) as $name ) { $value = get_post_meta( $combo->ID, 'fcc_' . $name, true ); $item[ $name ] = '' === $value ? null : (float) $value; }
		$item['saveAmount'] = max( 0, (float) $item['originalPrice'] - (float) $item['salePrice'] );
		$item['productsIncluded'] = project_core_fcc_lines( get_post_meta( $combo->ID, 'fcc_productsIncluded', true ) );
		$item['image'] = wp_get_attachment_image_url( absint( get_post_meta( $combo->ID, 'fcc_image', true ) ), 'full' );
		$hover_image_id = absint( get_post_meta( $combo->ID, 'fcc_hoverImage', true ) );
		$item['hoverImage'] = $hover_image_id ? wp_get_attachment_image_url( $hover_image_id, 'full' ) : '';
		$item['hoverImageAlt'] = $hover_image_id ? ( (string) get_post_meta( $hover_image_id, '_wp_attachment_image_alt', true ) ?: $item['name'] ) : '';
		$item['isBestSeller'] = (bool) get_post_meta( $combo->ID, 'fcc_isBestSeller', true );
		$item['cardTags'] = project_core_fcc_normalize_card_tags( get_post_meta( $combo->ID, 'fcc_cardTags', true ) );
		$data[] = $item;
	}
	return $data;
}

function project_core_fcc_frontend_data( $with_products = true ) {
	$data = array( 'brand' => array( 'zaloLink' => project_core_fcc_contact( 'zaloLink' ), 'email' => project_core_fcc_contact( 'email' ), 'hotlineDisplay' => project_core_fcc_contact( 'hotlineDisplay' ) ), 'products' => array(), 'combos' => array() );
	if ( ! $with_products ) { return $data; }
	foreach ( array( 'products' => 'fcc_product', 'combos' => 'fcc_combo' ) as $list => $type ) {
		foreach ( project_core_fcc_posts( $type ) as $post ) {
			$price = get_post_meta( $post->ID, 'fcc_salePrice', true );
			$data[ $list ][] = array( 'id' => $post->ID, 'name' => get_the_title( $post ), 'shortName' => (string) get_post_meta( $post->ID, 'fcc_shortName', true ), 'salePrice' => '' === $price ? null : (float) $price );
		}
	}
	return $data;
}

function project_core_fcc_legacy_product_id( $legacy_id ) {
	$posts = get_posts( array( 'post_type' => 'fcc_product', 'post_status' => 'publish', 'posts_per_page' => 1, 'fields' => 'ids', 'meta_key' => '_fcc_source_id', 'meta_value' => absint( $legacy_id ) ) );
	return $posts ? (int) $posts[0] : 0;
}

function project_core_fcc_url( $kind, $fragment = '' ) {
	$id = project_core_fcc_page_id( $kind );
	$url = $id ? get_permalink( $id ) : home_url( '/' );
	return $url . $fragment;
}
