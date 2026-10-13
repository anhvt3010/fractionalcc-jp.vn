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
	foreach ( array( '0985 561 862' => 'hotlineDisplay', '0985561862' => 'hotline', 'jpbuidangco.ltd@gmail.com' => 'email', 'Công ty TNHH BUI DANG-JP' => 'distributor' ) as $source => $field ) {
		$value = project_core_fcc_site( $field );
		if ( '' !== $value ) { $text = str_replace( $source, $value, $text ); }
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

function project_child_fcc_image( $field ) {
	$id = absint( get_post_meta( project_core_fcc_page_id( 'shared' ), $field, true ) );
	return $id ? (string) wp_get_attachment_image_url( $id, 'full' ) : '';
}

function project_child_fcc_image_alt( $image_field, $fallback_field ) {
	$id = absint( get_post_meta( project_core_fcc_page_id( 'shared' ), $image_field, true ) );
	$alt = $id ? get_post_meta( $id, '_wp_attachment_image_alt', true ) : '';
	return is_scalar( $alt ) && '' !== (string) $alt ? (string) $alt : project_child_fcc_raw_text( $fallback_field, 'shared' );
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
	$schema   = project_child_fcc_schema();
	$choices  = isset( $schema['home']['hero-section']['fcc_home_hero_background_position']['choices'] ) ? $schema['home']['hero-section']['fcc_home_hero_background_position']['choices'] : array();
	$position = get_post_meta( $id, 'fcc_home_hero_background_position', true );
	$position = is_string( $position ) && isset( $choices[ $position ] ) ? $position : 'center center';
	$style    = '--fcc-hero-background:url("' . esc_url_raw( $url ) . '");--fcc-hero-position:' . $position . ';';
	echo 'class="hero-section fcc-hero-background" style="' . esc_attr( $style ) . '"';
}

/** Render the featured product image for the home hero card. */
function project_child_fcc_hero_product_image_url() {
	$home_id = project_core_fcc_page_id( 'home' );
	if ( $home_id ) {
		$image_id = absint( get_post_meta( $home_id, 'fcc_home_hero_product_image', true ) );
		if ( $image_id ) {
			$url = wp_get_attachment_image_url( $image_id, 'full' );
			if ( $url ) {
				return (string) $url;
			}
		}
	}
	$shared_url = project_child_fcc_image( 'fcc_ui_453bd451ef0751' );
	return $shared_url ? $shared_url : project_child_fcc_asset( 'assets/images/banner/combo-c-cx4-gold.png' );
}

/** Render the featured product image alt text for the home hero card. */
function project_child_fcc_hero_product_image_alt() {
	$home_id = project_core_fcc_page_id( 'home' );
	if ( $home_id ) {
		$alt = get_post_meta( $home_id, 'fcc_home_hero_product_alt', true );
		if ( is_scalar( $alt ) && '' !== (string) $alt ) {
			return (string) $alt;
		}
		$image_id = absint( get_post_meta( $home_id, 'fcc_home_hero_product_image', true ) );
		if ( $image_id ) {
			$image_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
			if ( is_scalar( $image_alt ) && '' !== (string) $image_alt ) {
				return (string) $image_alt;
			}
		}
	}
	return project_child_fcc_image_alt( 'fcc_ui_453bd451ef0751', 'fcc_ui_eeddc60a6e4bec' );
}

function project_child_fcc_link( $source ) {
	if ( 0 === strpos( $source, 'tel:' ) ) { return 'tel:' . preg_replace( '/[^0-9+]/', '', project_core_fcc_site( 'hotline' ) ); }
	if ( 0 === strpos( $source, 'mailto:' ) ) { return 'mailto:' . sanitize_email( project_core_fcc_site( 'email' ) ); }
	if ( 0 === strpos( $source, 'https://zalo.me/' ) ) { return project_core_fcc_site( 'zaloLink' ); }
	if ( 0 === strpos( $source, 'https://m.me/' ) ) {
		$messenger = project_core_fcc_site( 'messengerLink' );
		return ! empty( $messenger ) ? $messenger : 'https://m.me/buidangjp';
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

function project_child_fcc_product_value( $name ) {
	$data = project_core_fcc_product( get_queried_object_id(), 'active_summary' === $name );
	if ( ! $data ) { return ''; }
	if ( 'price' === $name ) { return project_child_fcc_price( $data['salePrice'] ); }
	if ( 'original' === $name ) { return $data['originalPrice'] ? project_child_fcc_price( $data['originalPrice'] ) : ''; }
	if ( 'saving' === $name ) { return $data['saveAmount'] > 0 ? 'Tiết kiệm ' . project_child_fcc_price( $data['saveAmount'] ) : ''; }
	if ( 'buy' === $name ) { return $data['salePrice'] ? 'MUA NGAY – ' . project_child_fcc_price( $data['salePrice'] ) : 'MUA NGAY'; }
	if ( 'target_summary' === $name ) { return implode( ' • ', $data['targetUsers'] ); }
	if ( 'active_summary' === $name ) { return implode( ' • ', wp_list_pluck( $data['actives'], 'name' ) ); }
	if ( 'callout_title' === $name ) { return $data['spiculeCount'] ? 'CÔNG NGHỆ CHUẨN NHẬT' : 'CHUẨN TOKYO'; }
	if ( 'callout_subtitle' === $name ) { return $data['spiculeCount'] ?: 'TÁI TẠO TẦNG SÂU'; }
	return isset( $data[ $name ] ) && is_scalar( $data[ $name ] ) ? (string) $data[ $name ] : '';
}

function project_child_fcc_order_options() {
	echo '<option value="">Chọn Combo hoặc Sản phẩm</option>';
	$data = project_core_fcc_frontend_data();
	foreach ( $data['combos'] as $combo ) { echo '<option value="combo_' . esc_attr( $combo['id'] ) . '">' . esc_html( $combo['name'] . ' – ' . project_child_fcc_price( $combo['salePrice'] ) ) . '</option>'; }
	foreach ( $data['products'] as $product ) { echo '<option value="prod_' . esc_attr( $product['id'] ) . '"' . selected( get_queried_object_id(), $product['id'], false ) . '>' . esc_html( $product['shortName'] . ' – ' . project_child_fcc_price( $product['salePrice'] ) ) . '</option>'; }
}

function project_child_fcc_form_fields( $source = 'modal' ) {
	echo '<input type="hidden" name="action" value="fcc_submit_order">';
	echo '<input type="hidden" name="_fcc_nonce" value="' . esc_attr( wp_create_nonce( 'fcc_order_action' ) ) . '">';
	echo '<input type="hidden" name="fcc_form_source" value="' . esc_attr( $source ) . '">';
	echo '<input type="text" name="website_url" value="" tabindex="-1" autocomplete="off" aria-hidden="true" class="fcc-honeypot">';
}
