<?php
/** Editable product selections by skin concern; approved defaults use portable source keys. @package Project_Core */
defined( 'ABSPATH' ) || exit;

function project_core_fcc_regimens() {
	return array(
		'dom-nau'      => array( 'name' => 'regimen_dom_nau', 'label' => 'Đốm nâu & không đều màu' ),
		'lo-chan-long' => array( 'name' => 'regimen_lo_chan_long', 'label' => 'Lỗ chân lông to & da sần' ),
		'lao-hoa'      => array( 'name' => 'regimen_lao_hoa', 'label' => 'Lão hóa & nếp nhăn' ),
		'tham-mun'     => array( 'name' => 'regimen_tham_mun', 'label' => 'Thâm mụn sau viêm' ),
	);
}

/** Filter even imported/tampered metadata; retain the editor's selected order. */
function project_core_fcc_regimen_ids( $value ) {
	$ids = array();
	foreach ( is_array( $value ) ? $value : array() as $candidate ) {
		if ( ! is_scalar( $candidate ) || ! ctype_digit( (string) $candidate ) ) { continue; }
		$id = (int) $candidate;
		$post = $id ? get_post( $id ) : null;
		if ( $post && 'publish' === $post->post_status && in_array( $post->post_type, array( 'fcc_product', 'fcc_combo' ), true ) ) {
			$ids[] = $id;
		}
	}
	return array_values( array_unique( $ids ) );
}

function project_core_fcc_regimen_posts( $concern, $page_id ) {
	$regimens = project_core_fcc_regimens();
	if ( ! isset( $regimens[ $concern ] ) || 'page' !== get_post_type( $page_id ) || 'templates/fractional-concerns.php' !== get_page_template_slug( $page_id ) ) { return array(); }
	$ids = array_slice( project_core_fcc_regimen_ids( get_post_meta( $page_id, 'fcc_' . $regimens[ $concern ]['name'], true ) ), 0, 6 );
	if ( ! $ids ) { return array(); }
	return get_posts( array( 'post_type' => array( 'fcc_product', 'fcc_combo' ), 'post_status' => 'publish', 'post__in' => $ids, 'posts_per_page' => count( $ids ), 'orderby' => 'post__in', 'no_found_rows' => true ) );
}

function project_core_fcc_register_regimen_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) { return; }
	$fields = array(
		project_core_fcc_field( 'regimen_help', 'Chọn sản phẩm theo phác đồ', 'message', array( 'name' => '', 'message' => 'Mỗi tab tương ứng một tình trạng da trên trang Vấn đề làn da. Chọn sản phẩm hoặc combo đã xuất bản ở bên trái, kéo các mục bên phải để đổi thứ tự; bấm dấu trừ để bỏ chọn. Tối đa 6 mục mỗi tình trạng. Tên, ảnh, công dụng và giá lấy trực tiếp từ catalog. Lưu ở đây chỉ cập nhật website này, không đưa lựa chọn vào dữ liệu mặc định khởi tạo. Danh sách đề xuất đang chờ chủ nội dung duyệt; không thay thế hướng dẫn sử dụng từng sản phẩm.', 'esc_html' => 1 ) ),
	);
	foreach ( project_core_fcc_regimens() as $regimen ) {
		$fields[] = project_core_fcc_field( $regimen['name'] . '_tab', $regimen['label'], 'tab', array( 'name' => '' ) );
		$fields[] = project_core_fcc_field( $regimen['name'], 'Sản phẩm / combo gợi ý', 'relationship', array( 'post_type' => array( 'fcc_product', 'fcc_combo' ), 'return_format' => 'id', 'filters' => array( 'search', 'post_type' ), 'max' => 6, 'instructions' => 'Chọn theo công dụng và đối tượng phù hợp trong hồ sơ sản phẩm. Thứ tự đã chọn là thứ tự hiển thị. Có thể để trống để không hiển thị sản phẩm cho tình trạng này.' ) );
	}
	acf_add_local_field_group( array( 'key' => 'group_fcc_regimens', 'title' => 'Gợi ý phác đồ – sản phẩm theo tình trạng da', 'fields' => $fields, 'location' => array( array( array( 'param' => 'page_template', 'operator' => '==', 'value' => 'templates/fractional-concerns.php' ) ) ), 'position' => 'normal', 'show_in_rest' => 0 ) );
}
add_action( 'acf/init', 'project_core_fcc_register_regimen_fields' );

function project_core_fcc_regimen_query( $args ) {
	$args['post_status'] = 'publish';
	return $args;
}

function project_core_fcc_validate_regimen( $valid, $value ) {
	if ( true !== $valid || empty( $value ) ) { return $valid; }
	if ( ! is_array( $value ) || count( $value ) > 6 ) { return 'Chọn tối đa 6 sản phẩm hoặc combo cho mỗi tình trạng da.'; }
	if ( count( project_core_fcc_regimen_ids( $value ) ) !== count( $value ) ) { return 'Chỉ chọn sản phẩm hoặc combo đã xuất bản, không chọn trùng.'; }
	return $valid;
}

function project_core_fcc_update_regimen( $value ) {
	return array_slice( project_core_fcc_regimen_ids( $value ), 0, 6 );
}

foreach ( project_core_fcc_regimens() as $regimen ) {
	add_filter( 'acf/fields/relationship/query/key=field_fcc_' . $regimen['name'], 'project_core_fcc_regimen_query' );
	add_filter( 'acf/validate_value/key=field_fcc_' . $regimen['name'], 'project_core_fcc_validate_regimen', 10, 2 );
	add_filter( 'acf/update_value/key=field_fcc_' . $regimen['name'], 'project_core_fcc_update_regimen' );
}
