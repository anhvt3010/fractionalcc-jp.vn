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
	// Native ACF tabs and column widths keep the editor compact without custom styling.
	$product = array(
		project_core_fcc_field( 'product_tab_basic', 'Thông tin & giá', 'tab', array( 'name' => '', 'selected' => 1 ) ),
		project_core_fcc_field( 'shortName', 'Tên ngắn', 'text', array( 'wrapper' => array( 'width' => 50 ), 'instructions' => 'Tên gọn dùng trong danh sách chọn sản phẩm khi đặt hàng. Tên đầy đủ nhập ở tiêu đề bên trên.' ) ),
		project_core_fcc_field( 'salePrice', 'Giá bán (VNĐ)', 'number', array( 'wrapper' => array( 'width' => 25 ), 'min' => 0, 'step' => 1, 'instructions' => 'Nhập số tiền không có dấu phân cách. Chưa có giá thì để trống, không dùng 0 thay thế.' ) ),
		project_core_fcc_field( 'originalPrice', 'Giá gốc (VNĐ)', 'number', array( 'wrapper' => array( 'width' => 25 ), 'min' => 0, 'step' => 1, 'instructions' => 'Chỉ nhập giá gốc đã xác nhận; để trống nếu chưa có.' ) ),
		project_core_fcc_field( 'brand', 'Thương hiệu', 'text', array( 'wrapper' => array( 'width' => 25 ), 'instructions' => 'Ghi theo nhãn hoặc hồ sơ sản phẩm.' ) ),
		// A distinct ACF key avoids inheriting the site field layout; the stored meta name stays fcc_origin.
		project_core_fcc_field( 'origin', 'Xuất xứ', 'text', array( 'key' => 'field_fcc_product_origin', 'wrapper' => array( 'width' => 25 ), 'instructions' => 'Quốc gia xuất xứ theo hồ sơ.' ) ),
		project_core_fcc_field( 'volume', 'Quy cách', 'text', array( 'wrapper' => array( 'width' => 25 ), 'instructions' => 'Dung tích, khối lượng hoặc số lượng theo bao bì; ghi kèm đơn vị.' ) ),
		project_core_fcc_field( 'category', 'Phân loại theo hồ sơ', 'text', array( 'wrapper' => array( 'width' => 25 ), 'instructions' => 'Phân loại ghi trong hồ sơ. Bộ lọc website chọn ở tab Bộ lọc.' ) ),
		project_core_fcc_field( 'tagline', 'Công dụng / mô tả ngắn', 'text', array( 'instructions' => 'Tóm tắt công dụng theo hồ sơ; hiển thị trên thẻ và trang chi tiết sản phẩm.' ) ),
		project_core_fcc_field( 'image', 'Ảnh sản phẩm chính', 'image', array( 'return_format' => 'id', 'preview_size' => 'medium', 'mime_types' => 'jpg,jpeg,png,webp', 'instructions' => 'Ảnh dùng trên thẻ và trang sản phẩm. Thêm các ảnh chi tiết bằng block Thư viện ảnh trong trình soạn thảo bên trên.' ) ),
		project_core_fcc_field( 'product_tab_content', 'Công dụng & cách dùng', 'tab', array( 'name' => '' ) ),
	);
	foreach ( array(
		'uspBullets' => array( 'Điểm nổi bật', 'Mỗi điểm nổi bật một dòng, theo nội dung đã duyệt.' ),
		'targetUsers' => array( 'Đối tượng phù hợp', 'Mỗi đối tượng hoặc nhu cầu phù hợp một dòng, theo hồ sơ.' ),
		'howToUse' => array( 'Hướng dẫn sử dụng', 'Mỗi bước một dòng, đúng thứ tự hướng dẫn trên bao bì hoặc hồ sơ.' ),
		'notes' => array( 'Lưu ý', 'Ghi lưu ý sử dụng theo hồ sơ; xuống dòng để dễ đọc.' ),
	) as $name => $definition ) {
		$product[] = project_core_fcc_field( $name, $definition[0], 'textarea', array( 'wrapper' => array( 'width' => 50 ), 'rows' => 5, 'instructions' => $definition[1] ) );
	}
	$product[] = project_core_fcc_field( 'product_tab_ingredients', 'Thành phần', 'tab', array( 'name' => '' ) );
	$product[] = project_core_fcc_field( 'spiculeCount', 'Thông tin vi kim theo hồ sơ', 'text', array( 'wrapper' => array( 'width' => 50 ), 'instructions' => 'Ghi đúng thông tin vi kim của sản phẩm; chưa có nguồn thì để trống.' ) );
	$product[] = project_core_fcc_field( 'ingredientsText', 'Thành phần chính – nội dung đầy đủ từ hồ sơ', 'textarea', array( 'rows' => 6, 'instructions' => 'Giữ đầy đủ tên và mô tả thành phần từ hồ sơ. Nội dung này được lưu riêng với các thẻ hoạt chất bên dưới.' ) );
	$product[] = project_core_fcc_field( 'actives', 'Thẻ hoạt chất trên website', 'relationship', array( 'post_type' => array( 'fcc_ingredient' ), 'return_format' => 'id', 'filters' => array( 'search' ), 'instructions' => 'Tìm và chọn hoạt chất ở bên trái; kéo các mục đã chọn ở bên phải để sắp xếp. Sửa nội dung từng thẻ tại mục Hoạt chất.' ) );
	$product[] = project_core_fcc_field( 'product_tab_legal', 'Hồ sơ', 'tab', array( 'name' => '' ) );
	$product[] = project_core_fcc_field( 'cbmp', 'Số CBMP', 'text', array( 'wrapper' => array( 'width' => 50 ), 'instructions' => 'Sao chép đầy đủ số công bố mỹ phẩm từ hồ sơ, gồm dấu gạch và phần chữ.' ) );
	$product[] = project_core_fcc_field( 'legalStatus', 'Trạng thái hồ sơ', 'text', array( 'wrapper' => array( 'width' => 50 ), 'instructions' => 'Ghi tình trạng pháp lý theo hồ sơ đã cung cấp, không tự thêm cam kết.' ) );
	$product[] = project_core_fcc_field( 'product_tab_filters', 'Bộ lọc', 'tab', array( 'name' => '' ) );
	$product[] = project_core_fcc_field( 'categoryType', 'Loại sản phẩm (bộ lọc)', 'select', array( 'wrapper' => array( 'width' => 50 ), 'choices' => project_core_fcc_product_category_choices(), 'instructions' => 'Chọn nhóm để khách lọc sản phẩm trên trang danh mục.' ) );
	$product[] = project_core_fcc_field( 'skinConcerns', 'Nhu cầu (bộ lọc)', 'checkbox', array( 'layout' => 'horizontal', 'choices' => array( 'pore' => 'Lỗ chân lông', 'oil' => 'Da dầu', 'rough' => 'Da sần', 'aging' => 'Lão hóa', 'elasticity' => 'Đàn hồi', 'brightening' => 'Dưỡng sáng', 'dark-spot' => 'Thâm', 'recovery' => 'Phục hồi', 'lip' => 'Chăm sóc môi', 'dry-lip' => 'Môi khô' ), 'instructions' => 'Có thể chọn nhiều nhu cầu phù hợp với hồ sơ; dùng cho bộ lọc trên trang danh mục.' ) );
	$product[] = project_core_fcc_field( 'product_tab_reviews', 'Đánh giá', 'tab', array( 'name' => '' ) );
	$product[] = project_core_fcc_field( 'rating', 'Điểm đánh giá có nguồn', 'number', array( 'wrapper' => array( 'width' => 25 ), 'min' => 0, 'step' => '0.01', 'instructions' => 'Nhập điểm trung bình từ nguồn đánh giá đã xác nhận; chưa có thì để trống.' ) );
	$product[] = project_core_fcc_field( 'reviewCount', 'Số đánh giá có nguồn', 'number', array( 'wrapper' => array( 'width' => 25 ), 'min' => 0, 'step' => 1, 'instructions' => 'Tổng số lượt đánh giá cùng nguồn với điểm trung bình; chưa có thì để trống.' ) );
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
	acf_add_local_field_group( array(
		'key' => 'group_fcc_featured', 'title' => 'Sản phẩm chủ lực trên trang chủ', 'menu_order' => 10,
		'fields' => array(
			project_core_fcc_field( 'featured', 'Sản phẩm chủ lực', 'true_false', array( 'wrapper' => array( 'width' => 75 ), 'ui' => 1, 'instructions' => 'Bật để hiển thị ở mục sản phẩm chủ lực trên trang chủ. Chọn tối đa 2 sản phẩm hoặc combo đã xuất bản; bỏ chọn mục cũ trước khi thay thế.' ) ),
			project_core_fcc_field( 'featured_order', 'Thứ tự hiển thị', 'number', array( 'wrapper' => array( 'width' => 25 ), 'min' => 0, 'step' => 1, 'default_value' => 0, 'instructions' => 'Số nhỏ hơn đứng trước. Chỉ có tác dụng khi bật Sản phẩm chủ lực.' ) ),
		),
		'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'fcc_product' ) ), array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'fcc_combo' ) ) ),
	) );
}
add_action( 'acf/init', 'project_core_fcc_register_fields' );

/** Stack native ACF columns on small screens, retaining the standard admin UI. */
function project_core_fcc_product_editor_styles( $hook ) {
	$screen = get_current_screen();
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) || ! $screen || ! in_array( $screen->post_type, array( 'fcc_product', 'fcc_combo' ), true ) ) { return; }
	$root = dirname( __DIR__, 2 );
	wp_enqueue_style( 'fcc-admin-product-editor', plugins_url( 'assets/admin-product-editor.css', $root . '/project-core.php' ), array(), (string) filemtime( $root . '/assets/admin-product-editor.css' ) );
}
add_action( 'admin_enqueue_scripts', 'project_core_fcc_product_editor_styles' );
