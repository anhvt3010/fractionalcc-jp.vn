<?php
/** Editable domain data using ACF Free. @package Project_Core */
defined( 'ABSPATH' ) || exit;

function project_core_fcc_field( $name, $label, $type = 'text', $options = array() ) {
	return array_merge( array( 'key' => 'field_fcc_' . $name, 'name' => 'fcc_' . $name, 'label' => $label, 'type' => $type ), $options );
}

function project_core_fcc_register_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) { return; }
	$site = array();
	foreach ( array( 'name' => 'Thương hiệu', 'distributor' => 'Đơn vị phân phối', 'distributorRole' => 'Vai trò phân phối', 'slogan' => 'Thông điệp', 'headline' => 'Tiêu đề nhận diện', 'subheadline' => 'Mô tả nhận diện', 'origin' => 'Xuất xứ', 'hotline' => 'Hotline (chỉ số)', 'hotlineDisplay' => 'Hotline hiển thị' ) as $name => $label ) { $site[] = project_core_fcc_field( $name, $label ); }
	$site[] = project_core_fcc_field( 'email', 'Email liên hệ', 'email' );
	$site[] = project_core_fcc_field( 'zaloLink', 'URL Zalo', 'url' );
	$site[] = project_core_fcc_field( 'messengerLink', 'URL Messenger', 'url' );
	$site[] = project_core_fcc_field( 'content_status', 'Trạng thái duyệt nội dung', 'select', array( 'choices' => array( 'local_sample' => 'Dữ liệu mẫu local – chờ duyệt', 'approved' => 'Đã duyệt để sử dụng' ), 'default_value' => 'local_sample', 'instructions' => 'Chỉ chọn đã duyệt khi nội dung, chính sách, đánh giá và media đã được xác nhận.' ) );
	$product = array();
	foreach ( array( 'shortName' => 'Tên ngắn', 'category' => 'Nhóm hiển thị', 'tagline' => 'Mô tả ngắn', 'spiculeCount' => 'Thông tin vi kim theo hồ sơ', 'volume' => 'Quy cách', 'cbmp' => 'Số CBMP', 'legalStatus' => 'Trạng thái hồ sơ', 'origin' => 'Xuất xứ' ) as $name => $label ) { $product[] = project_core_fcc_field( $name, $label ); }
	$product[] = project_core_fcc_field( 'categoryType', 'Loại sản phẩm (bộ lọc)', 'select', array( 'choices' => array( 'peel' => 'Tẩy tế bào chết', 'serum' => 'Serum', 'mask' => 'Mặt nạ', 'lip' => 'Dưỡng môi' ) ) );
	$product[] = project_core_fcc_field( 'skinConcerns', 'Nhu cầu (bộ lọc)', 'checkbox', array( 'choices' => array( 'pore' => 'Lỗ chân lông', 'oil' => 'Da dầu', 'rough' => 'Da sần', 'aging' => 'Lão hóa', 'elasticity' => 'Đàn hồi', 'brightening' => 'Dưỡng sáng', 'dark-spot' => 'Thâm', 'recovery' => 'Phục hồi', 'lip' => 'Chăm sóc môi', 'dry-lip' => 'Môi khô' ) ) );
	foreach ( array( 'salePrice' => 'Giá bán (VNĐ)', 'originalPrice' => 'Giá gốc đã được duyệt (VNĐ)', 'rating' => 'Điểm đánh giá có nguồn', 'reviewCount' => 'Số đánh giá có nguồn' ) as $name => $label ) { $product[] = project_core_fcc_field( $name, $label, 'number', array( 'min' => 0, 'step' => 'rating' === $name ? '0.01' : '1', 'instructions' => 'Để trống nếu chưa có dữ liệu; không nhập số 0 để thay giá chưa có.' ) ); }
	foreach ( array( 'uspBullets' => 'Điểm nổi bật', 'targetUsers' => 'Đối tượng phù hợp', 'howToUse' => 'Hướng dẫn sử dụng', 'notes' => 'Lưu ý' ) as $name => $label ) { $product[] = project_core_fcc_field( $name, $label, 'textarea', array( 'rows' => 4, 'instructions' => 'Mỗi ý một dòng. Chỉ dùng nội dung đã được duyệt.' ) ); }
	$product[] = project_core_fcc_field( 'image', 'Ảnh sản phẩm', 'image', array( 'return_format' => 'id', 'preview_size' => 'medium', 'mime_types' => 'jpg,jpeg,png,webp' ) );
	$product[] = project_core_fcc_field( 'actives', 'Hoạt chất (kéo để sắp xếp)', 'relationship', array( 'post_type' => array( 'fcc_ingredient' ), 'return_format' => 'id', 'filters' => array( 'search' ) ) );
	$combo = array();
	foreach ( array( 'code' => 'Mã combo', 'shortName' => 'Tên ngắn', 'badge' => 'Nhãn hiển thị', 'idealFor' => 'Nhu cầu phù hợp', 'routine' => 'Hướng dẫn', 'gift' => 'Quà tặng đã xác nhận' ) as $name => $label ) { $combo[] = project_core_fcc_field( 'combo_' . $name, $label ); $combo[ count( $combo ) - 1 ]['name'] = 'fcc_' . $name; }
	foreach ( array( 'originalPrice' => 'Giá gốc (VNĐ)', 'salePrice' => 'Giá combo (VNĐ)' ) as $name => $label ) { $combo[] = project_core_fcc_field( 'combo_' . $name, $label, 'number', array( 'min' => 0 ) ); $combo[ count( $combo ) - 1 ]['name'] = 'fcc_' . $name; }
	$combo[] = project_core_fcc_field( 'combo_productsIncluded', 'Mô tả sản phẩm bên trong', 'textarea', array( 'instructions' => 'Mỗi ý một dòng.' ) ); $combo[ count( $combo ) - 1 ]['name'] = 'fcc_productsIncluded';
	$combo[] = project_core_fcc_field( 'combo_productIds', 'Sản phẩm trong combo', 'relationship', array( 'post_type' => array( 'fcc_product' ), 'return_format' => 'id' ) ); $combo[ count( $combo ) - 1 ]['name'] = 'fcc_productIds';
	$combo[] = project_core_fcc_field( 'combo_image', 'Ảnh combo', 'image', array( 'return_format' => 'id', 'preview_size' => 'medium' ) ); $combo[ count( $combo ) - 1 ]['name'] = 'fcc_image';
	$combo[] = project_core_fcc_field( 'combo_isBestSeller', 'Hiện nhãn bán chạy (cần có nguồn)', 'true_false', array( 'ui' => 1 ) ); $combo[ count( $combo ) - 1 ]['name'] = 'fcc_isBestSeller';
	$groups = array( 'fcc_site' => array( 'Thông tin Fractional CC', $site ), 'fcc_product' => array( 'Thông tin sản phẩm', $product ), 'fcc_combo' => array( 'Thông tin combo', $combo ), 'fcc_ingredient' => array( 'Thông tin hoạt chất', array( project_core_fcc_field( 'tag', 'Nhóm / nhãn'), project_core_fcc_field( 'description', 'Mô tả theo hồ sơ', 'textarea', array( 'rows' => 5 ) ) ) ) );
	foreach ( $groups as $type => $group ) {
		acf_add_local_field_group( array( 'key' => 'group_' . $type, 'title' => $group[0], 'fields' => $group[1], 'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => $type ) ) ), 'show_in_rest' => 0 ) );
	}
}
add_action( 'acf/init', 'project_core_fcc_register_fields' );
