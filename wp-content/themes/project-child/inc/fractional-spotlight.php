<?php
/** Editable spotlight presentation for selected products and combos. @package Project_Child */
defined( 'ABSPATH' ) || exit;

function project_child_fcc_spotlight_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) { return; }
	$fields = array(
		array( 'key' => 'field_fcc_spotlight_tab_content', 'label' => 'Nội dung thẻ', 'name' => '', 'type' => 'tab', 'selected' => 1 ),
	);
	$definitions = array(
		'spotlightTitle' => array( 'Tiêu đề trên thẻ', 'text', 100, 'Chỉ dùng ở mục sản phẩm chủ lực trên trang chủ. Để trống để dùng tên đầy đủ của sản phẩm hoặc combo.' ),
		'spotlightDescription' => array( 'Mô tả trên thẻ', 'textarea', 100, 'Để trống để dùng Công dụng / mô tả ngắn của sản phẩm hoặc Nhu cầu phù hợp của combo.' ),
		'spotlightSpecs' => array( 'Thông số trong nhãn', 'text', 50, 'Để trống để dùng số CBMP và quy cách của sản phẩm, hoặc mô tả các sản phẩm trong combo.' ),
		'spotlightRating' => array( 'Dòng đánh giá / thông tin bổ sung', 'text', 50, 'Chỉ nhập thông tin đã xác nhận. Để trống để dùng điểm và số đánh giá của sản phẩm nếu có.' ),
		'spotlightHighlights' => array( 'Các điểm nổi bật', 'textarea', 100, 'Mỗi ý một dòng theo dạng Nhãn | Mô tả. Để trống để dùng các thẻ hoạt chất của sản phẩm nếu có.' ),
		'spotlightGift' => array( 'Thông tin quà tặng', 'text', 100, 'Chỉ nhập quà tặng đã xác nhận. Để trống để dùng quà tặng của combo nếu có; sản phẩm không có thông tin thay thế.' ),
		'spotlightDetailLabel' => array( 'Chữ nút xem chi tiết', 'text', 50, 'Để trống để dùng Xem Chi Tiết (sản phẩm) hoặc Xem combo (combo).' ),
		'spotlightBuyLabel' => array( 'Chữ nút mua', 'text', 50, 'Để trống để dùng MUA NGAY (sản phẩm) hoặc MUA COMBO NGAY (combo).' ),
	);
	foreach ( $definitions as $name => $definition ) {
		if ( 'spotlightHighlights' === $name ) {
			$fields[] = array( 'key' => 'field_fcc_spotlight_tab_details', 'label' => 'Điểm nổi bật & nút', 'name' => '', 'type' => 'tab' );
		}
		$fields[] = array( 'key' => 'field_fcc_' . $name, 'name' => 'fcc_' . $name, 'label' => $definition[0], 'type' => $definition[1], 'wrapper' => array( 'width' => $definition[2] ), 'rows' => 4, 'instructions' => $definition[3] );
	}
	acf_add_local_field_group( array( 'key' => 'group_fcc_spotlight', 'title' => 'Thông tin thẻ sản phẩm chủ lực', 'menu_order' => 20, 'fields' => $fields, 'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'fcc_product' ) ), array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'fcc_combo' ) ) ) ) );
}
add_action( 'acf/init', 'project_child_fcc_spotlight_fields' );

/** Optional page-specific presentation; empty values use the product data. */
function project_child_fcc_product_presentation_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) { return; }
	$fields = array();
	foreach ( array( 'seo_title' => array( 'Tiêu đề SEO riêng', 'text' ), 'seo_description' => array( 'Mô tả SEO riêng', 'textarea' ), 'seo_image' => array( 'Ảnh chia sẻ riêng', 'image' ), 'detail_callout_title' => array( 'Tiêu đề nhãn trên ảnh', 'text' ), 'detail_callout_subtitle' => array( 'Mô tả nhãn trên ảnh', 'text' ) ) as $name => $definition ) {
		$meta_name = 0 === strpos( $name, 'seo_' ) ? 'fcc_product_' . $name : 'fcc_' . $name;
		$field = array( 'key' => 'field_' . $meta_name, 'name' => $meta_name, 'label' => $definition[0], 'type' => $definition[1], 'instructions' => 'Để trống để dùng nội dung sản phẩm và nhãn dùng chung. Chỉ nhập nội dung đã xác nhận.' );
		if ( 'image' === $definition[1] ) { $field += array( 'return_format' => 'id', 'preview_size' => 'medium', 'mime_types' => 'jpg,jpeg,png,webp' ); }
		$fields[] = $field;
	}
	acf_add_local_field_group( array( 'key' => 'group_fcc_product_presentation', 'title' => 'SEO & nhãn ảnh trang sản phẩm', 'fields' => $fields, 'menu_order' => 21, 'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'fcc_product' ) ) ) ) );
}
add_action( 'acf/init', 'project_child_fcc_product_presentation_fields' );

function project_child_fcc_spotlight_data( $post, $combos ) {
	$is_combo = 'fcc_combo' === $post->post_type;
	$data = $is_combo ? ( isset( $combos[ $post->ID ] ) ? $combos[ $post->ID ] : array() ) : project_core_fcc_product( $post->ID, true );
	if ( ! $data ) { return array(); }
	$data['url'] = $is_combo ? project_core_fcc_url( 'catalog' ) : $data['url'];
	$data['order_code'] = ( $is_combo ? 'combo_' : 'prod_' ) . $post->ID;
	$image_id = absint( get_post_meta( $post->ID, 'fcc_image', true ) );
	$data['alt'] = get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ?: $data['name'];
	$fallbacks = array(
		'spotlightTitle' => $data['name'],
		'spotlightDescription' => $is_combo ? $data['idealFor'] : $data['tagline'],
		'spotlightRating' => ! $is_combo && null !== $data['rating'] ? $data['rating'] . ( null !== $data['reviewCount'] ? ' (' . $data['reviewCount'] . ' đánh giá)' : '' ) : '',
		'spotlightSpecs' => $is_combo ? implode( ' • ', $data['productsIncluded'] ) : implode( ' • ', array_filter( array( $data['cbmp'] ? 'Số CBMP: ' . $data['cbmp'] : '', $data['volume'] ? 'Dung tích: ' . $data['volume'] : '' ) ) ),
		'spotlightHighlights' => $is_combo ? '' : implode( "\n", array_map( function ( $active ) { return $active['name'] . ' | ' . $active['desc']; }, $data['actives'] ) ),
		'spotlightGift' => $is_combo ? $data['gift'] : '',
		'spotlightDetailLabel' => $is_combo ? 'Xem combo' : 'Xem Chi Tiết',
		'spotlightBuyLabel' => $is_combo ? 'MUA COMBO NGAY' : 'MUA NGAY',
	);
	foreach ( $fallbacks as $name => $fallback ) {
		$value = get_post_meta( $post->ID, 'fcc_' . $name, true );
		$data[ $name ] = is_string( $value ) && '' !== $value ? $value : $fallback;
	}
	return $data;
}

/** Plain text with optional label separators; all stored content remains escaped. */
function project_child_fcc_spotlight_line( $text ) {
	$parts = explode( '|', $text );
	foreach ( $parts as $index => $part ) {
		if ( count( $parts ) > 1 && 0 === $index % 2 ) {
			echo '<strong class="duo-line-label">' . esc_html( trim( $part ) ) . '</strong> ';
		} else {
			echo '<span class="duo-line-desc">' . esc_html( trim( $part ) ) . '</span> ';
		}
	}
}
