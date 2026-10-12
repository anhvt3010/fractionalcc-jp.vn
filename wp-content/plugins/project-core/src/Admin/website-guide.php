<?php
/**
 * Website content administration guide. No custom save endpoint is needed.
 *
 * @package Project_Core
 */

defined( 'ABSPATH' ) || exit;

function project_core_register_website_guide() {
	add_menu_page(
		'Nội dung website',
		'Nội dung website',
		'edit_pages',
		'project-website-content',
		'project_core_render_website_guide',
		'dashicons-welcome-write-blog',
		21
	);
}
add_action( 'admin_menu', 'project_core_register_website_guide' );

function project_core_render_website_guide() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		wp_die( esc_html__( 'Bạn không có quyền truy cập trang này.', 'project-core' ) );
	}

	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => array( 'publish', 'draft', 'private' ),
			'posts_per_page' => 1,
			'meta_key'       => '_project_core_sample_page',
			'meta_value'     => '1',
		)
	);
	?>
	<div class="wrap">
		<h1><?php echo esc_html__( 'Nội dung website', 'project-core' ); ?></h1>
		<p><?php echo esc_html__( 'Bắt đầu bằng trang mẫu. Bạn có thể đổi chữ, ảnh và thông tin liên hệ mà không cần sửa code.', 'project-core' ); ?></p>
		<?php if ( ! function_exists( 'get_field' ) ) : ?>
			<div class="notice notice-warning inline"><p><?php echo esc_html__( 'ACF chưa hoạt động. Nhờ quản trị viên kích hoạt Advanced Custom Fields để chỉnh sửa các trường nội dung.', 'project-core' ); ?></p></div>
		<?php endif; ?>
		<?php if ( 'project-child' !== get_stylesheet() ) : ?>
			<div class="notice notice-warning inline"><p><?php echo esc_html__( 'Trang mẫu cần theme Project Child. Nhờ quản trị viên kích hoạt theme này trong mục Giao diện.', 'project-core' ); ?></p></div>
		<?php endif; ?>
		<?php if ( $pages && current_user_can( 'edit_post', $pages[0]->ID ) ) : ?>
			<p>
				<a class="button button-primary" href="<?php echo esc_url( get_edit_post_link( $pages[0]->ID, 'raw' ) ); ?>"><?php echo esc_html__( 'Sửa trang mẫu', 'project-core' ); ?></a>
				<a class="button" href="<?php echo esc_url( get_permalink( $pages[0]->ID ) ); ?>"><?php echo esc_html__( 'Xem trang mẫu', 'project-core' ); ?></a>
			</p>
		<?php else : ?>
			<p><a class="button" href="<?php echo esc_url( admin_url( 'edit.php?post_type=page' ) ); ?>"><?php echo esc_html__( 'Mở danh sách trang', 'project-core' ); ?></a></p>
		<?php endif; ?>
		<h2><?php echo esc_html__( 'Cách chỉnh sửa', 'project-core' ); ?></h2>
		<ol>
			<li><?php echo esc_html__( 'Nhấn Sửa trang mẫu. Cuộn xuống nhóm Nội dung trang giới thiệu.', 'project-core' ); ?></li>
			<li><?php echo esc_html__( 'Chọn tab Mở đầu, Giới thiệu hoặc Liên hệ rồi sửa các trường có nhãn tiếng Việt.', 'project-core' ); ?></li>
			<li><?php echo esc_html__( 'Phần soạn thảo Gutenberg phía trên dành cho nội dung bổ sung như đoạn văn, danh sách và ảnh.', 'project-core' ); ?></li>
			<li><?php echo esc_html__( 'Nhấn Lưu/Cập nhật, sau đó Xem trang để kiểm tra kết quả.', 'project-core' ); ?></li>
		</ol>
		<p><?php echo esc_html__( 'Địa chỉ hello@example.com chỉ là dữ liệu mẫu. Hãy thay bằng email của bạn trước khi dùng thực tế.', 'project-core' ); ?></p>
		<p><?php echo esc_html__( 'Không cần chỉnh sửa nhóm field ACF, PHP, CSS hoặc trình sửa file theme/plugin. Cấu trúc trang đã được lập trình sẵn.', 'project-core' ); ?></p>
	</div>
	<?php
}
