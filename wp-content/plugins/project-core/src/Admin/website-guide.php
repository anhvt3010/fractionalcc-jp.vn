<?php
/**
 * CMS entry point. Editing uses native WordPress permissions, nonces and ACF.
 *
 * @package Project_Core
 */
defined( 'ABSPATH' ) || exit;

function project_core_register_website_guide() {
	add_menu_page( 'Nội dung website', 'Nội dung website', 'edit_pages', 'project-website-content', 'project_core_render_website_guide', 'dashicons-welcome-write-blog', 21 );
}
add_action( 'admin_menu', 'project_core_register_website_guide' );

function project_core_render_website_guide() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		wp_die( esc_html__( 'Bạn không có quyền truy cập trang này.', 'project-core' ), '', array( 'response' => 403 ) );
	}
	$pages = get_option( 'project_core_fcc_pages', array() );
	?>
	<div class="wrap">
		<h1><?php echo esc_html__( 'Nội dung website Fractional CC', 'project-core' ); ?></h1>
		<p><?php echo esc_html__( 'Chỉnh nội dung và ảnh bằng các trường có nhãn; bố cục được giữ theo thiết kế. Sản phẩm, combo và hoạt chất được quản lý riêng để tái sử dụng.', 'project-core' ); ?></p>
		<p><?php echo esc_html__( 'Trong Thông tin chung, dùng ô tìm kiếm hoặc chọn tab theo vị trí: đầu trang, menu, chân trang, biểu mẫu, ảnh đánh giá và nhãn trang sản phẩm. Mỗi phần có hướng dẫn và liên kết xem khu vực tương ứng. Nội dung riêng của trang chủ/sản phẩm được chỉnh ở mục của trang/sản phẩm đó; cách sửa text ngoài giao diện vẫn giữ nguyên.', 'project-core' ); ?></p>
		<?php if ( ! function_exists( 'acf_add_local_field_group' ) || 'project-child' !== get_stylesheet() ) : ?>
			<div class="notice notice-warning inline"><p><?php echo esc_html__( 'Cần kích hoạt ACF và Fractional CC – Project Child.', 'project-core' ); ?></p></div>
		<?php endif; ?>
		<?php if ( $pages && 'approved' !== project_core_fcc_site( 'content_status' ) ) : ?>
			<div class="notice notice-warning inline"><p><?php echo esc_html__( 'Nội dung đang là dữ liệu mẫu local, chờ duyệt trước khi public. Các trang Fractional CC được đặt noindex. Kiểm tra đánh giá, giá gốc, ưu đãi, chính sách và quyền sử dụng media trước khi chọn trạng thái đã duyệt trong Thông tin chung.', 'project-core' ); ?></p></div>
		<?php endif; ?>
		<table class="widefat striped">
			<thead><tr><th><?php echo esc_html__( 'Nội dung', 'project-core' ); ?></th><th><?php echo esc_html__( 'Thao tác', 'project-core' ); ?></th></tr></thead>
			<tbody>
			<?php foreach ( array( 'shared' => 'Thông tin liên hệ, logo và giao diện chung', 'home' => 'Trang chủ', 'catalog' => 'Danh mục sản phẩm', 'concerns' => 'Vấn đề làn da & phác đồ', 'policies' => 'Chính sách', 'thankyou' => 'Trang cảm ơn' ) as $kind => $label ) : ?>
				<?php $id = isset( $pages[ $kind ] ) ? absint( $pages[ $kind ] ) : 0; ?>
				<?php if ( ! $id || ! current_user_can( 'edit_post', $id ) ) { continue; } ?>
				<tr><td><?php echo esc_html( $label ); ?></td><td><a class="button" href="<?php echo esc_url( get_edit_post_link( $id, 'raw' ) ); ?>"><?php echo esc_html__( 'Chỉnh sửa', 'project-core' ); ?></a><?php if ( 'shared' !== $kind ) : ?> <a class="button" href="<?php echo esc_url( get_permalink( $id ) ); ?>"><?php echo esc_html__( 'Xem trang', 'project-core' ); ?></a><?php endif; ?></td></tr>
			<?php endforeach; ?>
			<?php foreach ( array( 'fcc_product' => 'Sản phẩm', 'fcc_combo' => 'Combo', 'fcc_ingredient' => 'Hoạt chất', 'fcc_order' => 'Đơn hàng khách đặt' ) as $type => $label ) : ?>
				<tr><td><?php echo esc_html( $label ); ?></td><td><a class="button" href="<?php echo esc_url( admin_url( 'edit.php?post_type=' . $type ) ); ?>"><?php echo esc_html__( 'Mở danh sách', 'project-core' ); ?></a></td></tr>
			<?php endforeach; ?>
			<?php if ( ! empty( $pages['concerns'] ) && current_user_can( 'edit_post', $pages['concerns'] ) ) : ?>
				<tr><td><strong>Gợi ý phác đồ (Quản lý sản phẩm / combo gợi ý cho từng vấn đề da)</strong></td><td><a class="button button-primary" href="<?php echo esc_url( get_edit_post_link( $pages['concerns'], 'raw' ) ); ?>">Quản lý sản phẩm gợi ý</a></td></tr>
			<?php endif; ?>
			<?php if ( current_user_can( 'edit_pages' ) ) : ?>
				<tr><td><strong>Quản lý liên hệ (Hotline, Chat Zalo, Messenger Facebook, Email)</strong></td><td><a class="button button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=fcc-contact-settings' ) ); ?>">Quản lý liên hệ</a></td></tr>
			<?php endif; ?>
			<?php if ( current_user_can( 'manage_options' ) ) : ?>
				<tr><td><strong>Cài đặt SMTP & Email thông báo</strong></td><td><a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=fcc-smtp-settings' ) ); ?>">Cấu hình SMTP</a></td></tr>
			<?php endif; ?>
			</tbody>
		</table>
		<h2><?php echo esc_html__( 'Cách chỉnh sửa', 'project-core' ); ?></h2>
		<ol>
			<li><?php echo esc_html__( 'Chọn nội dung ở trên. Với trang, mở tab theo section và sửa chữ/ảnh; không cần sửa HTML, CSS hoặc PHP.', 'project-core' ); ?></li>
			<li><?php echo esc_html__( 'Với sản phẩm, mở tab Thông tin & giá để sửa tên ngắn, giá và ảnh chính. Các tab Công dụng & cách dùng, Thành phần, Hồ sơ, Bộ lọc và Đánh giá tách riêng theo nội dung. Mỗi ý trong danh sách là một dòng; để giá trống khi chưa có dữ liệu.', 'project-core' ); ?></li>
			<li><?php echo esc_html__( 'Tại danh sách Sản phẩm, chọn Sửa nhanh để đổi giá bán, giá gốc, tên ngắn, quy cách, loại sản phẩm, xuất xứ, số CBMP, trạng thái hồ sơ và mô tả ngắn. Nhập giá bằng số VNĐ không có dấu phân cách; để trống khi chưa có giá.', 'project-core' ); ?></li>
			<li><?php echo esc_html__( 'Thêm ảnh chi tiết bằng block Thư viện ảnh trong trình soạn thảo sản phẩm. Chọn hoạt chất trong trường liên kết, kéo để sắp xếp.', 'project-core' ); ?></li>
			<li><?php echo esc_html__( 'Để chọn thẻ trong Danh Mục Sản Phẩm & Các Combo Tối Ưu trên trang chủ, mở từng Sản phẩm hoặc Combo, bật Hiển thị trong danh mục trang chủ và nhập thứ tự. Tổng cộng tối đa 6 mục; bỏ chọn một mục trước khi thêm mục mới. Lựa chọn này độc lập với 2 sản phẩm chủ lực.', 'project-core' ); ?></li>
			<li><?php echo esc_html__( 'Nhấn Cập nhật rồi Xem trang. Không thay thiết kế hoặc thêm nội dung chưa được duyệt.', 'project-core' ); ?></li>
			<li><?php echo esc_html__( 'Mở Vấn đề làn da & phác đồ để sửa chữ, chọn ảnh theo từng tình trạng, nguyên tắc, tư vấn và SEO. Nhóm Gợi ý phác đồ quản lý sản phẩm/combo và thứ tự, tối đa 6 mục mỗi tình trạng. Lựa chọn sản phẩm lưu trên site này, chưa đưa vào dữ liệu mặc định khởi tạo.', 'project-core' ); ?></li>
			<li><?php echo esc_html__( 'Trong Trang chủ, nhóm Công nghệ – nội dung chi tiết quản lý ảnh và các đoạn giải thích; nhóm Chu trình – video quản lý liên kết video hướng dẫn. Trong Thông tin chung, nhóm Form đặt hàng quản lý chính sách tóm tắt cạnh form, các nhóm Nhãn và Thông báo quản lý chữ nút, nhãn và thông báo tương tác. Hotline hiển thị và liên kết gọi đều lấy cấu hình Liên hệ dùng chung.', 'project-core' ); ?></li>
			<li><?php echo esc_html__( 'Điểm/số đánh giá sản phẩm được dùng cho cả danh mục và chi tiết; combo có điểm/số đánh giá riêng trong editor Combo. Mỗi trang có ảnh chia sẻ ở tab SEO; favicon nằm trong Thông tin chung. Sản phẩm có nhóm SEO & nhãn ảnh riêng, để trống để dùng dữ liệu sản phẩm.', 'project-core' ); ?></li>
		</ol>
		<?php if ( current_user_can( 'manage_options' ) ) : ?>
		<h2><?php echo esc_html__( 'Khởi tạo dữ liệu mặc định', 'project-core' ); ?></h2>
		<p><?php echo esc_html__( 'Site mới tự tạo dữ liệu mặc định khi kích hoạt Project Child và Project Core, sau khi ACF sẵn sàng. Bộ mặc định gồm 10 sản phẩm và combo C–CX4 theo Excel đã duyệt. Nút bên dưới bổ sung dữ liệu còn thiếu nếu cần.', 'project-core' ); ?></p>
		<p><?php echo esc_html__( 'Chỉ tạo bản ghi/trường còn thiếu; chạy lại giữ nguyên nội dung đã sửa. Không tự đổi URL, homepage hoặc kích hoạt theme.', 'project-core' ); ?></p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="project_core_fcc_setup">
			<?php wp_nonce_field( 'project_core_fcc_setup' ); ?>
			<?php submit_button( $pages ? 'Bổ sung dữ liệu còn thiếu' : 'Khởi tạo nội dung Fractional CC', 'secondary' ); ?>
		</form>
		<?php endif; ?>
		<?php
		$samples = get_posts( array( 'post_type' => 'page', 'post_status' => array( 'publish', 'draft', 'private' ), 'posts_per_page' => 1, 'meta_key' => '_project_core_sample_page', 'meta_value' => '1' ) );
		if ( $samples && current_user_can( 'edit_post', $samples[0]->ID ) ) :
		?>
			<p><a href="<?php echo esc_url( get_edit_post_link( $samples[0]->ID, 'raw' ) ); ?>"><?php echo esc_html__( 'Sửa trang mẫu cũ', 'project-core' ); ?></a></p>
		<?php endif; ?>
	</div>
	<?php
}
