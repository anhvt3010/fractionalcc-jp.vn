<?php
/** Context-aware, escaped access to editable presentation values. @package Project_Child */
defined( 'ABSPATH' ) || exit;

function project_child_fcc_text( $key, $scope, $editable = true ) {
	$text = esc_html( project_child_fcc_raw_text( $key, $scope ) );
	return function_exists( 'project_child_fcc_customizer_markup' ) ? project_child_fcc_customizer_markup( $text, $key, $scope, $editable ) : $text;
}

function project_child_fcc_raw_text( $key, $scope ) {
	$id = project_core_fcc_page_id( $scope );
	$text = get_post_meta( $id, $key, true );
	if ( ! is_scalar( $text ) ) { return ''; }
	$text = (string) $text;
	// Keep contact information consistent without making customers edit every label.
	$distributor_replacements = array(
		'CÔNG TY TNHH JP- BÙI ĐẶNG' => 'distributor',
		'Công ty TNHH BUI DANG-JP'  => 'distributor',
		'Công ty TNHH BUI DANG–JP'  => 'distributor',
		'CÔNG TY TNHH BUI DANG-JP'  => 'distributor',
		'CÔNG TY TNHH BUI DANG–JP'  => 'distributor',
	);
	foreach ( $distributor_replacements as $source => $field ) {
		$value = project_core_fcc_site( $field );
		if ( '' !== $value ) { $text = str_replace( $source, $value, $text ); }
	}
	// Only designated contact labels follow the central fields; customer phone examples do not.
	static $contacts;
	if ( null === $contacts ) {
		$contacts = array();
		foreach ( project_child_fcc_schema() as $kind => $sections ) {
			foreach ( $sections as $fields ) {
				foreach ( $fields as $field ) {
					if ( ! empty( $field['fcc_contact'] ) ) { $contacts[ $kind ][ $field['name'] ] = $field; }
				}
			}
		}
	}
	if ( isset( $contacts[ $scope ][ $key ] ) ) {
		$field = $contacts[ $scope ][ $key ];
		$value = project_core_fcc_contact( $field['fcc_contact'] );
		if ( ! empty( $field['fcc_contact_only'] ) ) { return $value; }
		if ( '' === $value && 'text' === $field['type'] ) { return ''; }
		$pattern = 'email' === $field['fcc_contact'] ? '/[a-zA-Z0-9.!#$%&\x27*+\/=?^_`{|}~-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/' : '/(?<!\d)\+?\d(?:[ .()-]*\d){8,14}(?!\d)/';
		$text = preg_replace_callback( $pattern, static function () use ( $value ) { return $value; }, $text );
	}
	return $text;
}

function project_child_fcc_reference_text( $text, $reference = '' ) {
	if ( preg_match( '/^prod_(\d+)$/', $reference, $match ) ) {
		$product = project_core_fcc_product( project_core_fcc_legacy_product_id( $match[1] ), false );
		if ( $product ) {
			return preg_replace_callback( '/(?:593\.100|682\.500|506\.800|379\.776)(\s*(?:đ|Đ|₫|VNĐ))?/u', function ( $price ) use ( $product ) { return null === $product['salePrice'] ? 'Liên hệ tư vấn' : number_format( $product['salePrice'], 0, ',', '.' ) . ( isset( $price[1] ) ? $price[1] : '' ); }, $text );
		}
	}
	$prices = array( '593.100' => 6, '682.500' => 7, '506.800' => 8, '379.776' => 9 );
	foreach ( $prices as $source => $legacy_id ) {
		if ( false !== strpos( $text, $source ) ) {
			$product = project_core_fcc_product( project_core_fcc_legacy_product_id( $legacy_id ), false );
			if ( $product ) { return str_replace( $source, null === $product['salePrice'] ? 'Liên hệ' : number_format( $product['salePrice'], 0, ',', '.' ), $text ); }
		}
	}
	foreach ( project_core_fcc_combos() as $combo ) {
		$text = str_replace( array( '1.237.500', '1.275.600' ), array( number_format( (float) $combo['salePrice'], 0, ',', '.' ), number_format( (float) $combo['originalPrice'], 0, ',', '.' ) ), $text );
		break;
	}
	return $text;
}

function project_child_fcc_asset( $path ) {
	if ( ! preg_match( '#^assets/images/[a-zA-Z0-9/_-]+\.(png|jpe?g|webp)$#', $path ) ) { return ''; }
	return get_stylesheet_directory_uri() . '/assets/fractional/' . $path;
}

function project_child_fcc_image( $field, $scope = 'shared' ) {
	$id = absint( get_post_meta( project_core_fcc_page_id( $scope ), $field, true ) );
	return $id ? (string) wp_get_attachment_image_url( $id, 'full' ) : '';
}

function project_child_fcc_image_alt( $image_field, $fallback_field ) {
	$id = absint( get_post_meta( project_core_fcc_page_id( 'shared' ), $image_field, true ) );
	$alt = $id ? get_post_meta( $id, '_wp_attachment_image_alt', true ) : '';
	return is_scalar( $alt ) && '' !== (string) $alt ? (string) $alt : project_child_fcc_raw_text( $fallback_field, 'shared' );
}

/** Resolve exact source bytes to portable derivatives, including migrated Media uploads. */
function project_child_fcc_image_variant( $url ) {
	static $cache = array();
	static $manifest;
	if ( ! is_string( $url ) || ! $url ) { return array(); }
	if ( isset( $cache[ $url ] ) ) { return $cache[ $url ]; }
	$cache[ $url ] = array();
	$uploads = wp_get_upload_dir();
	$locations = array( get_stylesheet_directory_uri() => get_stylesheet_directory(), $uploads['baseurl'] => $uploads['basedir'] );
	foreach ( $locations as $base_url => $directory ) {
		if ( 0 !== strpos( $url, $base_url . '/' ) ) { continue; }
		$relative = rawurldecode( (string) wp_parse_url( substr( $url, strlen( $base_url ) ), PHP_URL_PATH ) );
		$file = realpath( $directory . $relative );
		$base = realpath( $directory );
		if ( ! $file || ! $base || 0 !== strpos( wp_normalize_path( $file ), trailingslashit( wp_normalize_path( $base ) ) ) ) { return array(); }
		if ( null === $manifest ) {
			$path = get_stylesheet_directory() . '/assets/fractional/image-variants.json';
			$manifest = is_readable( $path ) ? json_decode( file_get_contents( $path ), true ) : array();
		}
		// Match bytes, not filenames: a customer's replacement image must never use an old default.
		$digest = hash_file( 'sha256', $file );
		if ( isset( $manifest[ $digest ] ) ) {
			$variant = $manifest[ $digest ];
			$root = get_stylesheet_directory() . '/assets/fractional/';
			if ( is_file( $root . $variant['webp'] ) ) {
				$srcset = array();
				foreach ( $variant['srcset'] as $width => $path ) {
					if ( is_file( $root . $path ) ) { $srcset[] = add_query_arg( 'ver', $variant['revision'], get_stylesheet_directory_uri() . '/assets/fractional/' . $path ) . ' ' . absint( $width ) . 'w'; }
				}
				$cache[ $url ] = array( 'url' => add_query_arg( 'ver', $variant['revision'], get_stylesheet_directory_uri() . '/assets/fractional/' . $variant['webp'] ), 'srcset' => implode( ', ', $srcset ), 'width' => $variant['width'], 'height' => $variant['height'] );
			}
		} elseif ( preg_match( '/\.webp$/i', $file ) ) {
			$dimensions = getimagesize( $file );
			if ( $dimensions ) { $cache[ $url ] = array( 'url' => $url, 'srcset' => '', 'width' => $dimensions[0], 'height' => $dimensions[1] ); }
		} elseif ( preg_match( '/\.(png|jpe?g)$/i', $file ) ) {
			$webp_file = preg_replace( '/\.(png|jpe?g)$/i', '.webp', $file );
			if ( is_file( $webp_file ) && filemtime( $webp_file ) >= filemtime( $file ) ) {
				$cache[ $url ] = array( 'url' => $base_url . preg_replace( '/\.(png|jpe?g)$/i', '.webp', $relative ), 'srcset' => '' );
			}
		}
		break;
	}
	return $cache[ $url ];
}

function project_child_fcc_webp_url( $url ) {
	$variant = project_child_fcc_image_variant( $url );
	return $variant['url'] ?? '';
}

/** Render a progressive picture element with WebP source and fallback img. */
function project_child_fcc_picture_html( $url, $alt = '', $class = '', $attrs = array() ) {
	if ( ! $url ) { return ''; }
	$variant = project_child_fcc_image_variant( $url );
	$webp = $variant['url'] ?? '';
	$attrs = wp_parse_args( $attrs, array( 'decoding' => 'async' ) );
	if ( ! isset( $attrs['width'], $attrs['height'] ) && isset( $variant['width'], $variant['height'] ) ) {
		$attrs += array( 'width' => $variant['width'], 'height' => $variant['height'] );
	}
	$attr_str = '';
	foreach ( $attrs as $k => $v ) {
		if ( true === $v ) {
			$attr_str .= ' ' . esc_attr( $k );
		} elseif ( false !== $v && null !== $v && '' !== $v ) {
			$attr_str .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
		}
	}
	$class_attr = $class ? ' class="' . esc_attr( $class ) . '"' : '';
	$alt_attr = ' alt="' . esc_attr( $alt ) . '"';
	$img_html = '<img src="' . esc_url( $url ) . '"' . $alt_attr . $class_attr . $attr_str . '>';
	if ( $webp && $webp !== $url ) {
		$srcset = $variant['srcset'] ?: $webp;
		$sizes = $attrs['sizes'] ?? ( ! empty( $attrs['width'] ) ? '(max-width: ' . absint( $attrs['width'] ) . 'px) 100vw, ' . absint( $attrs['width'] ) . 'px' : '100vw' );
		return '<picture><source srcset="' . esc_attr( $srcset ) . '" sizes="' . esc_attr( $sizes ) . '" type="image/webp">' . $img_html . '</picture>';
	}
	return $img_html;
}

/** Render the editable decorative background without changing hero layout/content. */
function project_child_fcc_hero_background_attributes() {
	$id = project_core_fcc_page_id( 'home' );
	if ( ! $id ) {
		echo 'class="hero-section"';
		return;
	}
	$has_meta = metadata_exists( 'post', $id, 'fcc_home_hero_background' );
	$image_id = absint( get_post_meta( $id, 'fcc_home_hero_background', true ) );
	$url      = $image_id ? (string) wp_get_attachment_image_url( $image_id, 'full' ) : ( $has_meta ? '' : project_child_fcc_asset( 'assets/images/banner/hero-background.jpg' ) );
	if ( ! $url ) {
		echo 'class="hero-section"';
		return;
	}
	$url = project_child_fcc_webp_url( $url ) ?: $url;
	$schema   = project_child_fcc_schema();
	$choices  = isset( $schema['home']['hero-section']['fcc_home_hero_background_position']['choices'] ) ? $schema['home']['hero-section']['fcc_home_hero_background_position']['choices'] : array();
	$position = get_post_meta( $id, 'fcc_home_hero_background_position', true );
	$position = is_string( $position ) && isset( $choices[ $position ] ) ? $position : 'center center';
	$style    = '--fcc-hero-background:url("' . esc_url_raw( $url ) . '");--fcc-hero-position:' . $position . ';';
	echo 'class="hero-section fcc-hero-background" style="' . esc_attr( $style ) . '"';
}

/** Keep preload and picture candidate selection identical to avoid duplicate downloads. */
function project_child_fcc_hero_image_sizes() {
	return '(min-width: 1440px) 580px, (min-width: 992px) 42vw, (min-width: 640px) 530px, calc(100vw - 40px)';
}

/** Render the featured product image for the home hero card. */
function project_child_fcc_hero_product_image_url() {
	$combo = project_core_fcc_featured_combo();
	return $combo ? (string) $combo['image'] : '';
}

/** Render the featured product image alt text for the home hero card. */
function project_child_fcc_hero_product_image_alt() {
	$combo = project_core_fcc_featured_combo();
	if ( ! $combo ) { return ''; }
	$image_id = absint( get_post_meta( $combo['id'], 'fcc_image', true ) );
	return (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ?: $combo['name'];
}

function project_child_fcc_featured_combo_order_code() {
	$combo = project_core_fcc_featured_combo();
	return $combo ? 'combo_' . $combo['id'] : '';
}

/** Build a link from the saved channel, without reviving removed contacts. */
function project_child_fcc_contact_link( $channel ) {
	$fields = array( 'hotline' => 'hotline', 'phone' => 'phone', 'email' => 'email', 'zalo' => 'zaloLink', 'facebook' => 'messengerLink' );
	if ( ! isset( $fields[ $channel ] ) ) { return ''; }
	$value = project_core_fcc_contact( $fields[ $channel ] );
	if ( '' === $value ) { return ''; }
	if ( in_array( $channel, array( 'hotline', 'phone' ), true ) ) { return 'tel:' . $value; }
	return 'email' === $channel ? 'mailto:' . $value : $value;
}

/** Empty channels have neither a visible link nor an empty href. */
function project_child_fcc_contact_attributes( $channel ) {
	$link = project_child_fcc_contact_link( $channel );
	echo $link ? 'href="' . esc_url( $link ) . '"' : 'hidden';
}

function project_child_fcc_link( $source ) {
	if ( 0 === strpos( $source, 'tel:' ) ) {
		return project_child_fcc_contact_link( 'hotline' );
	}
	if ( 0 === strpos( $source, 'mailto:' ) ) {
		return project_child_fcc_contact_link( 'email' );
	}
	if ( 0 === strpos( $source, 'https://zalo.me/' ) ) {
		return project_child_fcc_contact_link( 'zalo' );
	}
	if ( preg_match( '#^https?://(?:m\.me|(?:www\.|web\.|m\.)?facebook\.com)/#i', $source ) ) {
		return project_child_fcc_contact_link( 'facebook' );
	}
	$parts = wp_parse_url( $source );
	$fragment = isset( $parts['fragment'] ) ? '#' . $parts['fragment'] : '';
	$map = array( 'index.html' => 'home', 'san-pham.html' => 'catalog', 'chinh-sach.html' => 'policies', 'cam-on.html' => 'thankyou', 'van-de-lan-da.html' => 'concerns' );
	$path = isset( $parts['path'] ) ? $parts['path'] : '';
	if ( isset( $map[ $path ] ) ) { return project_core_fcc_url( $map[ $path ], $fragment ); }
	if ( 'chi-tiet-san-pham.html' === $path ) {
		parse_str( isset( $parts['query'] ) ? $parts['query'] : '', $query );
		$id = isset( $query['id'] ) ? project_core_fcc_legacy_product_id( $query['id'] ) : 0;
		return $id ? get_permalink( $id ) : project_core_fcc_url( 'catalog' );
	}
	return $source;
}

function project_child_fcc_order_code( $source ) {
	if ( 0 === strpos( $source, 'prod_' ) ) {
		$id = project_core_fcc_legacy_product_id( substr( $source, 5 ) );
		return $id ? 'prod_' . $id : '';
	}
	if ( 0 === strpos( $source, 'combo_' ) ) {
		$code = substr( $source, 6 );
		foreach ( project_core_fcc_combos() as $combo ) { if ( $combo['code'] === $code ) { return 'combo_' . $combo['id']; } }
	}
	return $source;
}

function project_child_fcc_price( $amount ) {
	return null === $amount || '' === $amount ? 'Liên hệ tư vấn' : number_format( (float) $amount, 0, ',', '.' ) . ' đ';
}

/** Product/combo scores have one data source, with editable wording. */
function project_child_fcc_rating_text( $data, $detail = false ) {
	if ( ! isset( $data['rating'] ) ) { return ''; }
	$score = (string) $data['rating'];
	if ( false === strpos( $score, '.' ) ) { $score .= '.0'; }
	$text = $score . ( $detail ? ' / 5.0' : '' );
	if ( isset( $data['reviewCount'] ) ) {
		$label = project_child_fcc_raw_text( $detail ? 'fcc_ui_rating_detail_reviews' : 'fcc_ui_rating_reviews', 'shared' );
		$text .= ' (' . number_format( (int) $data['reviewCount'], 0, ',', '.' ) . '+ ' . $label . ')';
	}
	return $text;
}

/** Adapt combo content to the shared detail layout without inventing product facts. */
function project_child_fcc_detail_data( $with_details = true ) {
	$id = get_queried_object_id();
	if ( 'fcc_combo' !== get_post_type( $id ) ) { return project_core_fcc_product( $id, $with_details ); }
	$data = project_core_fcc_combo( $id, $with_details );
	if ( ! $data ) { return array(); }
	$data['tagline'] = (string) get_post_meta( $id, 'fcc_spotlightDescription', true ) ?: $data['idealFor'];
	$data['category'] = 'Combo';
	$data['brand'] = '';
	$data['origin'] = $data['code'];
	$data['targetUsers'] = project_core_fcc_lines( $data['idealFor'] );
	$data['uspBullets'] = array_values( array_filter( array( $data['routine'], $data['gift'] ? 'Quà tặng: ' . $data['gift'] : '' ) ) );
	$data['actives'] = array();
	if ( $with_details ) {
		foreach ( project_core_fcc_combo_contents( $data )['products'] as $product ) {
			$data['actives'][] = array( 'name' => $product['name'] );
		}
	}
	return $data;
}

function project_child_fcc_detail_order_code() {
	return ( 'fcc_combo' === get_post_type( get_queried_object_id() ) ? 'combo_' : 'prod_' ) . get_queried_object_id();
}

function project_child_fcc_product_value( $name ) {
	$data = project_child_fcc_detail_data( 'active_summary' === $name );
	if ( ! $data ) { return ''; }
	if ( 'price' === $name ) { return project_child_fcc_price( $data['salePrice'] ); }
	if ( 'original' === $name ) { return $data['originalPrice'] ? project_child_fcc_price( $data['originalPrice'] ) : ''; }
	if ( 'saving' === $name ) { return $data['saveAmount'] > 0 ? project_child_fcc_raw_text( 'fcc_ui_saving_prefix', 'shared' ) . ' ' . project_child_fcc_price( $data['saveAmount'] ) : ''; }
	if ( 'buy' === $name ) { $label = project_child_fcc_raw_text( 'fcc_ui_buy_prefix', 'shared' ); return $data['salePrice'] ? $label . ' – ' . project_child_fcc_price( $data['salePrice'] ) : $label; }
	if ( 'target_summary' === $name ) { return implode( ' • ', $data['targetUsers'] ); }
	if ( 'active_summary' === $name ) { return implode( ' • ', wp_list_pluck( $data['actives'], 'name' ) ); }
	if ( 'callout_title' === $name ) { return get_post_meta( $data['id'], 'fcc_detail_callout_title', true ) ?: ( 'fcc_combo' === get_post_type( $data['id'] ) ? $data['badge'] : ( $data['spiculeCount'] ? project_child_fcc_raw_text( 'fcc_ui_callout_technology', 'shared' ) : project_child_fcc_raw_text( 'fcc_ui_callout_default_title', 'shared' ) ) ); }
	if ( 'callout_subtitle' === $name ) { return get_post_meta( $data['id'], 'fcc_detail_callout_subtitle', true ) ?: ( 'fcc_combo' === get_post_type( $data['id'] ) ? $data['shortName'] : ( $data['spiculeCount'] ?: project_child_fcc_raw_text( 'fcc_ui_callout_default_subtitle', 'shared' ) ) ); }
	return isset( $data[ $name ] ) && is_scalar( $data[ $name ] ) ? (string) $data[ $name ] : '';
}

function project_child_fcc_order_options() {
	echo '<option value="">' . esc_html( project_child_fcc_raw_text( 'fcc_ui_choose_item', 'shared' ) ) . '</option>';
	$data = project_core_fcc_frontend_data();
	foreach ( $data['combos'] as $combo ) { echo '<option value="combo_' . esc_attr( $combo['id'] ) . '"' . selected( get_queried_object_id(), $combo['id'], false ) . '>' . esc_html( $combo['name'] . ' – ' . project_child_fcc_price( $combo['salePrice'] ) ) . '</option>'; }
	foreach ( $data['products'] as $product ) { echo '<option value="prod_' . esc_attr( $product['id'] ) . '"' . selected( get_queried_object_id(), $product['id'], false ) . '>' . esc_html( $product['shortName'] . ' – ' . project_child_fcc_price( $product['salePrice'] ) ) . '</option>'; }
}

function project_child_fcc_form_fields( $source = 'modal' ) {
	echo '<input type="hidden" name="action" value="fcc_submit_order">';
	echo '<input type="hidden" name="_fcc_nonce" value="' . esc_attr( wp_create_nonce( 'fcc_order_action' ) ) . '">';
	echo '<input type="hidden" name="fcc_form_source" value="' . esc_attr( $source ) . '">';
	echo '<input type="text" name="website_url" value="" tabindex="-1" autocomplete="off" aria-hidden="true" class="fcc-honeypot">';
}
